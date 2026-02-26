@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Discount Codes')

@section('breadcrumb')
    <li class="breadcrumb-item active">Discount Codes</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manage Discount Codes</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.attendee.discounts.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Discount Code
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search discount codes..." value="{{ request('search') }}">
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
                            <a href="{{ route('admin.attendee.discounts.index') }}?is_active=yes" 
                               class="btn btn-sm btn-success {{ request('is_active') == 'yes' ? 'active' : '' }}">
                                Active
                            </a>
                            <a href="{{ route('admin.attendee.discounts.index') }}?is_active=no" 
                               class="btn btn-sm btn-secondary {{ request('is_active') == 'no' ? 'active' : '' }}">
                                Inactive
                            </a>
                            <a href="{{ route('admin.attendee.discounts.index') }}" 
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
                                <th>Code</th>
                                <th>Type</th>
                                <th>Value</th>
                                <th>Usage</th>
                                <th>Valid From</th>
                                <th>Valid Until</th>
                                <th>Min Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($discountCodes as $discount)
                            <tr>
                                <td>{{ $discount->id }}</td>
                                <td>
                                    <strong>{{ $discount->code }}</strong>
                                </td>
                                <td>
                                    @if($discount->type == 'percentage')
                                        <span class="badge badge-info">Percentage</span>
                                    @else
                                        <span class="badge badge-primary">Fixed</span>
                                    @endif
                                </td>
                                <td>
                                    @if($discount->type == 'percentage')
                                        {{ $discount->value }}%
                                    @else
                                        ${{ number_format($discount->value, 2) }}
                                    @endif
                                </td>
                                <td>
                                    {{ $discount->used_count }} / {{ $discount->usage_limit ?? '∞' }}
                                </td>
                                <td>{{ $discount->valid_from ? $discount->valid_from->format('M d, Y') : 'Always' }}</td>
                                <td>{{ $discount->valid_until ? $discount->valid_until->format('M d, Y') : 'No end' }}</td>
                                <td>
                                    @if($discount->min_order_amount)
                                        ${{ number_format($discount->min_order_amount, 2) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($discount->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.attendee.discounts.edit', $discount) }}" 
                                           class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                data-id="{{ $discount->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">No discount codes found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        Showing {{ $discountCodes->firstItem() ?? 0 }} to {{ $discountCodes->lastItem() ?? 0 }} 
                        of {{ $discountCodes->total() }} entries
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $discountCodes->withQueryString()->links() }}
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
                <p>Are you sure you want to delete this discount code?</p>
                <p class="text-danger"><small>This action cannot be undone.</small></p>
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
        $('#deleteForm').attr('action', '{{ url("admin/attendee/discounts") }}/' + id);
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush