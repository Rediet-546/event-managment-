<?php

namespace Modules\Attendee\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Modules\Attendee\Models\Booking;
use Illuminate\Http\Request;

class AttendeeController extends Controller
{
    /**
     * Display a listing of attendees
     */
    public function index(Request $request)
    {
        $query = User::whereHas('bookings', function ($q) {
            $q->where('status', 'confirmed');
        })->withCount(['bookings' => function ($q) {
            $q->where('status', 'confirmed');
        }]);

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $attendees = $query->paginate(20);

        $stats = [
            'total' => $attendees->total(),
            'checked_in' => Booking::whereNotNull('checked_in_at')->count(),
            'upcoming' => Booking::whereHas('event', function ($q) {
                $q->where('start_date', '>', now());
            })->where('status', 'confirmed')->count(),
        ];

        return view('attendee::admin.attendees.index', compact('attendees', 'stats', 'request'));
    }

    /**
     * Display the specified attendee
     */
    public function show(User $user)
    {
        $user->load(['bookings' => function ($q) {
            $q->with(['event', 'ticketType'])->latest();
        }]);

        $stats = [
            'total_bookings' => $user->bookings()->count(),
            'confirmed_bookings' => $user->bookings()->where('status', 'confirmed')->count(),
            'total_spent' => $user->bookings()->where('status', 'confirmed')->sum('final_price'),
            'upcoming_events' => $user->bookings()
                ->whereHas('event', function ($q) {
                    $q->where('start_date', '>', now());
                })
                ->where('status', 'confirmed')
                ->count(),
        ];

        return view('attendee::admin.attendees.show', compact('user', 'stats'));
    }
}