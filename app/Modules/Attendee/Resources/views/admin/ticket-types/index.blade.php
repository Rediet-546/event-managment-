@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Ticket Types')

@section('breadcrumb')
    <li class="breadcrumb-item active">Ticket Types</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manage Ticket Types</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.attendee.ticket-types.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Ticket Type
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search ticket types..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 text-right">
                        <div class="btn-group">
                            <a href="{{ route('admin.attendee.ticket-types.index') }}?status=active" 
                               class="btn btn-sm btn-info {{ request('status') == 'active' ? 'active' : '' }}">
                                Active
                            </a>
                            <a href="{{ route('admin.attendee.ticket-types.index') }}?status=inactive" 
                               class="btn btn-sm btn-secondary {{ request('status') == 'inactive' ? 'active' : '' }}">
                                Inactive
                            </a>
                            <a href="{{ route('admin.attendee.ticket-types.index') }}" 
                               class="btn btn-sm btn-default">
                                All
                            </a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Price</th>
                                <th>Available</th>
                                <th>Sold</th>
                                <th>Status</th>
                                <th>Sale Dates</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($ticketTypes as $type)
                            <tr>
                                <td>{{ $type->id }}</td>
                                <td>
                                    <strong>{{ $type->name }}</strong>
                                </td>
                                <td>{{ Str::limit($type->description, 50) }}</td>
                                <td>${{ number_format($type->price, 2) }}</td>
                                <td>
                                    @if($type->quantity_available)
                                        {{ $type->available_quantity ?? 0 }} / {{ $type->quantity_available }}
                                    @else
                                        Unlimited
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $sold = $type->bookings()->where('status', 'confirmed')->sum('quantity');
                                    @endphp
                                    <span class="badge badge-info">{{ $sold }}</span>
                                </td>
                                <td>
                                    @if($type->status == 'active')
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    @if($type->sale_start_date || $type->sale_end_date)
                                        {{ $type->sale_start_date ? $type->sale_start_date->format('M d') : 'Always' }} 
                                        - 
                                        {{ $type->sale_end_date ? $type->sale_end_date->format('M d, Y') : 'No end' }}
                                    @else
                                        <span class="text-muted">Always available</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.attendee.ticket-types.edit', $type) }}" 
                                           class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                data-id="{{ $type->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="9" class="text-center">No ticket types found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        Showing {{ $ticketTypes->firstItem() ?? 0 }} to {{ $ticketTypes->lastItem() ?? 0 }} 
                        of {{ $ticketTypes->total() }} entries
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $ticketTypes->withQueryString()->links() }}
                        </div>
                    </div>
                </div>
            </div>
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
                <p>Are you sure you want to delete this ticket type?</p>
                <p class="text-danger"><small>This action cannot be undone. Bookings using this ticket type will be affected.</small></p>
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
<script>
$(document).ready(function() {
    $('.delete-btn').click(function() {
        var id = $(this).data('id');
        $('#deleteForm').attr('action', '{{ url("admin/attendee/ticket-types") }}/' + id);
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush