@extends('layouts.app')

@section('title', 'My Bookings')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Account Menu</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('attendee.front.account.dashboard') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                    </a>
                    <a href="{{ route('attendee.front.account.bookings') }}" class="list-group-item list-group-item-action active">
                        <i class="fas fa-ticket-alt mr-2"></i> My Bookings
                    </a>
                    <a href="{{ route('attendee.front.account.profile') }}" class="list-group-item list-group-item-action">
                        <i class="fas fa-user mr-2"></i> Profile
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-md-9">
            <div class="card shadow">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-ticket-alt mr-2"></i>My Bookings</h5>
                </div>
                
                <div class="card-body">
                    <!-- Filter Tabs -->
                    <ul class="nav nav-pills mb-3" id="bookingTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="all-tab" data-toggle="tab" href="#all" role="tab">
                                All
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="upcoming-tab" data-toggle="tab" href="#upcoming" role="tab">
                                Upcoming
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="past-tab" data-toggle="tab" href="#past" role="tab">
                                Past
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="cancelled-tab" data-toggle="tab" href="#cancelled" role="tab">
                                Cancelled
                            </a>
                        </li>
                    </ul>
                    
                    <!-- Bookings List -->
                    <div class="tab-content" id="bookingTabsContent">
                        <!-- All Bookings -->
                        <div class="tab-pane fade show active" id="all" role="tabpanel">
                            @forelse($bookings as $booking)
                            <div class="booking-card border rounded p-3 mb-3">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex">
                                            <div class="booking-date text-center mr-3">
                                                <div class="month bg-primary text-white px-3 py-1 rounded-top">
                                                    {{ $booking->event->start_date->format('M') }}
                                                </div>
                                                <div class="day border border-top-0 rounded-bottom px-3 py-2">
                                                    <strong>{{ $booking->event->start_date->format('d') }}</strong>
                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">{{ $booking->event->title }}</h5>
                                                <p class="mb-1 text-muted">
                                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $booking->event->venue }}<br>
                                                    <i class="fas fa-clock mr-1"></i> {{ $booking->event->start_date->format('h:i A') }}
                                                </p>
                                                <div>
                                                    <span class="badge badge-info mr-2">{{ $booking->ticketType->name }}</span>
                                                    <span class="badge badge-secondary">{{ $booking->quantity }} tickets</span>
                                                    {!! $booking->status_label !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <strong class="text-success d-block mb-2">${{ number_format($booking->final_price, 2) }}</strong>
                                        <a href="{{ route('attendee.front.bookings.show', $booking) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-center text-muted py-4">No bookings found.</p>
                            @endforelse
                            
                            <!-- Pagination -->
                            <div class="mt-3">
                                {{ $bookings->withQueryString()->links() }}
                            </div>
                        </div>
                        
                        <!-- Upcoming Bookings -->
                        <div class="tab-pane fade" id="upcoming" role="tabpanel">
                            @forelse($bookings->where('event.start_date', '>', now()) as $booking)
                            <div class="booking-card border rounded p-3 mb-3">
                                <!-- Same structure as above -->
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex">
                                            <div class="booking-date text-center mr-3">
                                                <div class="month bg-success text-white px-3 py-1 rounded-top">
                                                    {{ $booking->event->start_date->format('M') }}
                                                </div>
                                                <div class="day border border-top-0 rounded-bottom px-3 py-2">
                                                    <strong>{{ $booking->event->start_date->format('d') }}</strong>
                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">{{ $booking->event->title }}</h5>
                                                <p class="mb-1 text-muted">
                                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $booking->event->venue }}<br>
                                                    <i class="fas fa-clock mr-1"></i> {{ $booking->event->start_date->format('h:i A') }}
                                                </p>
                                                <div>
                                                    <span class="badge badge-info">{{ $booking->ticketType->name }}</span>
                                                    <span class="badge badge-secondary">{{ $booking->quantity }} tickets</span>
                                                    {!! $booking->status_label !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <strong class="text-success d-block mb-2">${{ number_format($booking->final_price, 2) }}</strong>
                                        <a href="{{ route('attendee.front.bookings.show', $booking) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-center text-muted py-4">No upcoming bookings.</p>
                            @endforelse
                        </div>
                        
                        <!-- Past Bookings -->
                        <div class="tab-pane fade" id="past" role="tabpanel">
                            @forelse($bookings->where('event.start_date', '<', now()) as $booking)
                            <div class="booking-card border rounded p-3 mb-3 bg-light">
                                <!-- Same structure but with muted style -->
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex">
                                            <div class="booking-date text-center mr-3 opacity-50">
                                                <div class="month bg-secondary text-white px-3 py-1 rounded-top">
                                                    {{ $booking->event->start_date->format('M') }}
                                                </div>
                                                <div class="day border border-top-0 rounded-bottom px-3 py-2">
                                                    <strong>{{ $booking->event->start_date->format('d') }}</strong>
                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">{{ $booking->event->title }}</h5>
                                                <p class="mb-1 text-muted">
                                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $booking->event->venue }}<br>
                                                    <i class="fas fa-clock mr-1"></i> {{ $booking->event->start_date->format('h:i A') }}
                                                </p>
                                                <div>
                                                    <span class="badge badge-info">{{ $booking->ticketType->name }}</span>
                                                    <span class="badge badge-secondary">{{ $booking->quantity }} tickets</span>
                                                    {!! $booking->status_label !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <strong class="text-muted d-block mb-2">${{ number_format($booking->final_price, 2) }}</strong>
                                        <a href="{{ route('attendee.front.bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-center text-muted py-4">No past bookings.</p>
                            @endforelse
                        </div>
                        
                        <!-- Cancelled Bookings -->
                        <div class="tab-pane fade" id="cancelled" role="tabpanel">
                            @forelse($bookings->where('status', 'cancelled') as $booking)
                            <div class="booking-card border rounded p-3 mb-3 bg-light">
                                <!-- Same structure with cancelled style -->
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex">
                                            <div class="booking-date text-center mr-3 opacity-50">
                                                <div class="month bg-danger text-white px-3 py-1 rounded-top">
                                                    {{ $booking->event->start_date->format('M') }}
                                                </div>
                                                <div class="day border border-top-0 rounded-bottom px-3 py-2">
                                                    <strong>{{ $booking->event->start_date->format('d') }}</strong>
                                                </div>
                                            </div>
                                            <div>
                                                <h5 class="mb-1">{{ $booking->event->title }}</h5>
                                                <p class="mb-1 text-muted">
                                                    <i class="fas fa-map-marker-alt mr-1"></i> {{ $booking->event->venue }}<br>
                                                    <i class="fas fa-clock mr-1"></i> {{ $booking->event->start_date->format('h:i A') }}
                                                </p>
                                                <div>
                                                    <span class="badge badge-info">{{ $booking->ticketType->name }}</span>
                                                    <span class="badge badge-secondary">{{ $booking->quantity }} tickets</span>
                                                    <span class="badge badge-danger">Cancelled</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <strong class="text-muted d-block mb-2">${{ number_format($booking->final_price, 2) }}</strong>
                                        <a href="{{ route('attendee.front.bookings.show', $booking) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-center text-muted py-4">No cancelled bookings.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.booking-date {
    min-width: 60px;
}
.booking-date .month {
    font-size: 0.8rem;
    font-weight: bold;
}
.booking-date .day {
    font-size: 1.2rem;
}
.opacity-50 {
    opacity: 0.5;
}
</style>
@endpush
@endsection