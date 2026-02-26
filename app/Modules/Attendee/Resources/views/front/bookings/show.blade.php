@extends('layouts.app')

@section('title', 'Booking Details - ' . $booking->booking_number)

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <!-- Booking Header -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-file-invoice mr-2"></i>Booking Details</h4>
                    <span class="badge badge-light badge-lg">{!! $booking->status_label !!}</span>
                </div>
                
                <div class="card-body">
                    <!-- Booking Info Grid -->
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="120">Booking #:</th>
                                    <td><strong>{{ $booking->booking_number }}</strong></td>
                                </tr>
                                <tr>
                                    <th>Booking Date:</th>
                                    <td>{{ $booking->created_at->format('F d, Y - h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Event:</th>
                                    <td>{{ $booking->event->title }}</td>
                                </tr>
                                <tr>
                                    <th>Event Date:</th>
                                    <td>{{ $booking->event->start_date->format('F d, Y - h:i A') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="120">Ticket Type:</th>
                                    <td>{{ $booking->ticketType->name }}</td>
                                </tr>
                                <tr>
                                    <th>Quantity:</th>
                                    <td>{{ $booking->quantity }}</td>
                                </tr>
                                <tr>
                                    <th>Unit Price:</th>
                                    <td>${{ number_format($booking->unit_price, 2) }}</td>
                                </tr>
                                <tr>
                                    <th>Total:</th>
                                    <td class="text-success font-weight-bold">${{ number_format($booking->final_price, 2) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    @if($booking->special_requests)
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle mr-2"></i>
                        <strong>Special Requests:</strong> {{ $booking->special_requests }}
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- Tickets Section -->
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-ticket-alt mr-2"></i>Your Tickets</h5>
                </div>
                <div class="card-body">
                    @forelse($booking->tickets as $ticket)
                    <div class="ticket-card border rounded p-3 mb-3">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-ticket-alt text-primary fa-2x mr-3"></i>
                                    <div>
                                        <h6 class="mb-1">Ticket #{{ $ticket->ticket_number }}</h6>
                                        <p class="mb-0 small text-muted">
                                            Attendee: {{ $ticket->attendee_name }}<br>
                                            Status: 
                                            @if($ticket->status == 'active')
                                                <span class="badge badge-success">Active</span>
                                            @elseif($ticket->checked_in_at)
                                                <span class="badge badge-info">Checked In</span>
                                            @else
                                                <span class="badge badge-secondary">Used</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <a href="{{ route('attendee.front.tickets.show', $ticket->ticket_number) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <a href="{{ route('attendee.front.tickets.download', $ticket->ticket_number) }}" 
                                   class="btn btn-sm btn-outline-success">
                                    <i class="fas fa-download"></i> PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-center text-muted">No tickets generated yet.</p>
                    @endforelse
                </div>
            </div>
            
            <!-- Payment Information -->
            @if($booking->payment)
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-credit-card mr-2"></i>Payment Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="150">Transaction ID:</th>
                            <td>{{ $booking->payment->transaction_id }}</td>
                        </tr>
                        <tr>
                            <th>Payment Method:</th>
                            <td>{{ ucfirst($booking->payment->payment_method) }}</td>
                        </tr>
                        <tr>
                            <th>Amount Paid:</th>
                            <td class="text-success font-weight-bold">${{ number_format($booking->payment->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Payment Date:</th>
                            <td>{{ $booking->payment->created_at->format('F d, Y - h:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($booking->payment->status == 'completed')
                                    <span class="badge badge-success">Completed</span>
                                @else
                                    <span class="badge badge-warning">{{ $booking->payment->status }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            @endif
            
            <!-- Action Buttons -->
            <div class="text-center">
                @if($booking->can_be_cancelled)
                <button type="button" class="btn btn-danger btn-lg" id="cancelBooking">
                    <i class="fas fa-times-circle mr-2"></i> Cancel Booking
                </button>
                @endif
                <a href="{{ route('attendee.front.account.bookings') }}" class="btn btn-secondary btn-lg">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Bookings
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    $('#cancelBooking').click(function() {
        Swal.fire({
            title: 'Cancel Booking?',
            text: 'Are you sure you want to cancel this booking? This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, cancel it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '{{ route("attendee.front.bookings.cancel", $booking) }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        Swal.fire(
                            'Cancelled!',
                            'Your booking has been cancelled.',
                            'success'
                        ).then(() => {
                            location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire(
                            'Error!',
                            'Failed to cancel booking.',
                            'error'
                        );
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection