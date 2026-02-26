<?php

namespace Modules\Attendee\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Attendee\Models\Booking;

class CheckBookingAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $booking = $request->route('booking');
        
        if (!$booking) {
            return $next($request);
        }
        
        // Admin can access any booking
        if (auth()->user() && auth()->user()->hasRole('admin')) {
            return $next($request);
        }
        
        // Check if booking belongs to the authenticated user
        if ($booking->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this booking.');
        }
        
        return $next($request);
    }
}