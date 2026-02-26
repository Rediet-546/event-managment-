@extends('layouts.app')

@section('title', 'Booking Successful')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg text-center">
                <div class="card-body p-5">
                    <!-- Success Icon -->
                    <div class="mb-4">
                        <div class="rounded-circle bg-success d-inline-flex p-4">
                            <i class="fas fa-check-circle text-white fa-4x"></i>
                        </div>
                    </div>
                    
                    <h1 class="display-4 mb-3">Booking Successful!</h1>
                    <p class="lead text-muted mb-4">Thank you for your booking. Your tickets have been confirmed.</p>
                    
                    <!-- Booking Details Card -->
                    <div class="card bg-light mb-4">
                        <div class="card-body">
                            <h5 class="card-title text-primary mb-3">Booking Details</h5>
                            <div class="row">
                                <div class="col-sm-6 text-sm-right">
                                    <strong>Booking Number:</strong>
                                </div>
                                <div class="col-sm-6 text-sm-left">
                                    <span class="badge badge-primary badge-lg">{{ $booking->booking_number }}</span>
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-sm-6 text-sm-right">
                                    <strong>Event:</strong>
                                </div>
                                <div class="col-sm-6 text-sm-left">
                                    {{ $booking->event->title }}
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-sm-6 text-sm-right">
                                    <strong>Date:</strong>
                                </div>
                                <div class="col-sm-6 text-sm-left">
                                    {{ $booking->event->start_date->format('F d, Y - h:i A') }}
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-sm-6 text-sm-right">
                                    <strong>Venue:</strong>
                                </div>
                                <div class="col-sm-6 text-sm-left">
                                    {{ $booking->event->venue }}
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-sm-6 text-sm-right">
                                    <strong>Tickets:</strong>
                                </div>
                                <div class="col-sm-6 text-sm-left">
                                    {{ $booking->ticketType->name }} ({{ $booking->quantity }} tickets)
                                </div>
                            </div>
                            <hr class="my-2">
                            <div class="row">
                                <div class="col-sm-6 text-sm-right">
                                    <strong>Total Paid:</strong>
                                </div>
                                <div class="col-sm-6 text-sm-left">
                                    <span class="text-success font-weight-bold">${{ number_format($booking->final_price, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('attendee.front.tickets.booking', $booking->id) }}" class="btn btn-success btn-lg btn-block">
                                <i class="fas fa-ticket-alt mr-2"></i> View Tickets
                            </a>
                        </div>
                        <div class="col-md-6 mb-2">
                            <a href="{{ route('attendee.front.bookings.show', $booking) }}" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-file-invoice mr-2"></i> View Booking
                            </a>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('attendee.front.account.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user mr-2"></i> Go to Dashboard
                        </a>
                        <a href="/" class="btn btn-outline-primary">
                            <i class="fas fa-home mr-2"></i> Home
                        </a>
                    </div>
                    
                    <!-- Email Confirmation Message -->
                    <div class="alert alert-info mt-4 mb-0">
                        <i class="fas fa-envelope mr-2"></i>
                        A confirmation email has been sent to <strong>{{ auth()->user()->email }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.badge-lg {
    font-size: 1rem;
    padding: 0.5rem 1rem;
}
</style>
@endsection