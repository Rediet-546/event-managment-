<?php

namespace Modules\Attendee\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DiscountCodeRequest extends FormRequest
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
        $rules = [
            'code' => 'required|string|max:50|unique:attendee_discount_codes,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'usage_limit' => 'nullable|integer|min:1|max:999999',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after_or_equal:valid_from',
            'min_order_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
        ];

        // For update requests
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            $rules['code'] = 'required|string|max:50|unique:attendee_discount_codes,code,' . $this->discountCode->id;
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'code.required' => 'Discount code is required.',
            'code.unique' => 'This discount code already exists.',
            'code.max' => 'Discount code cannot exceed 50 characters.',
            'type.required' => 'Please select discount type.',
            'type.in' => 'Invalid discount type selected.',
            'value.required' => 'Discount value is required.',
            'value.numeric' => 'Discount value must be a number.',
            'value.min' => 'Discount value cannot be negative.',
            'usage_limit.min' => 'Usage limit must be at least 1.',
            'valid_until.after_or_equal' => 'Valid until date must be after or equal to valid from date.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('is_active')) {
            $this->merge([
                'is_active' => filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN),
            ]);
        }

        // Generate random code if not provided
        if ($this->isMethod('POST') && !$this->has('code')) {
            $this->merge([
                'code' => $this->generateDiscountCode(),
            ]);
        }
    }

    /**
     * Generate a random discount code.
     */
    protected function generateDiscountCode(): string
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < 8; $i++) {
            $code .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $code;
    }
}