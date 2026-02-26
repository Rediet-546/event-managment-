<?php

namespace Modules\Attendee\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function show($ticketNumber)
    {
        $ticket = (object)[
            'ticket_number' => $ticketNumber,
            'attendee_name' => 'John Doe',
            'attendee_email' => 'john@example.com',
            'status' => 'active',
            'checked_in_at' => null,
            'created_at' => now(),
            'qr_code_url' => '#',
            'check_in_url' => '#',
            'booking' => (object)[
                'booking_number' => 'BKG202403150001',
                'event' => (object)[
                    'title' => 'Laravel Conference 2024',
                    'start_date' => now()->addDays(30),
                    'venue' => 'Convention Center'
                ],
                'ticketType' => (object)['name' => 'General Admission']
            ]
        ];

        return view('attendee::front.tickets.show', compact('ticket'));
    }

    public function download($ticketNumber)
    {
        return response()->json([
            'success' => true,
            'message' => 'PDF download started'
        ]);
    }

    public function qrCode($ticketNumber)
    {
        return response()->json([
            'success' => true,
            'qr_code' => 'QR_DATA_' . $ticketNumber
        ]);
    }
}