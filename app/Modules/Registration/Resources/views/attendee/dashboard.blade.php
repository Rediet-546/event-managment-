@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">Welcome back, {{ $user->first_name }}!</h2>
                            <p class="mb-0 opacity-75">
                                <i class="fas fa-calendar-alt me-2"></i>
                                {{ now()->format('l, F j, Y') }}
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('events.index') }}" class="btn btn-light">
                                <i class="fas fa-search me-2"></i>Browse Events
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small">Total Bookings</span>
                            <h3 class="mb-0">{{ $statistics['total_bookings'] }}</h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10">
                            <i class="fas fa-ticket-alt fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-success">
                            <i class="fas fa-arrow-up me-1"></i>
                            {{ $statistics['upcoming_events'] }} upcoming
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small">Upcoming Events</span>
                            <h3 class="mb-0">{{ $statistics['upcoming_events'] }}</h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10">
                            <i class="fas fa-calendar-check fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-info">
                            <i class="fas fa-clock me-1"></i>
                            Next: {{ $upcomingBookings->first()?->event->start_date->diffForHumans() ?? 'No events' }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small">Past Events</span>
                            <h3 class="mb-0">{{ $statistics['past_events'] }}</h3>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10">
                            <i class="fas fa-history fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="card stat-card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted text-uppercase small">Total Spent</span>
                            <h3 class="mb-0">${{ number_format($statistics['total_spent'], 2) }}</h3>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10">
                            <i class="fas fa-dollar-sign fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Upcoming Bookings -->
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                        Your Upcoming Events
                    </h5>
                    <a href="{{ route('attendee.bookings') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
                <div class="card-body">
                    @if($upcomingBookings->count() > 0)
                        <div class="row">
                            @foreach($upcomingBookings as $booking)
                                <div class="col-md-6 mb-3">
                                    <div class="card booking-card h-100">
                                        @if($booking->event->cover_image)
                                            <img src="{{ asset('storage/' . $booking->event->cover_image) }}" 
                                                 class="card-img-top" 
                                                 alt="{{ $booking->event->title }}"
                                                 style="height: 120px; object-fit: cover;">
                                        @endif
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between mb-2">
                                                <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($booking->status) }}
                                                </span>
                                                <small class="text-muted">
                                                    #{{ substr($booking->booking_reference, -8) }}
                                                </small>
                                            </div>
                                            
                                            <h6 class="card-title">{{ $booking->event->title }}</h6>
                                            
                                            <p class="card-text small text-muted mb-2">
                                                <i class="fas fa-calendar me-2"></i>
                                                {{ $booking->event->start_date->format('M d, Y - h:i A') }}
                                            </p>
                                            
                                            <p class="card-text small text-muted mb-2">
                                                <i class="fas fa-map-marker-alt me-2"></i>
                                                {{ $booking->event->venue }}
                                            </p>
                                            
                                            <div class="d-flex justify-content-between align-items-center mt-3">
                                                <span class="small">
                                                    <i class="fas fa-users me-1"></i>
                                                    {{ $booking->number_of_guests }} guests
                                                </span>
                                                <a href="{{ route('booking.view', $booking->booking_reference) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('images/no-bookings.svg') }}" 
                                 alt="No bookings" 
                                 style="max-width: 150px; opacity: 0.5;">
                            <h6 class="mt-3">No Upcoming Events</h6>
                            <p class="text-muted small">You haven't booked any upcoming events yet.</p>
                            <a href="{{ route('events.index') }}" class="btn btn-primary btn-sm">
                                Browse Events
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4 mb-4">
            <!-- Quick Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt text-warning me-2"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('events.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-search me-2"></i>Browse Events
                        </a>
                        <a href="{{ route('events.calendar') }}" class="btn btn-outline-info">
                            <i class="fas fa-calendar-alt me-2"></i>View Calendar
                        </a>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user-cog me-2"></i>Update Profile
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-history text-info me-2"></i>
                        Recent Activity
                    </h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($pastBookings as $booking)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">{{ $booking->event->title }}</h6>
                                        <small class="text-muted">
                                            <i class="fas fa-calendar me-1"></i>
                                            {{ $booking->event->start_date->format('M d, Y') }}
                                        </small>
                                    </div>
                                    <span class="badge bg-{{ $booking->status === 'completed' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <p class="text-muted mb-0">No recent activity</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}
.stat-card {
    border: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    transition: all 0.3s;
}
.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.booking-card {
    border: 1px solid rgba(0,0,0,0.05);
    transition: all 0.3s;
}
.booking-card:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    transform: translateY(-3px);
}
</style>
@endpush