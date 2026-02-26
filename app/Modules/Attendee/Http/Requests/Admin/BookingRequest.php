<?php

namespace Modules\Attendee\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BookingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rules = [
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id',
            'ticket_type_id' => 'required|exists:attendee_ticket_types,id',
            'quantity' => 'required|integer|min:1|max:10',
            'payment_method' => 'required|string|in:stripe,paypal,bank_transfer,cash',
            'special_requests' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];

        // For update requests
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules = [
                'status' => 'sometimes|in:pending,confirmed,cancelled,refunded,expired',
                'payment_status' => 'sometimes|in:pending,paid,failed,refunded',
                'notes' => 'nullable|string|max:1000',
            ];
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'event_id.required' => 'Please select an event.',
            'event_id.exists' => 'The selected event is invalid.',
            'user_id.required' => 'Please select a customer.',
            'user_id.exists' => 'The selected customer is invalid.',
            'ticket_type_id.required' => 'Please select a ticket type.',
            'ticket_type_id.exists' => 'The selected ticket type is invalid.',
            'quantity.required' => 'Please enter quantity.',
            'quantity.min' => 'Quantity must be at least 1.',
            'quantity.max' => 'Quantity cannot exceed 10.',
            'payment_method.required' => 'Please select a payment method.',
            'payment_method.in' => 'Invalid payment method selected.',
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
    }
}