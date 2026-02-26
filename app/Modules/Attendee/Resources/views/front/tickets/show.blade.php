@extends('layouts.app')

@section('title', 'Ticket - ' . $ticket->ticket_number)

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card ticket-card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">
                        <i class="fas fa-ticket-alt mr-2"></i> Event Ticket
                    </h3>
                </div>
                
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <h2 class="text-primary mb-3">{{ $ticket->booking->event->title }}</h2>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <th width="150">Ticket Number:</th>
                                    <td>
                                        <strong>{{ $ticket->ticket_number }}</strong>
                                        @if($ticket->status == 'used')
                                            <span class="badge badge-secondary ml-2">Used</span>
                                        @else
                                            <span class="badge badge-success ml-2">Active</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Attendee:</th>
                                    <td>{{ $ticket->attendee_name }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $ticket->attendee_email }}</td>
                                </tr>
                                <tr>
                                    <th>Event Date:</th>
                                    <td>{{ $ticket->booking->event->start_date->format('F d, Y - h:i A') }}</td>
                                </tr>
                                <tr>
                                    <th>Venue:</th>
                                    <td>{{ $ticket->booking->event->venue }}</td>
                                </tr>
                                <tr>
                                    <th>Ticket Type:</th>
                                    <td>{{ $ticket->booking->ticketType->name }}</td>
                                </tr>
                                <tr>
                                    <th>Booking Reference:</th>
                                    <td>{{ $ticket->booking->booking_number }}</td>
                                </tr>
                            </table>

                            @if($ticket->status == 'active')
                                <div class="alert alert-info mt-3">
                                    <i class="fas fa-info-circle"></i> 
                                    Please present this ticket (digital or printed) at the event entrance.
                                </div>
                            @elseif($ticket->checked_in_at)
                                <div class="alert alert-success mt-3">
                                    <i class="fas fa-check-circle"></i> 
                                    Checked in at {{ $ticket->checked_in_at->format('F d, Y - h:i A') }}
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-4 text-center">
                            <div class="qr-container p-3 border rounded">
                                <h5>QR Code</h5>
                                <div id="qrCode"></div>
                                <p class="mt-2 small text-muted">Scan for check-in</p>
                            </div>
                            
                            <div class="mt-3">
                                <a href="{{ route('attendee.front.tickets.download', $ticket->ticket_number) }}" 
                                   class="btn btn-success btn-block">
                                    <i class="fas fa-download"></i> Download PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer text-muted">
                    <small>Ticket issued on {{ $ticket->created_at->format('F d, Y') }}</small>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <a href="{{ route('attendee.front.account.bookings') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to My Bookings
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.ticket-card {
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.qr-container {
    background: #f8f9fa;
}
#qrCode canvas, #qrCode img {
    max-width: 200px;
    height: auto;
    margin: 0 auto;
}
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.1/build/qrcode.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Generate QR code
    const qrData = '{{ $ticket->check_in_url }}';
    QRCode.toCanvas(document.getElementById('qrCode'), qrData, {
        width: 200,
        margin: 2,
        color: {
            dark: '#000000',
            light: '#ffffff'
        }
    }, function(error) {
        if (error) console.error(error);
    });
});
</script>
@endpush
@endsection