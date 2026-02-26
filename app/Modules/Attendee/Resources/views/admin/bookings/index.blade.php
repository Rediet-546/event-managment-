@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Bookings Management')

@section('breadcrumb')
    <li class="breadcrumb-item active">Bookings</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">All Bookings</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.attendee.bookings.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Booking
                    </a>
                    <a href="{{ route('admin.attendee.bookings.export', request()->all()) }}" class="btn btn-success btn-sm">
                        <i class="fas fa-download"></i> Export
                    </a>
                    <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#filterModal">
                        <i class="fas fa-filter"></i> Filters
                    </button>
                </div>
            </div>
            
            <div class="card-body">
                <!-- Bulk Actions -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="btn-group">
                            <button type="button" class="btn btn-secondary" id="selectAll">
                                <i class="fas fa-check-square"></i> Select All
                            </button>
                            <button type="button" class="btn btn-info" id="bulkExport">
                                <i class="fas fa-download"></i> Export Selected
                            </button>
                            <button type="button" class="btn btn-success" id="bulkCheckin">
                                <i class="fas fa-user-check"></i> Check-in Selected
                            </button>
                            <button type="button" class="btn btn-warning" id="bulkCancel">
                                <i class="fas fa-times-circle"></i> Cancel Selected
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <form method="GET" class="form-inline float-right">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search bookings..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Bookings Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th width="30px">
                                    <input type="checkbox" id="checkAll">
                                </th>
                                <th>Booking #</th>
                                <th>Event</th>
                                <th>Customer</th>
                                <th>Ticket Type</th>
                                <th>Qty</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th>Date</th>
                                <th>Check-in</th>
                                <th width="150px">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($bookings as $booking)
                            <tr>
                                <td>
                                    <input type="checkbox" class="booking-checkbox" value="{{ $booking->id }}">
                                </td>
                                <td>
                                    <strong>{{ $booking->booking_number }}</strong>
                                </td>
                                <td>
                                    <a href="{{ route('admin.events.show', $booking->event_id) }}">
                                        {{ Str::limit($booking->event->title ?? 'N/A', 30) }}
                                    </a>
                                </td>
                                <td>
                                    {{ $booking->user->name ?? 'N/A' }}<br>
                                    <small>{{ $booking->user->email ?? '' }}</small>
                                </td>
                                <td>{{ $booking->ticketType->name ?? 'N/A' }}</td>
                                <td class="text-center">{{ $booking->quantity }}</td>
                                <td>${{ number_format($booking->final_price, 2) }}</td>
                                <td>
                                    @if($booking->status == 'confirmed')
                                        <span class="badge badge-success">Confirmed</span>
                                    @elseif($booking->status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @elseif($booking->status == 'cancelled')
                                        <span class="badge badge-danger">Cancelled</span>
                                    @elseif($booking->status == 'refunded')
                                        <span class="badge badge-info">Refunded</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $booking->status }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($booking->payment_status == 'paid')
                                        <span class="badge badge-success">Paid</span>
                                    @elseif($booking->payment_status == 'pending')
                                        <span class="badge badge-warning">Pending</span>
                                    @else
                                        <span class="badge badge-danger">{{ $booking->payment_status }}</span>
                                    @endif
                                </td>
                                <td>{{ $booking->created_at->format('M d, Y') }}</td>
                                <td>
                                    @if($booking->checked_in_at)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check-circle"></i> {{ $booking->checked_in_at->format('H:i') }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-times-circle"></i> Not checked
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.attendee.bookings.show', $booking) }}" 
                                           class="btn btn-sm btn-info" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.attendee.bookings.edit', $booking) }}" 
                                           class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$booking->checked_in_at && $booking->status == 'confirmed')
                                        <button type="button" class="btn btn-sm btn-success checkin-btn" 
                                                data-id="{{ $booking->id }}" title="Check-in">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                                        @endif
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                data-id="{{ $booking->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="12" class="text-center">No bookings found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="row mt-3">
                    <div class="col-md-6">
                        Showing {{ $bookings->firstItem() ?? 0 }} to {{ $bookings->lastItem() ?? 0 }} 
                        of {{ $bookings->total() }} entries
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $bookings->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form method="GET">
                <div class="modal-header">
                    <h5 class="modal-title">Filter Bookings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Event</label>
                        <select name="event_id" class="form-control">
                            <option value="">All Events</option>
                            @foreach($events ?? [] as $event)
                                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="">All Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label>Payment Status</label>
                        <select name="payment_status" class="form-control">
                            <option value="">All Payment Status</option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Failed</option>
                            <option value="refunded" {{ request('payment_status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date From</label>
                                <input type="date" name="date_from" class="form-control" 
                                       value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date To</label>
                                <input type="date" name="date_to" class="form-control" 
                                       value="{{ request('date_to') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.attendee.bookings.index') }}" class="btn btn-secondary">
                        Clear Filters
                    </a>
                    <button type="submit" class="btn btn-primary">Apply Filters</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this booking?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('attendee-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Select All checkbox
    $('#checkAll').click(function() {
        $('.booking-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    // Select All button
    $('#selectAll').click(function() {
        $('.booking-checkbox').prop('checked', true);
        $('#checkAll').prop('checked', true);
    });
    
    // Bulk Export
    $('#bulkExport').click(function() {
        var selected = [];
        $('.booking-checkbox:checked').each(function() {
            selected.push($(this).val());
        });
        
        if (selected.length === 0) {
            Swal.fire('Warning', 'Please select at least one booking', 'warning');
            return;
        }
        
        window.location.href = '{{ route("admin.attendee.bookings.export") }}?ids=' + selected.join(',');
    });
    
    // Bulk Check-in
    $('#bulkCheckin').click(function() {
        var selected = [];
        $('.booking-checkbox:checked').each(function() {
            selected.push($(this).val());
        });
        
        if (selected.length === 0) {
            Swal.fire('Warning', 'Please select at least one booking', 'warning');
            return;
        }
        
        Swal.fire({
            title: 'Confirm Check-in',
            text: 'Check-in ' + selected.length + ' attendees?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, check-in',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.attendee.bookings.bulk-action") }}',
                    method: 'POST',
                    data: {
                        action: 'checkin',
                        booking_ids: selected,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Success', response.message, 'success')
                            .then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to check-in attendees', 'error');
                    }
                });
            }
        });
    });
    
    // Bulk Cancel
    $('#bulkCancel').click(function() {
        var selected = [];
        $('.booking-checkbox:checked').each(function() {
            selected.push($(this).val());
        });
        
        if (selected.length === 0) {
            Swal.fire('Warning', 'Please select at least one booking', 'warning');
            return;
        }
        
        Swal.fire({
            title: 'Confirm Cancellation',
            text: 'Cancel ' + selected.length + ' bookings?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, cancel',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.attendee.bookings.bulk-action") }}',
                    method: 'POST',
                    data: {
                        action: 'cancel',
                        booking_ids: selected,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Success', response.message, 'success')
                            .then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to cancel bookings', 'error');
                    }
                });
            }
        });
    });
    
    // Individual Check-in
    $('.checkin-btn').click(function() {
        var id = $(this).data('id');
        
        Swal.fire({
            title: 'Confirm Check-in',
            text: 'Check-in this attendee?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, check-in',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ url("admin/attendee/bookings") }}/' + id + '/checkin',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Success', 'Attendee checked in successfully', 'success')
                            .then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to check-in', 'error');
                    }
                });
            }
        });
    });
    
    // Delete
    $('.delete-btn').click(function() {
        var id = $(this).data('id');
        $('#deleteForm').attr('action', '{{ url("admin/attendee/bookings") }}/' + id);
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush