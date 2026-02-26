@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Edit Discount Code - ' . $discountCode->code)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.attendee.discounts.index') }}">Discount Codes</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Discount Code: {{ $discountCode->code }}</h3>
            </div>
            <form action="{{ route('admin.attendee.discounts.update', $discountCode) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="code">Discount Code <span class="text-danger">*</span></label>
                                <input type="text" name="code" id="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $discountCode->code) }}" required>
                                @error('code')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type">Discount Type <span class="text-danger">*</span></label>
                                <select name="type" id="type" class="form-control @error('type') is-invalid @enderror" required>
                                    <option value="percentage" {{ old('type', $discountCode->type) == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                    <option value="fixed" {{ old('type', $discountCode->type) == 'fixed' ? 'selected' : '' }}>Fixed Amount ($)</option>
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
                                       value="{{ old('value', $discountCode->value) }}" step="0.01" min="0" required>
                                @error('value')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_order_amount">Minimum Order Amount</label>
                                <input type="number" name="min_order_amount" id="min_order_amount" class="form-control @error('min_order_amount') is-invalid @enderror" 
                                       value="{{ old('min_order_amount', $discountCode->min_order_amount) }}" step="0.01" min="0" placeholder="Leave empty for no minimum">
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
                                       value="{{ old('usage_limit', $discountCode->usage_limit) }}" min="1" placeholder="Leave empty for unlimited">
                                @error('usage_limit')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                                <small class="text-muted">Currently used: {{ $discountCode->used_count }} times</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valid_from">Valid From</label>
                                <input type="datetime-local" name="valid_from" id="valid_from" class="form-control @error('valid_from') is-invalid @enderror" 
                                       value="{{ old('valid_from', $discountCode->valid_from ? $discountCode->valid_from->format('Y-m-d\TH:i') : '') }}">
                                @error('valid_from')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="valid_until">Valid Until</label>
                                <input type="datetime-local" name="valid_until" id="valid_until" class="form-control @error('valid_until') is-invalid @enderror" 
                                       value="{{ old('valid_until', $discountCode->valid_until ? $discountCode->valid_until->format('Y-m-d\TH:i') : '') }}">
                                @error('valid_until')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $discountCode->description) }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" name="is_active" class="custom-control-input" id="is_active" value="1" {{ old('is_active', $discountCode->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Active</label>
                        </div>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Usage Stats:</strong> Used {{ $discountCode->used_count }} times
                        @if($discountCode->usage_limit)
                            ({{ $discountCode->usage_limit - $discountCode->used_count }} remaining)
                        @endif
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Discount Code
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
    // Update value label based on type
    $('#type').change(function() {
        const type = $(this).val();
        const label = type === 'percentage' ? 'Percentage (%)' : 'Fixed Amount ($)';
        $('label[for="value"]').text('Discount Value (' + (type === 'percentage' ? '%' : '$') + ')');
    });
});
</script>
@endpush