@extends('layouts.app')

@section('title', 'Creator Dashboard')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">Welcome back, {{ $user->first_name }}!</h2>
                            <p class="mb-0 opacity-75">
                                <i class="fas fa-chart-line me-2"></i>
                                Here's what's happening with your events
                            </p>
                        </div>
                        <div>
                            <a href="{{ route('events.create') }}" class="btn btn-light">
                                <i class="fas fa-plus-circle me-2"></i>Create New Event
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
                            <span class="text-muted text-uppercase small">Total Events</span>
                            <h3 class="mb-0">{{ $statistics['total_events'] }}</h3>
                        </div>
                        <div class="stat-icon bg-primary bg-opacity-10">
                            <i class="fas fa-calendar fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-success">
                            <i class="fas fa-arrow-up me-1"></i>
                            {{ $statistics['active_events'] }} active
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
                            <span class="text-muted text-uppercase small">Total Bookings</span>
                            <h3 class="mb-0">{{ $statistics['total_bookings'] }}</h3>
                        </div>
                        <div class="stat-icon bg-success bg-opacity-10">
                            <i class="fas fa-ticket-alt fa-2x text-success"></i>
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
                            <span class="text-muted text-uppercase small">Total Revenue</span>
                            <h3 class="mb-0">${{ number_format($statistics['total_revenue'], 2) }}</h3>
                        </div>
                        <div class="stat-icon bg-warning bg-opacity-10">
                            <i class="fas fa-dollar-sign fa-2x text-warning"></i>
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
                            <span class="text-muted text-uppercase small">Conversion Rate</span>
                            <h3 class="mb-0">78%</h3>
                        </div>
                        <div class="stat-icon bg-info bg-opacity-10">
                            <i class="fas fa-chart-line fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-8 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Revenue Overview</h5>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4 mb-3">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">Events by Status</h5>
                </div>
                <div class="card-body">
                    <canvas id="eventsPieChart" height="200"></canvas>
                    <div class="mt-3">
                        @php
                            $statuses = \App\Modules\Events\Models\Event::where('creator_id', $user->id)
                                ->selectRaw('status, count(*) as count')
                                ->groupBy('status')
                                ->get();
                        @endphp
                        @foreach($statuses as $status)
                            <div class="d-flex justify-content-between mb-2">
                                <span>
                                    <span class="badge bg-{{ $status->status === 'published' ? 'success' : ($status->status === 'draft' ? 'secondary' : 'danger') }}">
                                        {{ ucfirst($status->status) }}
                                    </span>
                                </span>
                                <span class="fw-bold">{{ $status->count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Events & Recent Bookings -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Your Events</h5>
                    <a href="{{ route('creator.analytics') }}" class="btn btn-sm btn-outline-primary">
                        View Analytics
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Event</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Bookings</th>
                                    <th>Revenue</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($events as $event)
                                    <tr>
                                        <td>
                                            <a href="{{ route('events.show', $event) }}" class="text-decoration-none fw-bold">
                                                {{ Str::limit($event->title, 30) }}
                                            </a>
                                        </td>
                                        <td>
                                            <small>
                                                <i class="fas fa-calendar me-1"></i>
                                                {{ $event->start_date->format('M d, Y') }}
                                            </small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $event->status === 'published' ? 'success' : ($event->status === 'draft' ? 'secondary' : 'danger') }}">
                                                {{ ucfirst($event->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $event->bookings_count ?? 0 }}
                                            </span>
                                        </td>
                                        <td>
                                            <strong>${{ number_format($event->bookings_sum_total_amount ?? 0, 2) }}</strong>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="{{ route('events.edit', $event) }}" 
                                                   class="btn btn-sm btn-outline-primary"
                                                   title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="{{ route('creator.event-bookings', $event) }}" 
                                                   class="btn btn-sm btn-outline-info"
                                                   title="View Bookings">
                                                    <i class="fas fa-users"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <img src="{{ asset('images/no-events.svg') }}" 
                                                 alt="No events" 
                                                 style="max-width: 100px; opacity: 0.5;">
                                            <p class="text-muted mt-3">You haven't created any events yet</p>
                                            <a href="{{ route('events.create') }}" class="btn btn-primary">
                                                Create Your First Event
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    @if($events->hasPages())
                        <div class="mt-3">
                            {{ $events->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <!-- Recent Bookings -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Recent Bookings</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @forelse($recentBookings as $booking)
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between mb-1">
                                    <h6 class="mb-0">{{ $booking->user->full_name }}</h6>
                                    <span class="badge bg-{{ $booking->status === 'confirmed' ? 'success' : 'warning' }}">
                                        {{ ucfirst($booking->status) }}
                                    </span>
                                </div>
                                <small class="text-muted d-block">
                                    <i class="fas fa-ticket-alt me-1"></i>
                                    {{ $booking->event->title }}
                                </small>
                                <div class="d-flex justify-content-between mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-users me-1"></i>
                                        {{ $booking->number_of_guests }} guests
                                    </small>
                                    <small class="fw-bold">
                                        ${{ number_format($booking->total_amount, 2) }}
                                    </small>
                                </div>
                            </div>
                        @empty
                            <div class="list-group-item text-center py-4">
                                <p class="text-muted mb-0">No recent bookings</p>
                            </div>
                        @endforelse
                    </div>
                </div>
                @if($recentBookings->count() > 0)
                    <div class="card-footer text-center">
                        <a href="{{ route('creator.analytics') }}" class="text-decoration-none small">
                            View All Bookings <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                @endif
            </div>

            <!-- Quick Tips -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Creator Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Add high-quality images to attract more attendees</span>
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Promote your events on social media</span>
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Respond to attendee questions quickly</span>
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Use analytics to improve future events</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.bg-gradient-success {
    background: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
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
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($analytics['bookings_over_time']->pluck('date')->toArray() ?? []) !!},
            datasets: [{
                label: 'Revenue',
                data: {!! json_encode($analytics['bookings_over_time']->pluck('revenue')->toArray() ?? []) !!},
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Bookings',
                data: {!! json_encode($analytics['bookings_over_time']->pluck('count')->toArray() ?? []) !!},
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            }
        }
    });

    // Events Pie Chart
    const eventsCtx = document.getElementById('eventsPieChart').getContext('2d');
    const eventsData = @json($statuses ?? []);
    
    new Chart(eventsCtx, {
        type: 'doughnut',
        data: {
            labels: eventsData.map(d => d.status),
            datasets: [{
                data: eventsData.map(d => d.count),
                backgroundColor: [
                    'rgb(40, 167, 69)',
                    'rgb(108, 117, 125)',
                    'rgb(220, 53, 69)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush