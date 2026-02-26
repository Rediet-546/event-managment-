<?php

namespace Modules\Attendee\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Modules\Attendee\Models\Booking;
use Modules\Attendee\Models\TicketType;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create($eventId)
    {
        $ticketTypes = TicketType::where('status', 'active')->get();
        
        $event = (object)[
            'id' => $eventId,
            'title' => 'Laravel Conference 2024',
            'description' => 'Annual Laravel Developer Conference',
            'start_date' => now()->addDays(30),
            'end_date' => now()->addDays(31),
            'venue' => 'Convention Center',
            'address' => '123 Main St',
            'city' => 'New York',
            'capacity' => 500,
            'bookings_count' => 150
        ];

        $settings = [
            'tax_rate' => 10,
            'service_fee' => 2.50
        ];

        return view('attendee::front.bookings.create', compact('event', 'ticketTypes', 'settings'));
    }

    public function store(Request $request, $eventId)
    {
        $request->validate([
            'ticket_type_id' => 'required|exists:attendee_ticket_types,id',
            'quantity' => 'required|integer|min:1|max:10',
            'payment_method' => 'required|string',
            'special_requests' => 'nullable|string',
            'terms_accepted' => 'required|accepted'
        ]);

        return redirect()->route('attendee.front.bookings.success', ['booking' => 1])
            ->with('success', 'Booking created successfully!');
    }

    public function success($bookingId)
    {
        $booking = (object)[
            'id' => $bookingId,
            'booking_number' => 'BKG202403150001',
            'event' => (object)[
                'title' => 'Laravel Conference 2024',
                'start_date' => now()->addDays(30),
                'venue' => 'Convention Center'
            ],
            'ticketType' => (object)['name' => 'General Admission'],
            'quantity' => 2,
            'unit_price' => 25.00,
            'final_price' => 60.50,
            'status' => 'confirmed',
            'created_at' => now()
        ];

        return view('attendee::front.bookings.success', compact('booking'));
    }

    public function show($bookingId)
    {
        $booking = (object)[
            'id' => $bookingId,
            'booking_number' => 'BKG202403150001',
            'event' => (object)[
                'title' => 'Laravel Conference 2024',
                'start_date' => now()->addDays(30),
                'venue' => 'Convention Center'
            ],
            'ticketType' => (object)['name' => 'General Admission'],
            'quantity' => 2,
            'unit_price' => 25.00,
            'final_price' => 60.50,
            'status' => 'confirmed',
            'status_label' => '<span class="badge badge-success">Confirmed</span>',
            'created_at' => now(),
            'special_requests' => 'Vegetarian meal preference',
            'payment' => (object)[
                'transaction_id' => 'ch_123456789',
                'payment_method' => 'stripe',
                'amount' => 60.50,
                'status' => 'completed',
                'created_at' => now()
            ],
            'tickets' => [
                (object)[
                    'ticket_number' => 'TIC123456001',
                    'status' => 'active',
                    'checked_in_at' => null,
                    'qr_code_url' => '#'
                ],
                (object)[
                    'ticket_number' => 'TIC123456002',
                    'status' => 'active',
                    'checked_in_at' => null,
                    'qr_code_url' => '#'
                ]
            ],
            'can_be_cancelled' => true,
            'history' => []
        ];

        return view('attendee::front.bookings.show', compact('booking'));
    }

    public function cancel($bookingId)
    {
        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully'
        ]);
    }

    public function validateDiscount(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'ticket_type_id' => 'required|integer',
            'quantity' => 'required|integer',
            'subtotal' => 'required|numeric'
        ]);

        if ($request->code === 'SAVE10') {
            return response()->json([
                'valid' => true,
                'amount' => $request->subtotal * 0.1,
                'message' => '10% discount applied'
            ]);
        }

        return response()->json([
            'valid' => false,
            'message' => 'Invalid discount code'
        ]);
    }
}