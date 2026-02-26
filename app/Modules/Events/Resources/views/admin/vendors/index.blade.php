@extends('layouts.admin')

@section('title', 'Vendor Management - EventFlow Admin')

@section('content')
<!-- Header -->
<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Vendors</h1>
        <p class="text-sm text-gray-600 mt-1">Manage and monitor vendor accounts</p>
    </div>
    <div class="flex gap-3">
        @if(isset($vendors) && $vendors->count())
            <a href="{{ route('admin.events.vendors.earnings', $vendors->first()) }}" 
               class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Earnings Report
            </a>
        @else
            <button class="inline-flex items-center px-4 py-2 bg-blue-400 text-white text-sm font-medium rounded-lg opacity-50 cursor-not-allowed" disabled>
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Earnings Report
            </button>
        @endif
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gray-50 rounded-lg p-6">
        <p class="text-sm text-gray-500 mb-1">Total Vendors</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-gray-900">{{ $vendors->total() }}</p>
            <span class="text-xs text-gray-500 bg-white px-2 py-1 rounded-full">All time</span>
        </div>
    </div>
    
    <div class="bg-gray-50 rounded-lg p-6">
        <p class="text-sm text-gray-500 mb-1">Active</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-green-600">{{ $vendors->where('is_active', true)->count() }}</p>
            <span class="text-xs text-green-600 bg-green-50 px-2 py-1 rounded-full">Active</span>
        </div>
    </div>
    
    <div class="bg-gray-50 rounded-lg p-6">
        <p class="text-sm text-gray-500 mb-1">Pending</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-yellow-600">{{ $vendors->where('vendor_approved_at', null)->count() }}</p>
            <span class="text-xs text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full">Awaiting</span>
        </div>
    </div>
    
    <div class="bg-gray-50 rounded-lg p-6">
        <p class="text-sm text-gray-500 mb-1">Suspended</p>
        <div class="flex items-end justify-between">
            <p class="text-3xl font-bold text-red-600">{{ $vendors->where('is_active', false)->count() }}</p>
            <span class="text-xs text-red-600 bg-red-50 px-2 py-1 rounded-full">Restricted</span>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <form method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Name or email..."
                           class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" 
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Sort By</label>
                <select name="sort_by" 
                        class="w-full rounded-lg border border-gray-300 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition">
                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Registration Date</option>
                    <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                    <option value="events_count" {{ request('sort_by') == 'events_count' ? 'selected' : '' }}>Event Count</option>
                </select>
            </div>
            
            <div class="flex items-end gap-2">
                <button type="submit" 
                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition duration-150">
                    Apply Filters
                </button>
                <a href="{{ route('admin.events.vendors.index') }}" 
                   class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition duration-150">
                    Clear
                </a>
            </div>
        </div>
    </form>
</div>

<!-- Vendors Table -->
<div class="bg-white rounded-lg shadow-sm overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Events</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($vendors as $vendor)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-600 to-purple-600 flex items-center justify-center text-white font-semibold text-sm">
                            {{ substr($vendor->name, 0, 1) }}
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-gray-900">{{ $vendor->name }}</div>
                            <div class="text-xs text-gray-500">ID: #{{ $vendor->id }}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm text-gray-900">{{ $vendor->email }}</div>
                    <div class="text-xs text-gray-500">{{ $vendor->phone ?? 'No phone' }}</div>
                </td>
                <td class="px-6 py-4">
                    @if(!$vendor->is_active)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Suspended
                        </span>
                    @elseif(!$vendor->vendor_approved_at)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4">
                    <div class="text-sm font-medium text-gray-900">{{ $vendor->events_count ?? 0 }}</div>
                    <div class="text-xs text-gray-500">{{ $vendor->published_events_count ?? 0 }} published</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    {{ $vendor->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.events.vendors.show', $vendor) }}" 
                           class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                            View
                        </a>
                        
                        @if(!$vendor->vendor_approved_at && $vendor->is_active)
                            <form action="{{ route('admin.events.vendors.approve', $vendor) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-green-600 hover:text-green-900 text-sm font-medium">
                                    Approve
                                </button>
                            </form>
                        @endif
                        
                        @if($vendor->is_active && $vendor->vendor_approved_at)
                            <button onclick="showSuspendModal({{ $vendor->id }})" 
                                    class="text-red-600 hover:text-red-900 text-sm font-medium">
                                Suspend
                            </button>
                        @endif
                        
                        @if(!$vendor->is_active)
                            <form action="{{ route('admin.events.vendors.reactivate', $vendor) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        class="text-green-600 hover:text-green-900 text-sm font-medium">
                                    Reactivate
                                </button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No vendors found</h3>
                    <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-6">
    {{ $vendors->withQueryString()->links() }}
</div>

<!-- Suspend Modal -->
<div id="suspendModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 hidden items-center justify-center z-50" x-cloak>
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4" @click.away="hideSuspendModal()">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Suspend Vendor</h3>
            <form id="suspendForm" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Reason for suspension
                    </label>
                    <textarea name="reason" 
                              rows="3" 
                              required
                              class="w-full rounded-lg border border-gray-300 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition"
                              placeholder="Please provide a reason..."></textarea>
                </div>
                
                <div class="mb-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               name="cancel_events" 
                               value="1"
                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-gray-700">
                            Also cancel all upcoming events
                        </span>
                    </label>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" 
                            onclick="hideSuspendModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button type="submit"
                            class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition">
                        Suspend Vendor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function showSuspendModal(vendorId) {
        document.getElementById('suspendModal').classList.remove('hidden');
        document.getElementById('suspendModal').classList.add('flex');
        document.getElementById('suspendForm').action = `/admin/events/vendors/${vendorId}/suspend`;
    }

    function hideSuspendModal() {
        document.getElementById('suspendModal').classList.add('hidden');
        document.getElementById('suspendModal').classList.remove('flex');
    }
</script>
@endpush