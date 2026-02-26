@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Create Discount Code')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.attendee.discounts.index') }}">Discount Codes</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create New Discount Code</h3>
            </div>
            <form action="{{ route('admin.attendee.discounts.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">Discount Code <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" 
                                           value="{{ old('code') }}" required placeholder="e.g., SUMMER2024">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info" id="generateCode">
                                            <i class="fas fa-random"></i> Generate
                                        </button>
                                    </div>
                                </div>
                                @error('code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Discount Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                    <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="fixed" {{ old('type') == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="value">Discount Value <span class="text-danger">*</span></label>
                                <input type="number" name="value" id="value" class="form-control @error('value') is-invalid @enderror" 
                                       value="{{ old('value') }}" step="0.01" min="0" required>
                                @error('value')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_order_amount">Minimum Order Amount</label>
                                <input type="number" name="min_order_amount" id="min_order_amount" class="form-control @error('min_order_amount') is-invalid @enderror" 
                                       value="{{ old('min_order_amount') }}" step="0.01" min="0" placeholder="Leave empty for no minimum">
                                @error('min_order_amount')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="usage_limit">Usage Limit</label>
                                <input type="number" name="usage_limit" id="usage_limit" class="form-control @error('usage_limit') is-invalid @enderror" 
                                       value="{{ old('usage_limit') }}" min="1" placeholder="Leave empty for unlimited">
                                @error('usage_limit')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valid_from">Valid From</label>
                                <input type="datetime-local" name="valid_from" id="valid_from" class="form-control @error('valid_from') is-invalid @enderror" 
                                       value="{{ old('valid_from') }}">
                                @error('valid_from')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valid_until">Valid Until</label>
                                <input type="datetime-local" name="valid_until" id="valid_until" class="form-control @error('valid_until') is-invalid @enderror" 
                                       value="{{ old('valid_until') }}">
                                @error('valid_until')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Describe the purpose of this discount code">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Discount Code
                    </button>
                    <a href="{{ route('admin.attendee.discounts.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('attendee-scripts')
<script>
$(document).ready(function() {
    // Generate random discount code
    $('#generateCode').click(function() {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        let code = '';
        for (let i = 0; i < 8; i++) {
            code += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        $('#code').val(code);
    });
    
    // Update value label based on type
    $('#type').change(function() {
        const type = $(this).val();
        const label = type === 'percentage' ? 'Percentage (%)' : 'Fixed Amount ($)';
        $('label[for="value"]').text('Discount Value (' + (type === 'percentage' ? '%' : '$') + ')');
    });
});
</script>
@endpush