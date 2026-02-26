@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Email Templates')

@section('breadcrumb')
    <li class="breadcrumb-item active">Email Templates</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Manage Email Templates</h3>
                <div class="card-tools">
                    <a href="{{ route('admin.attendee.email-templates.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus"></i> New Template
                    </a>
                </div>
            </div>
            
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" class="form-inline">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Search templates..." value="{{ request('search') }}">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-6 text-right">
                        <select name="type" class="form-control" style="width: 200px; display: inline-block;" onchange="window.location.href=this.value">
                            <option value="{{ route('admin.attendee.email-templates.index') }}">All Types</option>
                            <option value="{{ route('admin.attendee.email-templates.index') }}?type=confirmation" {{ request('type') == 'confirmation' ? 'selected' : '' }}>Confirmation</option>
                            <option value="{{ route('admin.attendee.email-templates.index') }}?type=reminder" {{ request('type') == 'reminder' ? 'selected' : '' }}>Reminder</option>
                            <option value="{{ route('admin.attendee.email-templates.index') }}?type=cancellation" {{ request('type') == 'cancellation' ? 'selected' : '' }}>Cancellation</option>
                            <option value="{{ route('admin.attendee.email-templates.index') }}?type=receipt" {{ request('type') == 'receipt' ? 'selected' : '' }}>Receipt</option>
                            <option value="{{ route('admin.attendee.email-templates.index') }}?type=custom" {{ request('type') == 'custom' ? 'selected' : '' }}>Custom</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead class="thead-dark">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Subject</th>
                                <th>Variables</th>
                                <th>Default</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($templates as $template)
                            <tr>
                                <td>{{ $template->id }}</td>
                                <td>
                                    <strong>{{ $template->name }}</strong>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ ucfirst($template->type) }}</span>
                                </td>
                                <td>{{ $template->subject }}</td>
                                <td>
                                    @if($template->variables)
                                        <span class="badge badge-secondary" title="{{ implode(', ', $template->variables) }}">
                                            {{ count($template->variables) }} variables
                                        </span>
                                    @else
                                        <span class="badge badge-light">None</span>
                                    @endif
                                </td>
                                <td>
                                    @if($template->is_default)
                                        <span class="badge badge-success">Default</span>
                                    @endif
                                </td>
                                <td>
                                    @if($template->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <a href="{{ route('admin.attendee.email-templates.edit', $template) }}" 
                                           class="btn btn-sm btn-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.attendee.email-templates.preview', $template) }}" 
                                           class="btn btn-sm btn-info" title="Preview" target="_blank">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if(!$template->is_default)
                                        <button type="button" class="btn btn-sm btn-danger delete-btn" 
                                                data-id="{{ $template->id }}" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No email templates found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        Showing {{ $templates->firstItem() ?? 0 }} to {{ $templates->lastItem() ?? 0 }} 
                        of {{ $templates->total() }} entries
                    </div>
                    <div class="col-md-6">
                        <div class="float-right">
                            {{ $templates->withQueryString()->links() }}
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
                <p>Are you sure you want to delete this email template?</p>
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
        $('#deleteForm').attr('action', '{{ url("admin/attendee/email-templates") }}/' + id);
        $('#deleteModal').modal('show');
    });
});
</script>
@endpush