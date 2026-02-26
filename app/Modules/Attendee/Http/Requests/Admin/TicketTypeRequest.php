<?php

namespace Modules\Attendee\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class TicketTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0|max:9999.99',
            'quantity_available' => 'nullable|integer|min:0|max:999999',
            'max_per_order' => 'required|integer|min:1|max:100',
            'min_per_order' => 'required|integer|min:1|max:100',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after_or_equal:sale_start_date',
            'status' => 'required|in:active,inactive',
            'metadata' => 'nullable|json',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Ticket type name is required.',
            'name.max' => 'Ticket type name cannot exceed 255 characters.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price cannot be negative.',
            'max_per_order.required' => 'Maximum per order is required.',
            'max_per_order.min' => 'Maximum per order must be at least 1.',
            'min_per_order.required' => 'Minimum per order is required.',
            'min_per_order.min' => 'Minimum per order must be at least 1.',
            'sale_end_date.after_or_equal' => 'Sale end date must be after or equal to start date.',
            'status.required' => 'Status is required.',
            'status.in' => 'Invalid status selected.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('price')) {
            $this->merge([
                'price' => (float) $this->price,
            ]);
        }

        if ($this->has('quantity_available') && $this->quantity_available === '') {
            $this->merge([
                'quantity_available' => null,
            ]);
        }
    }
}