<?php

namespace App\Modules\Registration\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $user = auth()->user();

        return [
            'first_name' => 'sometimes|string|max:100',
            'last_name' => 'sometimes|string|max:100',
            'email' => [
                'sometimes',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'username' => [
                'sometimes',
                'string',
                'min:3',
                'max:50',
                Rule::unique('users')->ignore($user->id)
            ],
            'phone' => 'nullable|string|max:20',
            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'bio' => 'nullable|string|max:500',
            'organization_name' => 'nullable|string|max:255',
            'tax_id' => 'nullable|string|max:50',
            'preferences' => 'nullable|array',
            'social_links' => 'nullable|array'
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'This email is already taken.',
            'username.unique' => 'This username is already taken.',
            'username.min' => 'Username must be at least 3 characters.'
        ];
    }
}