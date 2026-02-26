@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Edit Email Template - ' . $emailTemplate->name)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.attendee.email-templates.index') }}">Email Templates</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Email Template: {{ $emailTemplate->name }}</h3>
            </div>
            <form action="{{ route('admin.attendee.email-templates.update', $emailTemplate) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Template Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $emailTemplate->name) }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Template Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                    <option value="confirmation" {{ old('type', $emailTemplate->type) == 'confirmation' ? 'selected' : '' }}>Booking Confirmation</option>
                                    <option value="reminder" {{ old('type', $emailTemplate->type) == 'reminder' ? 'selected' : '' }}>Event Reminder</option>
                                    <option value="cancellation" {{ old('type', $emailTemplate->type) == 'cancellation' ? 'selected' : '' }}>Booking Cancellation</option>
                                    <option value="receipt" {{ old('type', $emailTemplate->type) == 'receipt' ? 'selected' : '' }}>Payment Receipt</option>
                                    <option value="custom" {{ old('type', $emailTemplate->type) == 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="subject">Email Subject <span class="text-danger">*</span></label>
                        <input type="text" name="subject" id="subject" class="form-control @error('subject') is-invalid @enderror" 
                               value="{{ old('subject', $emailTemplate->subject) }}" required>
                        @error('subject')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="content">Email Content <span class="text-danger">*</span></label>
                        <textarea name="content" id="content" rows="12" class="form-control @error('content') is-invalid @enderror">{{ old('content', $emailTemplate->content) }}</textarea>
                        @error('content')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="card bg-light">
                        <div class="card-header">
                            <h5 class="card-title">Available Variables</h5>
                        </div>
                        <div class="card-body">
                            <p>You can use these variables in your template:</p>
                            <div class="row">
                                <div class="col-md-4">
                                    <span class="badge badge-info">{booking_number}</span> - Booking Number
                                </div>
                                <div class="col-md-4">
                                    <span class="badge badge-info">{event_name}</span> - Event Name
                                </div>
                                <div class="col-md-4">
                                    <span class="badge badge-info">{event_date}</span> - Event Date
                                </div>
                                <div class="col-md-4">
                                    <span class="badge badge-info">{event_venue}</span> - Event Venue
                                </div>
                                <div class="col-md-4">
                                    <span class="badge badge-info">{attendee_name}</span> - Attendee Name
                                </div>
                                <div class="col-md-4">
                                    <span class="badge badge-info">{attendee_email}</span> - Attendee Email
                                </div>
                                <div class="col-md-4">
                                    <span class="badge badge-info">{ticket_type}</span> - Ticket Type
                                </div>
                                <div class="col-md-4">
                                    <span class="badge badge-info">{quantity}</span> - Ticket Quantity
                                </div>
                                <div class="col-md-4">
                                    <span class="badge badge-info">{total_price}</span> - Total Price
                                </div>
                                <div class="col-md-4">
                                    <span class="badge badge-info">{booking_date}</span> - Booking Date
                                </div>
                                <div class="col-md-4">
                                    <span class="badge badge-info">{check_in_url}</span> - Check-in URL
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" value="1" {{ old('is_active', $emailTemplate->is_active) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_active">Active</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" name="is_default" class="custom-control-input" id="is_default" value="1" {{ old('is_default', $emailTemplate->is_default) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="is_default">Set as Default</label>
                                </div>
                                <small class="text-muted">This will override existing default template for this type</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Template
                    </button>
                    <a href="{{ route('admin.attendee.email-templates.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('attendee-scripts')
<script src="https://cdn.ckeditor.com/4.16.2/standard/ckeditor.js"></script>
<script>
$(document).ready(function() {
    // Initialize CKEditor
    CKEDITOR.replace('content', {
        height: 300,
        toolbar: [
            { name: 'document', items: ['Source', '-', 'Preview'] },
            { name: 'clipboard', items: ['Cut', 'Copy', 'Paste', 'Undo', 'Redo'] },
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'Blockquote'] },
            { name: 'links', items: ['Link', 'Unlink'] },
            { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] }
        ]
    });
});
</script>
@endpush