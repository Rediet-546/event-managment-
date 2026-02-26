@extends('layouts.admin')

@section('title', 'Vendor Details - ' . $vendor->name)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-8">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.events.vendors.index') }}" class="text-gray-600 hover:text-gray-900">
                &larr; Back to Vendors
            </a>
            <h1 class="text-3xl font-bold text-gray-900">Vendor Details</h1>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.events.vendors.events', $vendor) }}" 
               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                View All Events
            </a>
            <a href="{{ route('admin.events.vendors.earnings', $vendor) }}" 
               class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition">
                Earnings Report
            </a>
        </div>
    </div>

    <!-- Vendor Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Total Events</div>
            <div class="text-3xl font-bold">{{ $stats['total_events'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Published Events</div>
            <div class="text-3xl font-bold text-green-600">{{ $stats['published_events'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Total Bookings</div>
            <div class="text-3xl font-bold text-blue-600">{{ $stats['total_bookings'] }}</div>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <div class="text-sm text-gray-500 mb-1">Total Revenue</div>
            <div class="text-3xl font-bold text-purple-600">${{ number_format($stats['total_revenue'], 2) }}</div>
        </div>
    </div>

    <!-- Vendor Details -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column - Vendor Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center mb-6">
                    <div class="h-20 w-20 rounded-full bg-gray-200 flex items-center justify-center">
                        <span class="text-3xl font-medium text-gray-600">{{ substr($vendor->name, 0, 1) }}</span>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-2xl font-bold">{{ $vendor->name }}</h2>
                        <p class="text-gray-600">{{ $vendor->email }}</p>
                        <p class="text-gray-600">{{ $vendor->phone ?? 'No phone' }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Status:</span>
                        <span>
                            @if(!$vendor->is_active)
                                <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Suspended</span>
                            @elseif(!$vendor->vendor_approved_at)
                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Active</span>
                            @endif
                        </span>
                    </div>

                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Member Since:</span>
                        <span>{{ $vendor->created_at->format('F d, Y') }}</span>
                    </div>

                    @if($vendor->vendor_approved_at)
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Approved On:</span>
                        <span>{{ $vendor->vendor_approved_at->format('F d, Y') }}</span>
                    </div>
                    @endif

                    @if($vendor->suspended_at)
                    <div class="flex justify-between py-2 border-b">
                        <span class="text-gray-600">Suspended On:</span>
                        <span>{{ $vendor->suspended_at->format('F d, Y') }}</span>
                    </div>
                    <div class="py-2">
                        <span class="text-gray-600">Suspension Reason:</span>
                        <p class="mt-1 text-sm text-red-600 bg-red-50 p-2 rounded">{{ $vendor->suspension_reason }}</p>
                    </div>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 space-y-2">
                    @if(!$vendor->vendor_approved_at && $vendor->is_active)
                        <form action="{{ route('admin.events.vendors.approve', $vendor) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                                Approve Vendor
                            </button>
                        </form>
                    @endif
                    
                    @if($vendor->is_active && $vendor->vendor_approved_at)
                        <button onclick="showSuspendModal()"
                                class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition">
                            Suspend Vendor
                        </button>
                    @endif
                    
                    @if(!$vendor->is_active)
                        <form action="{{ route('admin.events.vendors.reactivate', $vendor) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="w-full bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition">
                                Reactivate Vendor
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column - Recent Events -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-bold mb-4">Recent Events</h3>
                
                @if($recentEvents->isEmpty())
                    <p class="text-gray-500 text-center py-8">No events created yet</p>
                @else
                    <div class="space-y-4">
                        @foreach($recentEvents as $event)
                            <div class="border rounded-lg p-4 hover:shadow-md transition">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h4 class="font-semibold text-lg">{{ $event->title }}</h4>
                                        <div class="text-sm text-gray-600 mt-1">
                                            <span>{{ $event->start_date->format('M d, Y g:i A') }}</span>
                                            <span class="mx-2">•</span>
                                            <span>{{ $event->venue }}, {{ $event->city }}</span>
                                        </div>
                                    </div>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                        @if($event->status === 'published') bg-green-100 text-green-800
                                        @elseif($event->status === 'draft') bg-gray-100 text-gray-800
                                        @else bg-red-100 text-red-800
                                        @endif">
                                        {{ ucfirst($event->status) }}
                                    </span>
                                </div>
                                <div class="mt-3 flex gap-4 text-sm">
                                    <span>🎫 {{ $event->current_attendees }}/{{ $event->max_attendees ?? '∞' }}</span>
                                    <span>💰 ${{ number_format($event->price, 2) }}</span>
                                    <span>👁️ {{ $event->views }} views</span>
                                </div>
                                <div class="mt-3 flex gap-2">
                                    <a href="{{ route('events.show', $event) }}" 
                                       class="text-blue-600 hover:text-blue-900 text-sm">View</a>
                                    <a href="{{ route('events.edit', $event) }}" 
                                       class="text-gray-600 hover:text-gray-900 text-sm">Edit</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Suspend Modal (same as index) -->
<div id="suspendModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-8 max-w-md w-full">
        <h3 class="text-xl font-bold mb-4">Suspend Vendor</h3>
        <form id="suspendForm" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Reason for suspension</label>
                <textarea name="reason" rows="3" required
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
            </div>
            <div class="mb-4">
                <label class="flex items-center">
                    <input type="checkbox" name="cancel_events" value="1"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">Also cancel all upcoming events</span>
                </label>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="hideSuspendModal()"
                        class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                    Suspend Vendor
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    function showSuspendModal() {
        document.getElementById('suspendModal').classList.remove('hidden');
        document.getElementById('suspendModal').classList.add('flex');
        document.getElementById('suspendForm').action = "{{ route('admin.events.vendors.suspend', $vendor) }}";
    }

    function hideSuspendModal() {
        document.getElementById('suspendModal').classList.add('hidden');
        document.getElementById('suspendModal').classList.remove('flex');
    }
</script>
@endpush
@endsection