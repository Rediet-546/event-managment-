<?php

namespace App\Modules\Events\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'event_id' => 'required|exists:events,id',
            'ticket_type_id' => 'nullable|exists:ticket_types,id',
            'tickets_count' => 'required|integer|min:1|max:10',
            'guests' => 'nullable|array',
            'guests.*.first_name' => 'required_with:guests|string|max:100',
            'guests.*.last_name' => 'required_with:guests|string|max:100',
            'guests.*.email' => 'required_with:guests|email|max:255',
            'guests.*.phone' => 'nullable|string|max:20',
            'guests.*.dietary_restrictions' => 'nullable|string|max:500',
            'guests.*.special_requirements' => 'nullable|string|max:500',
            'payment_method' => 'nullable|in:stripe,paypal,cash,free',
            'special_requests' => 'nullable|string|max:1000',
            'terms_accepted' => 'required|accepted',
        ];
    }

    public function messages(): array
    {
        return [
            'tickets_count.min' => 'You must book at least 1 ticket.',
            'tickets_count.max' => 'Maximum 10 tickets per booking.',
            'guests.*.email.required_with' => 'Email is required for each guest.',
            'terms_accepted.accepted' => 'You must accept the terms and conditions.',
        ];
    }
}