@extends('layouts.admin')

@section('title', 'Vendor Events - ' . $vendor->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.events.vendors.show', $vendor) }}" class="text-gray-600 hover:text-gray-900">
                &larr; Back to Vendor
            </a>
            <h1 class="text-3xl font-bold text-gray-900">{{ $vendor->name }}'s Events</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.events.vendors.earnings', $vendor) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                View Earnings
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Total Events</div>
            <div class="text-3xl font-bold">{{ $events->total() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Published</div>
            <div class="text-3xl font-bold text-green-600">{{ $events->where('status', 'published')->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Draft</div>
            <div class="text-3xl font-bold text-gray-600">{{ $events->where('status', 'draft')->count() }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Cancelled</div>
            <div class="text-3xl font-bold text-red-600">{{ $events->where('status', 'cancelled')->count() }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Event title..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">All</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition">
                    Filter
                </button>
                <a href="{{ route('admin.events.vendors.events', $vendor) }}" 
                   class="px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition">
                    Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Events Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Location</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Attendees</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($events as $event)
                <tr>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $event->title }}</div>
                        <div class="text-sm text-gray-500">ID: {{ $event->id }}</div>
                        @if($event->is_featured)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800 mt-1">
                                Featured
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $event->start_date->format('M d, Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $event->start_date->format('g:i A') }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $event->venue }}</div>
                        <div class="text-sm text-gray-500">{{ $event->city }}, {{ $event->country }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900">{{ $event->current_attendees }}/{{ $event->max_attendees ?? '∞' }}</div>
                        <div class="text-sm text-gray-500">{{ $event->bookings_count ?? 0 }} bookings</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">${{ number_format($event->current_attendees * $event->price, 2) }}</div>
                        <div class="text-sm text-gray-500">{{ $event->formatted_price }} per ticket</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded-full 
                            @if($event->status === 'published') bg-green-100 text-green-800
                            @elseif($event->status === 'draft') bg-gray-100 text-gray-800
                            @elseif($event->status === 'cancelled') bg-red-100 text-red-800
                            @else bg-blue-100 text-blue-800
                            @endif">
                            {{ ucfirst($event->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm font-medium">
                        <div class="flex gap-2">
                            <a href="{{ route('events.show', $event) }}" target="_blank"
                               class="text-blue-600 hover:text-blue-900">View</a>
                            <a href="{{ route('events.edit', $event) }}" 
                               class="text-indigo-600 hover:text-indigo-900">Edit</a>
                            <button onclick="showEventStats({{ $event->id }})"
                                    class="text-green-600 hover:text-green-900">Stats</button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                        No events found for this vendor
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $events->withQueryString()->links() }}
    </div>
</div>

<!-- Event Stats Modal -->
<div id="eventStatsModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold" id="modalEventTitle">Event Statistics</h3>
            <button onclick="hideEventStats()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="statsContent" class="space-y-6">
            <!-- Stats will be loaded via AJAX -->
            <div class="text-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto"></div>
                <p class="mt-4 text-gray-600">Loading statistics...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function showEventStats(eventId) {
        document.getElementById('eventStatsModal').classList.remove('hidden');
        document.getElementById('eventStatsModal').classList.add('flex');
        
        // Load event stats via AJAX
        fetch(`/admin/events/stats/${eventId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('statsContent').innerHTML = data.html;
                document.getElementById('modalEventTitle').textContent = data.title;
            });
    }

    function hideEventStats() {
        document.getElementById('eventStatsModal').classList.add('hidden');
        document.getElementById('eventStatsModal').classList.remove('flex');
    }
</script>
@endpush
@endsection