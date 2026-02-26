<?php

namespace App\Modules\Registration\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'first_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s]+$/'
            ],
            'last_name' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-zA-Z\s]+$/'
            ],
            'username' => [
                'required',
                'string',
                'min:3',
                'max:50',
                'alpha_dash',
                'unique:users,username'
            ],
            'email' => [
                'required',
                'string',
                'email:rfc,dns',
                'max:255',
                'unique:users,email'
            ],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            'age' => [
                'required',
                'integer',
                'min:18',
                'max:120'
            ],
            'terms' => [
                'required',
                'accepted'
            ]
        ];
    }

    public function messages()
    {
        return [
            'first_name.regex' => 'First name may only contain letters and spaces.',
            'last_name.regex' => 'Last name may only contain letters and spaces.',
            'username.alpha_dash' => 'Username may only contain letters, numbers, dashes and underscores.',
            'username.min' => 'Username must be at least 3 characters.',
            'age.min' => 'You must be at least 18 years old to register.',
            'age.max' => 'Please enter a valid age.',
            'terms.accepted' => 'You must accept the terms and conditions.',
            'password.uncompromised' => 'This password has been exposed in data breaches. Please choose a different password.'
        ];
    }

    public function attributes()
    {
        return [
            'first_name' => 'first name',
            'last_name' => 'last name',
            'username' => 'username',
            'email' => 'email address',
            'password' => 'password',
            'age' => 'age',
            'terms' => 'terms and conditions'
        ];
    }

    /**
     * Prepare the data for validation
     */
    protected function prepareForValidation()
    {
        $this->merge([
            'email' => strtolower($this->email),
            'username' => strtolower($this->username)
        ]);
    }
}