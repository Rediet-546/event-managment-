<?php

namespace App\Modules\Registration\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'email' => [
                'required',
                'string',
                'email',
                'exists:users,email'
            ],
            'password' => [
                'required',
                'string'
            ],
            'remember' => [
                'boolean'
            ]
        ];
    }

    public function messages()
    {
        return [
            'email.exists' => 'No account found with this email address.'
        ];
    }

    /**
     * Rate limit for login attempts
     */
    public function ensureIsNotRateLimited()
    {
        $key = 'login:' . $this->ip();

        if (cache($key, 0) >= 5) {
            throw new \Illuminate\Validation\ValidationException(
                $this,
                'Too many login attempts. Please try again in ' . 
                now()->diffInMinutes(cache($key . '_lockout')) . ' minutes.'
            );
        }

        cache([$key => cache($key, 0) + 1], now()->addMinutes(5));
        cache([$key . '_lockout' => now()->addMinutes(15)], now()->addMinutes(15));
    }
}