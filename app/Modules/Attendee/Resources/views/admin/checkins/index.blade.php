@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Check-in History')

@section('breadcrumb')
    <li class="breadcrumb-item active">Check-ins</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Check-in History</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.attendee.checkins.scan') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-qrcode"></i> Scan QR Code
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-8">
                        <form method="GET" class="form-inline">
                            <div class="input-group mr-2">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search by ticket or booking #..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="date" name="date_from" class="form-control mr-2" value="{{ request('date_from') }}" placeholder="From">
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
                        </form>
                    </div>
                    <div class="col-md-4 text-right">
                        <a href="{{ route('admin.attendee.checkins.export') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-download"></i> Export
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Ticket Number</th>
                                <th>Attendee</th>
                                <th>Event</th>
                                <th>Check-in Time</th>
                                <th>Method</th>
                                <th>Checked-in By</th>
                                <th>IP Address</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($checkIns as $checkin)
                            <tr>
                                <td>{{ $checkin->id }}</td>
                                <td>
                                    <a href="{{ route('admin.attendee.tickets.show', $checkin->ticket_id) }}">
                                        {{ $checkin->ticket->ticket_number ?? 'N/A' }}
                                    </a>
                                </td>
                                <td>
                                    {{ $checkin->booking->user->name ?? 'N/A' }}<br>
                                    <small>{{ $checkin->booking->user->email ?? '' }}</small>
                                </td>
                                <td>{{ $checkin->booking->event->title ?? 'N/A' }}</td>
                                <td>{{ $checkin->checked_in_at->format('M d, Y - h:i A') }}</td>
                                <td>
                                    @if($checkin->method == 'qr')
                                        <span class="badge badge-success">QR Code</span>
                                    @elseif($checkin->method == 'manual')
                                        <span class="badge badge-warning">Manual</span>
                                    @elseif($checkin->method == 'api')
                                        <span class="badge badge-info">API</span>
                                    @else
                                        <span class="badge badge-secondary">{{ $checkin->method }}</span>
                                    @endif
                                </td>
                                <td>{{ $checkin->checker->name ?? 'System' }}</td>
                                <td><code>{{ $checkin->ip_address ?? 'N/A' }}</code></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No check-ins found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        Showing {{ $checkIns->firstItem() ?? 0 }} to {{ $checkIns->lastItem() ?? 0 }} 
                        of {{ $checkIns->total() }} entries
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $checkIns->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection