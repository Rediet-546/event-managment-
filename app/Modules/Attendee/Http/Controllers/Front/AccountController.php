<?php

namespace Modules\Attendee\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_bookings' => 5,
            'upcoming_events' => 2,
            'total_spent' => 250.00,
            'recent_bookings' => collect([])
        ];

        return view('attendee::front.account.dashboard', compact('stats'));
    }

    public function bookings()
    {
        $bookings = collect([
            (object)[
                'id' => 1,
                'booking_number' => 'BKG202403150001',
                'event' => (object)[
                    'title' => 'Laravel Conference 2024',
                    'start_date' => now()->addDays(30),
                    'venue' => 'Convention Center'
                ],
                'ticketType' => (object)['name' => 'General Admission'],
                'quantity' => 2,
                'final_price' => 60.50,
                'status' => 'confirmed',
                'status_label' => '<span class="badge badge-success">Confirmed</span>',
                'created_at' => now()->subDays(5)
            ],
            (object)[
                'id' => 2,
                'booking_number' => 'BKG202403150002',
                'event' => (object)[
                    'title' => 'Vue.js Workshop',
                    'start_date' => now()->addDays(15),
                    'venue' => 'Tech Hub'
                ],
                'ticketType' => (object)['name' => 'VIP Pass'],
                'quantity' => 1,
                'final_price' => 75.00,
                'status' => 'confirmed',
                'status_label' => '<span class="badge badge-success">Confirmed</span>',
                'created_at' => now()->subDays(10)
            ]
        ]);

        return view('attendee::front.account.bookings', compact('bookings'));
    }

    public function profile()
    {
        $user = auth()->user();
        return view('attendee::front.account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
        ]);

        $user = auth()->user();
        $user->update($request->only(['name', 'email', 'phone']));

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }
}