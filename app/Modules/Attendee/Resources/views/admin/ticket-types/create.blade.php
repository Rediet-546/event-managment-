@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Create Ticket Type')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.attendee.ticket-types.index') }}">Ticket Types</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create New Ticket Type</h3>
            </div>
            <form action="{{ route('admin.attendee.ticket-types.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="price">Price ($) <span class="text-danger">*</span></label>
                                <input type="number" name="price" id="price" class="form-control @error('price') is-invalid @enderror" 
                                       value="{{ old('price', 0) }}" step="0.01" min="0" required>
                                @error('price')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="quantity_available">Quantity Available</label>
                                <input type="number" name="quantity_available" id="quantity_available" class="form-control @error('quantity_available') is-invalid @enderror" 
                                       value="{{ old('quantity_available') }}" min="0" placeholder="Leave empty for unlimited">
                                @error('quantity_available')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="min_per_order">Min Per Order</label>
                                <input type="number" name="min_per_order" id="min_per_order" class="form-control @error('min_per_order') is-invalid @enderror" 
                                       value="{{ old('min_per_order', 1) }}" min="1">
                                @error('min_per_order')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="max_per_order">Max Per Order</label>
                                <input type="number" name="max_per_order" id="max_per_order" class="form-control @error('max_per_order') is-invalid @enderror" 
                                       value="{{ old('max_per_order', 10) }}" min="1">
                                @error('max_per_order')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sale_start_date">Sale Start Date</label>
                                <input type="datetime-local" name="sale_start_date" id="sale_start_date" class="form-control @error('sale_start_date') is-invalid @enderror" 
                                       value="{{ old('sale_start_date') }}">
                                @error('sale_start_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="sale_end_date">Sale End Date</label>
                                <input type="datetime-local" name="sale_end_date" id="sale_end_date" class="form-control @error('sale_end_date') is-invalid @enderror" 
                                       value="{{ old('sale_end_date') }}">
                                @error('sale_end_date')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Ticket Type
                    </button>
                    <a href="{{ route('admin.attendee.ticket-types.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection