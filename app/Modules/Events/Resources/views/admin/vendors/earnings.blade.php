@extends('layouts.admin')

@section('title', 'Vendor Earnings - ' . $vendor->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.events.vendors.show', $vendor) }}" class="text-gray-600 hover:text-gray-900">
                &larr; Back to Vendor
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $vendor->name }} - Earnings Report</h1>
        </div>
        <div class="flex gap-2">
            <button onclick="exportReport()" 
                    class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                Export Report
            </button>
        </div>
    </div>

    <!-- Date Range Filter -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="start_date" value="{{ $startDate instanceof \Carbon\Carbon ? $startDate->format('Y-m-d') : $startDate }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="end_date" value="{{ $endDate instanceof \Carbon\Carbon ? $endDate->format('Y-m-d') : $endDate }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Update Report
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Total Earnings</div>
            <div class="text-3xl font-bold text-green-600">${{ number_format($totalEarnings, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Total Bookings</div>
            <div class="text-3xl font-bold text-blue-600">{{ $totalBookings }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Average Booking Value</div>
            <div class="text-3xl font-bold text-purple-600">
                ${{ $totalBookings > 0 ? number_format($totalEarnings / $totalBookings, 2) : '0.00' }}
            </div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Period</div>
            <div class="text-lg font-semibold text-gray-800">
                {{ \Carbon\Carbon::parse($startDate)->format('M d') }} - 
                {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
            </div>
        </div>
    </div>

    <!-- Earnings Chart -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-bold mb-4">Daily Earnings</h2>
        <canvas id="earningsChart" height="100"></canvas>
    </div>

    <!-- Earnings Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bookings</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Earnings</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Avg. Per Booking</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($earnings as $earning)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">
                            {{ \Carbon\Carbon::parse($earning->date)->format('F d, Y') }}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $earning->total_bookings }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-green-600">${{ number_format($earning->total_earnings, 2) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">
                            ${{ $earning->total_bookings > 0 ? number_format($earning->total_earnings / $earning->total_bookings, 2) : '0.00' }}
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                        No earnings data found for this period
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">Totals</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900">{{ $totalBookings }}</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-green-600">${{ number_format($totalEarnings, 2) }}</th>
                    <th class="px-6 py-3 text-left text-sm font-semibold text-gray-900"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('earningsChart').getContext('2d');
        
        const dates = {!! json_encode($earnings->pluck('date')->map(function($date) {
            return \Carbon\Carbon::parse($date)->format('M d');
        })) !!};
        
        const earnings = {!! json_encode($earnings->pluck('total_earnings')) !!};
        const bookings = {!! json_encode($earnings->pluck('total_bookings')) !!};

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Earnings ($)',
                    data: earnings,
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    yAxisID: 'y',
                    tension: 0.4
                }, {
                    label: 'Bookings',
                    data: bookings,
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    yAxisID: 'y1',
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        title: {
                            display: true,
                            text: 'Earnings ($)'
                        }
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Bookings'
                        },
                        grid: {
                            drawOnChartArea: false,
                        },
                    },
                }
            }
        });
    });

    function exportReport() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = `{{ route('admin.events.vendors.earnings', $vendor) }}/export?${params.toString()}`;
    }
</script>
@endpush
@endsection