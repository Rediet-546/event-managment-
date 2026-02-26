<?php

namespace App\Modules\Registration\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RegistrationMiddleware
{
    /**
     * Handle registration-specific middleware
     */
    public function handle(Request $request, Closure $next, $type = null)
    {
        // Check if registration is enabled
        if (!config('registration.enabled', true)) {
            return redirect()->route('home')
                ->with('error', 'Registration is currently disabled.');
        }

        // Check for specific user type access
        if ($type === 'creator' && !config('registration.allow_creator_registration', true)) {
            return redirect()->route('register')
                ->with('error', 'Event creator registration is currently disabled.');
        }

        // Rate limiting for registration attempts
        if ($type === 'attempt') {
            $key = 'registration_attempt_' . $request->ip();
            
            if (cache($key, 0) >= 3) {
                return redirect()->route('register')
                    ->with('error', 'Too many registration attempts. Please try again later.');
            }
            
            cache([$key => cache($key, 0) + 1], now()->addHours(1));
        }

        // Age verification middleware
        if ($type === 'age-verify' && $request->has('age')) {
            $minAge = config('registration.minimum_age', 18);
            
            if ($request->age < $minAge) {
                return redirect()->back()
                    ->with('error', "You must be at least {$minAge} years old to register.");
            }
        }

        return $next($request);
    }
}