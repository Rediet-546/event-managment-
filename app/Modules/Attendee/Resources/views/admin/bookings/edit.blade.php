@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Edit Booking - ' . $booking->booking_number)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.attendee.bookings.index') }}">Bookings</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.attendee.bookings.show', $booking) }}">{{ $booking->booking_number }}</a></li>
    <li class="breadcrumb-item active">Edit</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Booking #{{ $booking->booking_number }}</h3>
            </div>
            <form action="{{ route('admin.attendee.bookings.update', $booking) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Booking Number</label>
                                <input type="text" class="form-control" value="{{ $booking->booking_number }}" readonly>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Booking Date</label>
                                <input type="text" class="form-control" value="{{ $booking->created_at->format('Y-m-d H:i:s') }}" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select name="status" id="status" class="form-control @error('status') is-invalid @enderror">
                                    <option value="pending" {{ old('status', $booking->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="confirmed" {{ old('status', $booking->status) == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                    <option value="cancelled" {{ old('status', $booking->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    <option value="refunded" {{ old('status', $booking->status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                @error('status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_status">Payment Status</label>
                                <select name="payment_status" id="payment_status" class="form-control @error('payment_status') is-invalid @enderror">
                                    <option value="pending" {{ old('payment_status', $booking->payment_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="paid" {{ old('payment_status', $booking->payment_status) == 'paid' ? 'selected' : '' }}>Paid</option>
                                    <option value="failed" {{ old('payment_status', $booking->payment_status) == 'failed' ? 'selected' : '' }}>Failed</option>
                                    <option value="refunded" {{ old('payment_status', $booking->payment_status) == 'refunded' ? 'selected' : '' }}>Refunded</option>
                                </select>
                                @error('payment_status')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">Admin Notes</label>
                                <textarea name="notes" id="notes" rows="4" class="form-control">{{ old('notes', $booking->notes) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        To modify ticket quantities or event details, please create a new booking or adjust through the API.
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Booking
                    </button>
                    <a href="{{ route('admin.attendee.bookings.show', $booking) }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Back to Details
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection