<?php

namespace App\Modules\Events\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Add proper authorization logic
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'exists:event_categories,id'],
            'title' => ['required', 'string', 'max:255', 'unique:events,title'],
            'description' => ['required', 'string'],
            'short_description' => ['nullable', 'string', 'max:500'],
            'venue' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'start_date' => ['required', 'date', 'after:now'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'registration_deadline' => ['nullable', 'date', 'before:start_date'],
            'max_attendees' => ['nullable', 'integer', 'min:1'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'currency' => ['required_with:price', 'string', 'size:3'],
            'is_virtual' => ['boolean'],
            'virtual_link' => ['required_if:is_virtual,true', 'nullable', 'url'],
            'meta_data' => ['nullable', 'array'],
            'media' => ['nullable', 'array'],
            'media.*' => ['image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.unique' => 'An event with this title already exists.',
            'end_date.after' => 'End date must be after the start date.',
            'registration_deadline.before' => 'Registration deadline must be before the event start date.',
            'virtual_link.required_if' => 'Virtual meeting link is required for virtual events.',
        ];
    }
}