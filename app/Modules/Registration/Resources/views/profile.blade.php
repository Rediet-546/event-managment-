@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container-fluid py-4">
    <!-- Profile Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar-wrapper me-4">
                            <img src="{{ $user->profile->avatar_url ?? asset('images/default-avatar.png') }}" 
                                 alt="Avatar" 
                                 class="rounded-circle border border-3 border-white"
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        <div>
                            <h2 class="mb-1">{{ $user->full_name }}</h2>
                            <p class="mb-2 opacity-75">
                                <i class="fas fa-envelope me-2"></i>{{ $user->email }}
                                @if($user->isEventCreator())
                                    <span class="badge bg-warning text-dark ms-2">
                                        <i class="fas fa-calendar-plus me-1"></i>Event Creator
                                    </span>
                                @else
                                    <span class="badge bg-info ms-2">
                                        <i class="fas fa-user me-1"></i>Attendee
                                    </span>
                                @endif
                            </p>
                            <p class="mb-0 small">
                                <i class="fas fa-calendar-alt me-2"></i>Member since {{ $user->created_at->format('F Y') }}
                            </p>
                        </div>
                        <div class="ms-auto">
                            <a href="{{ route('profile.edit') }}" class="btn btn-light">
                                <i class="fas fa-edit me-2"></i>Edit Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        @if($user->isAttendee())
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Total Bookings</h6>
                                <h3>{{ $statistics['total_bookings'] }}</h3>
                            </div>
                            <div class="stat-icon bg-primary bg-opacity-10">
                                <i class="fas fa-ticket-alt fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Upcoming Events</h6>
                                <h3>{{ $statistics['upcoming_events'] }}</h3>
                            </div>
                            <div class="stat-icon bg-success bg-opacity-10">
                                <i class="fas fa-calendar-check fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Total Spent</h6>
                                <h3>${{ number_format($statistics['total_spent'], 2) }}</h3>
                            </div>
                            <div class="stat-icon bg-warning bg-opacity-10">
                                <i class="fas fa-dollar-sign fa-2x text-warning"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Total Events</h6>
                                <h3>{{ $statistics['total_events'] }}</h3>
                            </div>
                            <div class="stat-icon bg-primary bg-opacity-10">
                                <i class="fas fa-calendar fa-2x text-primary"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card stat-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h6 class="text-muted mb-2">Total Revenue</h6>
                                <h3>${{ number_format($statistics['total_revenue'] ?? 0, 2) }}</h3>
                            </div>
                            <div class="stat-icon bg-success bg-opacity-10">
                                <i class="fas fa-dollar-sign fa-2x text-success"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        <div class="col-md-3 mb-3">
            <div class="card stat-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h6 class="text-muted mb-2">Account Age</h6>
                            <h3>{{ $statistics['member_since'] }}</h3>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10">
                            <i class="fas fa-clock fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Profile Details -->
    <div class="row">
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="fas fa-user text-primary me-2"></i>
                            <strong>Full Name:</strong> {{ $user->full_name }}
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-at text-primary me-2"></i>
                            <strong>Username:</strong> {{ $user->username }}
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-calendar text-primary me-2"></i>
                            <strong>Age:</strong> {{ $user->age }} years
                        </li>
                        @if($user->isEventCreator())
                            <li class="mb-3">
                                <i class="fas fa-building text-primary me-2"></i>
                                <strong>Organization:</strong> {{ $user->organization_name ?? 'Not specified' }}
                            </li>
                            <li class="mb-3">
                                <i class="fas fa-id-card text-primary me-2"></i>
                                <strong>Tax ID:</strong> {{ $user->tax_id ?? 'Not specified' }}
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Contact Information</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <strong>Email:</strong> {{ $user->email }}
                            @if($user->email_verified_at)
                                <span class="badge bg-success ms-2">Verified</span>
                            @else
                                <span class="badge bg-warning ms-2">Unverified</span>
                            @endif
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <strong>Phone:</strong> {{ $user->profile->phone ?? 'Not specified' }}
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <strong>Address:</strong> {{ $user->profile->full_address ?? 'Not specified' }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Account Status</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="fas fa-circle text-success me-2"></i>
                            <strong>Status:</strong> 
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-clock text-primary me-2"></i>
                            <strong>Last Login:</strong> 
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-globe text-primary me-2"></i>
                            <strong>Last IP:</strong> {{ $user->last_login_ip ?? 'N/A' }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Activity</h5>
                    @if($user->isAttendee())
                        <a href="{{ route('attendee.bookings') }}" class="btn btn-sm btn-outline-primary">
                            View All Bookings
                        </a>
                    @else
                        <a href="{{ route('creator.dashboard') }}" class="btn btn-sm btn-outline-primary">
                            Go to Dashboard
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Action</th>
                                    <th>Details</th>
                                    <th>IP Address</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(activity()->causedBy($user)->latest()->limit(10)->get() as $activity)
                                <tr>
                                    <td>{{ $activity->created_at->format('M d, Y H:i') }}</td>
                                    <td>{{ $activity->log_name }}</td>
                                    <td>{{ $activity->description }}</td>
                                    <td>{{ $activity->getExtraProperty('ip') ?? 'N/A' }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <p class="text-muted mb-0">No recent activity</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
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
    transition: transform 0.3s;
    border: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.05);
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
</style>
@endpush