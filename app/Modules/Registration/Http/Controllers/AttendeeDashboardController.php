<?php

namespace App\Modules\Registration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\Events\Models\Booking;
use App\Modules\Events\Models\Event;

class AttendeeDashboardController extends Controller
{
    /**
     * Display the attendee dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get attendee's bookings
        $recentBookings = Booking::where('user_id', $user->id)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Calculate statistics
        $statistics = [
            'total_bookings' => Booking::where('user_id', $user->id)->count(),
            'upcoming_events' => Booking::where('user_id', $user->id)
                ->whereHas('event', function($q) {
                    $q->where('start_date', '>', now());
                })
                ->count(),
            'total_spent' => Booking::where('user_id', $user->id)
                ->where('status', 'confirmed')
                ->sum('amount_paid'),
            'past_events' => Booking::where('user_id', $user->id)
                ->whereHas('event', function($q) {
                    $q->where('end_date', '<', now());
                })
                ->count(),
        ];
        
        // Get upcoming events (for recommendations)
        $recommendedEvents = Event::where('status', 'published')
            ->where('start_date', '>', now())
            ->inRandomOrder()
            ->limit(3)
            ->get();
        
        return view('registration::attendee.dashboard', compact(
            'user', 
            'recentBookings', 
            'statistics', 
            'recommendedEvents'
        ));
    }

    /**
     * Display all attendee bookings.
     */
    public function bookings()
    {
        $user = Auth::user();
        
        $bookings = Booking::where('user_id', $user->id)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('registration::attendee.bookings', compact('bookings'));
    }

    /**
     * Display attendee wallet/transactions.
     */
    public function wallet()
    {
        $user = Auth::user();
        
        $transactions = Booking::where('user_id', $user->id)
            ->where('status', 'confirmed')
            ->orderBy('created_at', 'desc')
            ->get();
        
        $totalSpent = $transactions->sum('amount_paid');
        $totalBookings = $transactions->count();
        
        return view('registration::attendee.wallet', compact(
            'user', 
            'transactions', 
            'totalSpent', 
            'totalBookings'
        ));
    }

    /**
     * Show booking details.
     */
    public function showBooking($bookingId)
    {
        $user = Auth::user();
        
        $booking = Booking::where('user_id', $user->id)
            ->with(['event', 'guests'])
            ->findOrFail($bookingId);
        
        return view('registration::attendee.booking-details', compact('booking'));
    }

    /**
     * Cancel a booking.
     */
    public function cancelBooking(Request $request, $bookingId)
    {
        $user = Auth::user();
        
        $booking = Booking::where('user_id', $user->id)->findOrFail($bookingId);
        
        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Booking is already cancelled.');
        }
        
        $request->validate([
            'reason' => 'nullable|string|max:255'
        ]);
        
        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $request->reason
        ]);
        
        // Update event attendee count
        $booking->event->decrement('current_attendees', $booking->tickets_count);
        
        return redirect()->route('attendee.bookings')
            ->with('success', 'Booking cancelled successfully.');
    }
}