<?php

namespace App\Modules\Events\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $event = $this->route('event');
        return auth()->user()->can('update', $event);
    }

    public function rules(): array
    {
        return [
            'category_id' => ['sometimes', 'exists:event_categories,id'],
            'title' => ['sometimes', 'string', 'max:255', 
                Rule::unique('events')->ignore($this->event)],
            'description' => ['sometimes', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'venue' => ['sometimes', 'string', 'max:255'],
            'address' => ['sometimes', 'string', 'max:255'],
            'city' => ['sometimes', 'string', 'max:100'],
            'country' => ['sometimes', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'start_date' => ['sometimes', 'date', 'after:now'],
            'end_date' => ['sometimes', 'date', 'after:start_date'],
            'registration_deadline' => ['nullable', 'date', 'before:start_date'],
            'max_attendees' => ['nullable', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required_with:price', 'string', 'size:3'],
            'is_virtual' => ['boolean'],
            'virtual_link' => ['required_if:is_virtual,true', 'nullable', 'url'],
            'status' => ['sometimes', Rule::in(['draft', 'published', 'cancelled'])],
            'meta_data' => ['nullable', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'end_date.after' => 'End date must be after the start date.',
            'registration_deadline.before' => 'Registration deadline must be before the event start date.',
            'virtual_link.required_if' => 'Virtual meeting link is required for virtual events.',
        ];
    }
}