@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Booking Details - ' . $booking->booking_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.attendee.bookings.index') }}">Bookings</a></li>
    <li class="breadcrumb-item active">{{ $booking->booking_number }}</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-md-12">
        <!-- Action Buttons -->
        <div class="card">
            <div class="card-body">
                <div class="btn-group">
                    <a href="{{ route('admin.attendee.bookings.edit', $booking) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Booking
                    </a>
                    @if($booking->status == 'confirmed' && !$booking->checked_in_at)
                    <button type="button" class="btn btn-success" id="checkinBtn">
                        <i class="fas fa-user-check"></i> Check-in
                    </button>
                    @endif
                    @if($booking->status == 'pending')
                    <button type="button" class="btn btn-warning" id="confirmBtn">
                        <i class="fas fa-check-circle"></i> Confirm
                    </button>
                    @endif
                    @if($booking->status == 'confirmed' && $booking->can_be_cancelled)
                    <button type="button" class="btn btn-danger" id="cancelBtn">
                        <i class="fas fa-times-circle"></i> Cancel
                    </button>
                    @endif
                    <a href="{{ route('admin.attendee.bookings.export') }}?ids[]={{ $booking->id }}" class="btn btn-info">
                        <i class="fas fa-download"></i> Export
                    </a>
                    <button type="button" class="btn btn-secondary" onclick="window.print()">
                        <i class="fas fa-print"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Booking Information -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Booking Information</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Booking Number</th>
                        <td><strong>{{ $booking->booking_number }}</strong></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{!! $booking->status_label !!}</td>
                    </tr>
                    <tr>
                        <th>Booking Date</th>
                        <td>{{ $booking->created_at->format('F d, Y - h:i A') }}</td>
                    </tr>
                    <tr>
                        <th>Event</th>
                        <td>
                            <a href="{{ route('admin.events.show', $booking->event_id) }}">
                                {{ $booking->event->title ?? 'N/A' }}
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <th>Ticket Type</th>
                        <td>{{ $booking->ticketType->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Quantity</th>
                        <td>{{ $booking->quantity }}</td>
                    </tr>
                    <tr>
                        <th>Unit Price</th>
                        <td>${{ number_format($booking->unit_price, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Total Price</th>
                        <td>${{ number_format($booking->total_price, 2) }}</td>
                    </tr>
                    @if($booking->discount_amount > 0)
                    <tr>
                        <th>Discount</th>
                        <td class="text-success">-${{ number_format($booking->discount_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($booking->tax_amount > 0)
                    <tr>
                        <th>Tax</th>
                        <td>${{ number_format($booking->tax_amount, 2) }}</td>
                    </tr>
                    @endif
                    @if($booking->fee_amount > 0)
                    <tr>
                        <th>Service Fee</th>
                        <td>${{ number_format($booking->fee_amount, 2) }}</td>
                    </tr>
                    @endif
                    <tr class="bg-light">
                        <th><strong>Final Price</strong></th>
                        <td><strong>${{ number_format($booking->final_price, 2) }}</strong></td>
                    </tr>
                    @if($booking->special_requests)
                    <tr>
                        <th>Special Requests</th>
                        <td>{{ $booking->special_requests }}</td>
                    </tr>
                    @endif
                    @if($booking->notes)
                    <tr>
                        <th>Admin Notes</th>
                        <td>{{ $booking->notes }}</td>
                    </tr>
                    @endif
                </table>
            </div>
        </div>
    </div>
    
    <!-- Attendee Information -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Attendee Information</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th width="200">Name</th>
                        <td>{{ $booking->user->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $booking->user->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Phone</th>
                        <td>{{ $booking->user->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Member Since</th>
                        <td>{{ $booking->user->created_at->format('M d, Y') ?? 'N/A' }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Payment Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Payment Information</h3>
            </div>
            <div class="card-body">
                @if($booking->payment)
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Transaction ID</th>
                            <td>{{ $booking->payment->transaction_id ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>{{ ucfirst($booking->payment->payment_method ?? 'N/A') }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td>${{ number_format($booking->payment->amount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($booking->payment->status == 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @elseif($booking->payment->status == 'pending')
                                    <span class="badge badge-warning">Pending</span>
                                @else
                                    <span class="badge badge-danger">{{ $booking->payment->status }}</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Paid At</th>
                            <td>{{ $booking->payment->paid_at ? $booking->payment->paid_at->format('F d, Y - h:i A') : 'N/A' }}</td>
                        </tr>
                    </table>
                @else
                    <p class="text-center text-muted">No payment information available</p>
                @endif
            </div>
        </div>
        
        <!-- Tickets Information -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Tickets</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Ticket Number</th>
                            <th>Status</th>
                            <th>Check-in</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($booking->tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->ticket_number }}</td>
                            <td>
                                @if($ticket->status == 'active')
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-secondary">Used</span>
                                @endif
                            </td>
                            <td>
                                @if($ticket->checked_in_at)
                                    {{ $ticket->checked_in_at->format('M d, H:i') }}
                                @else
                                    <span class="badge badge-secondary">Not checked</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.attendee.tickets.show', $ticket->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ $ticket->qr_code_url }}" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-qrcode"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center">No tickets generated yet</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- History -->
        <div class="card mt-3">
            <div class="card-header">
                <h3 class="card-title">Booking History</h3>
            </div>
            <div class="card-body p-0">
                <div class="timeline" style="padding: 20px;">
                    @forelse($booking->history as $history)
                    <div class="time-label">
                        <span class="bg-info">{{ $history->created_at->format('M d, Y') }}</span>
                    </div>
                    <div>
                        <i class="fas fa-{{ $history->icon ?? 'clock' }} bg-{{ $history->color ?? 'primary' }}"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> {{ $history->created_at->format('h:i A') }}</span>
                            <h3 class="timeline-header">
                                <strong>{{ ucfirst($history->action) }}</strong>
                                @if($history->user)
                                    <span class="text-muted">by {{ $history->user->name }}</span>
                                @endif
                            </h3>
                            <div class="timeline-body">
                                {{ $history->description }}
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted">No history available</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('attendee-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Check-in button
    $('#checkinBtn').click(function() {
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
                    url: '{{ route("admin.attendee.bookings.checkin", $booking) }}',
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
    
    // Confirm button
    $('#confirmBtn').click(function() {
        Swal.fire({
            title: 'Confirm Booking',
            text: 'Confirm this booking?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, confirm',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.attendee.bookings.confirm", $booking) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Success', 'Booking confirmed successfully', 'success')
                            .then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to confirm booking', 'error');
                    }
                });
            }
        });
    });
    
    // Cancel button
    $('#cancelBtn').click(function() {
        Swal.fire({
            title: 'Cancel Booking',
            text: 'Are you sure you want to cancel this booking?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, cancel',
            cancelButtonText: 'No'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("admin.attendee.bookings.cancel", $booking) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire('Success', 'Booking cancelled successfully', 'success')
                            .then(() => location.reload());
                    },
                    error: function(xhr) {
                        Swal.fire('Error', 'Failed to cancel booking', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush