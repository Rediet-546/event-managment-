@extends('attendee::admin.layouts.attendee')

@section('page-title', 'Create New Booking')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.attendee.bookings.index') }}">Bookings</a></li>
    <li class="breadcrumb-item active">Create</li>
@endsection

@section('attendee-content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Create New Booking</h3>
            </div>
            <form action="{{ route('admin.attendee.bookings.store') }}" method="POST" id="bookingForm">
                @csrf
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="event_id">Event <span class="text-danger">*</span></label>
                                <select name="event_id" id="event_id" class="form-control select2 @error('event_id') is-invalid @enderror" required>
                                    <option value="">Select Event</option>
                                    @foreach($events as $event)
                                    <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                                        {{ $event->title }} ({{ $event->start_date->format('M d, Y') }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('event_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="user_id">Customer <span class="text-danger">*</span></label>
                                <select name="user_id" id="user_id" class="form-control select2 @error('user_id') is-invalid @enderror" required>
                                    <option value="">Select Customer</option>
                                    @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('user_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ticket_type_id">Ticket Type <span class="text-danger">*</span></label>
                                <select name="ticket_type_id" id="ticket_type_id" class="form-control @error('ticket_type_id') is-invalid @enderror" required>
                                    <option value="">Select Ticket Type</option>
                                    @foreach($ticketTypes as $type)
                                    <option value="{{ $type->id }}" 
                                            data-price="{{ $type->price }}"
                                            data-available="{{ $type->available_quantity ?? 'unlimited' }}"
                                            {{ old('ticket_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }} - ${{ number_format($type->price, 2) }}
                                        ({{ $type->available_quantity ?? 'Unlimited' }} available)
                                    </option>
                                    @endforeach
                                </select>
                                @error('ticket_type_id')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="quantity">Quantity <span class="text-danger">*</span></label>
                                <input type="number" name="quantity" id="quantity" class="form-control @error('quantity') is-invalid @enderror" 
                                       value="{{ old('quantity', 1) }}" min="1" max="10" required>
                                @error('quantity')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="payment_method">Payment Method <span class="text-danger">*</span></label>
                                <select name="payment_method" id="payment_method" class="form-control @error('payment_method') is-invalid @enderror" required>
                                    <option value="stripe" {{ old('payment_method') == 'stripe' ? 'selected' : '' }}>Stripe</option>
                                    <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                    <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                    <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                                </select>
                                @error('payment_method')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="discount_code">Discount Code</label>
                                <div class="input-group">
                                    <input type="text" name="discount_code" id="discount_code" class="form-control" value="{{ old('discount_code') }}">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-info" id="applyDiscount">
                                            <i class="fas fa-check"></i> Apply
                                        </button>
                                    </div>
                                </div>
                                <small class="text-muted">Enter discount code if available</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="special_requests">Special Requests</label>
                                <textarea name="special_requests" id="special_requests" rows="3" class="form-control">{{ old('special_requests') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="notes">Admin Notes</label>
                                <textarea name="notes" id="notes" rows="3" class="form-control">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Price Summary -->
                    <div class="card bg-light mt-3">
                        <div class="card-header">
                            <h5 class="card-title">Price Summary</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td>Subtotal:</td>
                                    <td class="text-end">$<span id="subtotal">0.00</span></td>
                                </tr>
                                <tr id="discountRow" style="display: none;">
                                    <td>Discount:</td>
                                    <td class="text-end text-success">- $<span id="discountAmount">0.00</span></td>
                                </tr>
                                <tr>
                                    <td>Tax ({{ $settings['tax_rate'] ?? 0 }}%):</td>
                                    <td class="text-end">$<span id="tax">0.00</span></td>
                                </tr>
                                <tr>
                                    <td>Service Fee:</td>
                                    <td class="text-end">$<span id="fee">0.00</span></td>
                                </tr>
                                <tr class="font-weight-bold">
                                    <td>Total:</td>
                                    <td class="text-end">$<span id="total">0.00</span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Create Booking
                    </button>
                    <a href="{{ route('admin.attendee.bookings.index') }}" class="btn btn-secondary">
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
    // Initialize Select2
    $('.select2').select2({
        theme: 'bootstrap4'
    });
    
    // Calculate price on change
    function calculatePrice() {
        const ticketType = $('#ticket_type_id option:selected');
        const price = parseFloat(ticketType.data('price')) || 0;
        const quantity = parseInt($('#quantity').val()) || 0;
        const subtotal = price * quantity;
        
        $('#subtotal').text(subtotal.toFixed(2));
        
        // Get discount if applied
        let discountAmount = 0;
        if ($('#discountRow').is(':visible')) {
            discountAmount = parseFloat($('#discountAmount').text()) || 0;
        }
        
        // Calculate tax and fees
        const taxRate = {{ $settings['tax_rate'] ?? 0 }};
        const feePerTicket = {{ $settings['service_fee'] ?? 0 }};
        
        const taxableAmount = subtotal - discountAmount;
        const tax = taxableAmount * (taxRate / 100);
        const fee = quantity * feePerTicket;
        const total = taxableAmount + tax + fee;
        
        $('#tax').text(tax.toFixed(2));
        $('#fee').text(fee.toFixed(2));
        $('#total').text(total.toFixed(2));
    }
    
    // Event listeners
    $('#ticket_type_id, #quantity').on('change input', calculatePrice);
    
    // Apply discount
    $('#applyDiscount').click(function() {
        const code = $('#discount_code').val();
        if (!code) {
            toastr.warning('Please enter a discount code');
            return;
        }
        
        const ticketTypeId = $('#ticket_type_id').val();
        const quantity = $('#quantity').val();
        const subtotal = parseFloat($('#subtotal').text());
        
        $.ajax({
            url: '{{ route("api.attendee.discount.validate") }}',
            method: 'POST',
            data: {
                code: code,
                ticket_type_id: ticketTypeId,
                quantity: quantity,
                subtotal: subtotal,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.valid) {
                    $('#discountAmount').text(response.amount.toFixed(2));
                    $('#discountRow').show();
                    toastr.success('Discount applied successfully!');
                    calculatePrice();
                } else {
                    $('#discountRow').hide();
                    toastr.error(response.message || 'Invalid discount code');
                }
            },
            error: function() {
                $('#discountRow').hide();
                toastr.error('Failed to validate discount code');
            }
        });
    });
    
    // Initial calculation
    calculatePrice();
});
</script>
@endpush