<?php

namespace Modules\Attendee\Http\Requests\Front;

use Illuminate\Foundation\Http\FormRequest;

class FrontBookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if user is logged in (if required by settings)
        $requireLogin = config('attendee.booking.require_login', true);
        
        if ($requireLogin && !auth()->check()) {
            return false;
        }
        
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'ticket_type_id' => 'required|integer|exists:attendee_ticket_types,id',
            'quantity' => [
                'required',
                'integer',
                'min:1',
                'max:' . config('attendee.booking.max_per_order', 10),
                function ($attribute, $value, $fail) {
                    // Check if enough tickets are available
                    $ticketType = \Modules\Attendee\Models\TicketType::find($this->ticket_type_id);
                    if ($ticketType && $ticketType->quantity_available !== null) {
                        $available = $ticketType->quantity_available - $ticketType->bookings()->whereIn('status', ['confirmed', 'pending'])->sum('quantity');
                        if ($value > $available) {
                            $fail("Only {$available} tickets are available.");
                        }
                    }
                },
            ],
            'discount_code' => 'nullable|string|max:50|exists:attendee_discount_codes,code',
            'payment_method' => 'required|string|in:stripe,paypal,bank_transfer',
            'special_requests' => 'nullable|string|max:500',
            'terms_accepted' => 'required|accepted',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'ticket_type_id.required' => 'Please select a ticket type.',
            'ticket_type_id.exists' => 'Selected ticket type is invalid.',
            'quantity.required' => 'Please enter quantity.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed ' . config('attendee.booking.max_per_order', 10) . '.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
            'terms_accepted.required' => 'You must accept the terms and conditions.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('quantity')) {
            $this->merge([
                'quantity' => (int) $this->quantity,
            ]);
        }

        if ($this->has('discount_code')) {
            $this->merge([
                'discount_code' => strtoupper(trim($this->discount_code)),
            ]);
        }
    }

    /**
     * Handle a failed authorization attempt.
     */
    protected function failedAuthorization()
    {
        throw new \Illuminate\Auth\Access\AuthorizationException('You must be logged in to make a booking.');
    }
}