@extends('layouts.app')

@section('title', 'Book Tickets - ' . $event->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 rounded-lg">
                <div class="card-header bg-primary text-white py-3">
                    <h4 class="mb-0"><i class="fas fa-ticket-alt mr-2"></i>Book Tickets</h4>
                </div>
                
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('attendee.front.bookings.store', $event->id) }}" id="bookingForm">
                        @csrf
                        
                        <!-- Ticket Type Selection -->
                        <div class="form-group mb-4">
                            <label class="form-label font-weight-bold">Select Ticket Type</label>
                            @foreach($ticketTypes as $type)
                            <div class="ticket-option border rounded p-3 mb-2 {{ $type->available_quantity === 0 ? 'disabled' : '' }}" 
                                 data-type-id="{{ $type->id }}"
                                 data-price="{{ $type->price }}"
                                 data-max="{{ $type->max_per_order }}"
                                 data-available="{{ $type->available_quantity ?? 'unlimited' }}">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="ticket_{{ $type->id }}" 
                                                   name="ticket_type_id" value="{{ $type->id }}"
                                                   class="custom-control-input ticket-radio"
                                                   {{ $loop->first ? 'checked' : '' }}
                                                   {{ $type->available_quantity === 0 ? 'disabled' : '' }}
                                                   required>
                                            <label class="custom-control-label font-weight-bold" for="ticket_{{ $type->id }}">
                                                {{ $type->name }}
                                            </label>
                                        </div>
                                        <p class="text-muted small mb-0">{{ $type->description }}</p>
                                        @if($type->available_quantity !== null)
                                            <span class="badge badge-{{ $type->available_quantity > 0 ? 'success' : 'danger' }} mt-1">
                                                {{ $type->available_quantity }} seats available
                                            </span>
                                        @endif
                                    </div>
                                    <div class="col-md-4 text-right">
                                        <span class="h5 text-primary">${{ number_format($type->price, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                            @error('ticket_type_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Quantity Selection -->
                        <div class="form-group mb-4">
                            <label for="quantity" class="font-weight-bold">Number of Tickets</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="number" name="quantity" id="quantity" 
                                           class="form-control form-control-lg @error('quantity') is-invalid @enderror"
                                           value="{{ old('quantity', 1) }}" min="1" max="10" required>
                                </div>
                            </div>
                            @error('quantity')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maximum 10 tickets per order</small>
                        </div>

                        <!-- Discount Code -->
                        <div class="form-group mb-4">
                            <label for="discount_code" class="font-weight-bold">Discount Code</label>
                            <div class="input-group">
                                <input type="text" name="discount_code" id="discount_code" 
                                       class="form-control" placeholder="Enter discount code">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-info" id="applyDiscount">
                                        <i class="fas fa-check"></i> Apply
                                    </button>
                                </div>
                            </div>
                            <div id="discountMessage" class="small mt-1"></div>
                        </div>

                        <!-- Payment Method -->
                        <div class="form-group mb-4">
                            <label class="font-weight-bold">Payment Method</label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="payment_stripe" name="payment_method" 
                                               value="stripe" class="custom-control-input" checked>
                                        <label class="custom-control-label" for="payment_stripe">
                                            <i class="fab fa-cc-stripe text-primary"></i> Credit Card
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="payment_paypal" name="payment_method" 
                                               value="paypal" class="custom-control-input">
                                        <label class="custom-control-label" for="payment_paypal">
                                            <i class="fab fa-cc-paypal text-info"></i> PayPal
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="payment_bank" name="payment_method" 
                                               value="bank_transfer" class="custom-control-input">
                                        <label class="custom-control-label" for="payment_bank">
                                            <i class="fas fa-university text-success"></i> Bank Transfer
                                        </label>
                                    </div>
                                </div>
                            </div>
                            @error('payment_method')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Special Requests -->
                        <div class="form-group mb-4">
                            <label for="special_requests" class="font-weight-bold">Special Requests</label>
                            <textarea name="special_requests" id="special_requests" rows="3" 
                                      class="form-control" placeholder="Any dietary requirements, accessibility needs, or special requests?">{{ old('special_requests') }}</textarea>
                        </div>

                        <!-- Terms and Conditions -->
                        <div class="form-group mb-4">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="terms_accepted" class="custom-control-input" id="terms" value="1" {{ old('terms_accepted') ? 'checked' : '' }} required>
                                <label class="custom-control-label" for="terms">
                                    I accept the <a href="#" data-toggle="modal" data-target="#termsModal">terms and conditions</a>
                                </label>
                            </div>
                            @error('terms_accepted')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Price Summary -->
                        <div class="card bg-light mb-4">
                            <div class="card-body">
                                <h5 class="card-title">Price Summary</h5>
                                <table class="table table-sm table-borderless mb-0">
                                    <tr>
                                        <td>Subtotal:</td>
                                        <td class="text-right">$<span id="subtotal">0.00</span></td>
                                    </tr>
                                    <tr id="discountRow" style="display: none;">
                                        <td>Discount:</td>
                                        <td class="text-right text-success">- $<span id="discountAmount">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td>Service Fee:</td>
                                        <td class="text-right">$<span id="serviceFee">0.00</span></td>
                                    </tr>
                                    <tr>
                                        <td>Tax ({{ $settings['tax_rate'] ?? 0 }}%):</td>
                                        <td class="text-right">$<span id="tax">0.00</span></td>
                                    </tr>
                                    <tr class="font-weight-bold">
                                        <td>Total:</td>
                                        <td class="text-right text-primary">$<span id="total">0.00</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-lock mr-2"></i> Proceed to Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Event Details Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-lg border-0 rounded-lg sticky-top" style="top: 20px;">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar-alt mr-2"></i>Event Details</h5>
                </div>
                <div class="card-body">
                    <h4 class="text-primary">{{ $event->title }}</h4>
                    
                    <hr>
                    
                    <div class="mb-3">
                        <i class="fas fa-calendar text-primary mr-2"></i>
                        <strong>Date:</strong><br>
                        {{ $event->start_date->format('l, F d, Y') }}<br>
                        {{ $event->start_date->format('h:i A') }} - {{ $event->end_date->format('h:i A') }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-map-marker-alt text-primary mr-2"></i>
                        <strong>Venue:</strong><br>
                        {{ $event->venue }}<br>
                        {{ $event->address }}, {{ $event->city }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-users text-primary mr-2"></i>
                        <strong>Capacity:</strong><br>
                        <div class="progress" style="height: 20px;">
                            @php
                                $bookedPercentage = min(100, ($event->bookings_count / $event->capacity) * 100);
                            @endphp
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: {{ $bookedPercentage }}%;" 
                                 aria-valuenow="{{ $bookedPercentage }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $event->bookings_count }}/{{ $event->capacity }}
                            </div>
                        </div>
                    </div>
                    
                    @if($event->description)
                    <div class="mb-3">
                        <i class="fas fa-info-circle text-primary mr-2"></i>
                        <strong>Description:</strong><br>
                        <p class="text-muted small">{{ $event->description }}</p>
                    </div>
                    @endif
                    
                    <hr>
                    
                    <div class="text-center">
                        <i class="fas fa-shield-alt text-success fa-2x mb-2"></i>
                        <p class="small text-muted mb-0">Secure checkout powered by Stripe</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Terms Modal -->
<div class="modal fade" id="termsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Terms and Conditions</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h6>1. Ticket Validity</h6>
                <p>Tickets are valid only for the event date and time specified. Tickets cannot be rescheduled.</p>
                
                <h6>2. Cancellation Policy</h6>
                <p>Cancellations made 7 days before the event receive a full refund. Cancellations within 7 days receive a 50% refund. No refunds within 24 hours of the event.</p>
                
                <h6>3. Code of Conduct</h6>
                <p>All attendees must follow the event's code of conduct. Management reserves the right to remove any attendee without refund for inappropriate behavior.</p>
                
                <h6>4. Data Privacy</h6>
                <p>Your personal information will be used only for event communication and will not be shared with third parties.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">I Understand</button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.ticket-option {
    cursor: pointer;
    transition: all 0.2s;
}
.ticket-option:hover {
    border-color: #007bff !important;
    background-color: #f0f7ff;
}
.ticket-option.selected {
    border-color: #007bff !important;
    background-color: #e3f2fd;
}
.ticket-option.disabled {
    opacity: 0.6;
    cursor: not-allowed;
    background-color: #f8f9fa;
}
.sticky-top {
    z-index: 1020;
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let selectedTicket = null;
    let discountApplied = false;
    let discountAmount = 0;
    
    const taxRate = {{ $settings['tax_rate'] ?? 0 }};
    const serviceFee = {{ $settings['service_fee'] ?? 0 }};
    
    // Ticket selection
    $('.ticket-option').click(function() {
        if ($(this).hasClass('disabled')) return;
        
        $('.ticket-option').removeClass('selected border-primary');
        $(this).addClass('selected border-primary');
        
        const radio = $(this).find('.ticket-radio');
        radio.prop('checked', true);
        
        selectedTicket = {
            id: radio.val(),
            price: parseFloat($(this).data('price')),
            max: $(this).data('max'),
            available: $(this).data('available')
        };
        
        updatePrice();
    });
    
    // Quantity change
    $('#quantity').on('input', function() {
        const qty = parseInt($(this).val()) || 1;
        if (selectedTicket && selectedTicket.available !== 'unlimited' && qty > selectedTicket.available) {
            $(this).val(selectedTicket.available);
            toastr.warning('Only ' + selectedTicket.available + ' tickets available');
        }
        updatePrice();
    });
    
    // Apply discount
    $('#applyDiscount').click(function() {
        const code = $('#discount_code').val();
        if (!code) {
            toastr.warning('Please enter a discount code');
            return;
        }
        
        if (!selectedTicket) {
            toastr.warning('Please select a ticket type first');
            return;
        }
        
        const quantity = $('#quantity').val();
        const subtotal = selectedTicket.price * quantity;
        
        $.ajax({
            url: '{{ route("attendee.front.discount.validate") }}',
            method: 'POST',
            data: {
                code: code,
                ticket_type_id: selectedTicket.id,
                quantity: quantity,
                subtotal: subtotal,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.valid) {
                    discountApplied = true;
                    discountAmount = response.amount;
                    $('#discountRow').show();
                    $('#discountAmount').text(discountAmount.toFixed(2));
                    $('#discountMessage').html('<span class="text-success"><i class="fas fa-check"></i> ' + response.message + '</span>');
                    updatePrice();
                } else {
                    discountApplied = false;
                    discountAmount = 0;
                    $('#discountRow').hide();
                    $('#discountMessage').html('<span class="text-danger"><i class="fas fa-times"></i> ' + response.message + '</span>');
                }
            },
            error: function() {
                toastr.error('Failed to validate discount code');
            }
        });
    });
    
    // Update price calculation
    function updatePrice() {
        if (!selectedTicket) return;
        
        const quantity = parseInt($('#quantity').val()) || 1;
        const subtotal = selectedTicket.price * quantity;
        
        $('#subtotal').text(subtotal.toFixed(2));
        
        const taxableAmount = subtotal - discountAmount;
        const tax = taxableAmount * (taxRate / 100);
        const fee = quantity * serviceFee;
        const total = taxableAmount + tax + fee;
        
        $('#tax').text(tax.toFixed(2));
        $('#serviceFee').text(fee.toFixed(2));
        $('#total').text(total.toFixed(2));
    }
    
    // Trigger initial calculation
    $('.ticket-option:not(.disabled)').first().click();
});
</script>
@endpush
@endsection