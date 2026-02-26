<?php

namespace Modules\Attendee\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Attendee\Models\Ticket;
use Modules\Attendee\Models\CheckIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CheckInApiController extends Controller
{
    /**
     * Scan a ticket QR code
     */
    public function scan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $ticket = Ticket::with(['booking.event', 'booking.user'])
            ->where('qr_code', $request->qr_code)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ticket QR code'
            ], 404);
        }

        if ($ticket->checked_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket already used',
                'checked_in_at' => $ticket->checked_in_at
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ticket' => [
                    'id' => $ticket->id,
                    'ticket_number' => $ticket->ticket_number,
                    'attendee_name' => $ticket->attendee_name,
                    'attendee_email' => $ticket->attendee_email,
                    'booking' => [
                        'booking_number' => $ticket->booking->booking_number,
                        'event' => [
                            'title' => $ticket->booking->event->title,
                            'start_date' => $ticket->booking->event->start_date,
                            'venue' => $ticket->booking->event->venue
                        ]
                    ]
                ],
                'can_check_in' => true
            ]
        ]);
    }

    /**
     * Process a check-in
     */
    public function process(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ticket_id' => 'required|integer|exists:attendee_tickets,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $ticket = Ticket::with('booking')->find($request->ticket_id);

        if ($ticket->checked_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket already checked in'
            ]);
        }

        $checkIn = CheckIn::create([
            'booking_id' => $ticket->booking_id,
            'ticket_id' => $ticket->id,
            'checked_in_by' => $request->user() ? $request->user()->id : null,
            'checked_in_at' => now(),
            'method' => 'api',
            'ip_address' => $request->ip(),
        ]);

        $ticket->update([
            'checked_in_at' => now(),
            'checked_in_by' => $checkIn->checked_in_by,
            'status' => 'used'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful',
            'data' => [
                'check_in' => [
                    'id' => $checkIn->id,
                    'checked_in_at' => $checkIn->checked_in_at,
                    'method' => $checkIn->method
                ]
            ]
        ]);
    }

    /**
     * Verify a ticket by number
     */
    public function verify($ticketNumber)
    {
        $ticket = Ticket::with(['booking.event', 'booking.user'])
            ->where('ticket_number', $ticketNumber)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ticket' => [
                    'ticket_number' => $ticket->ticket_number,
                    'attendee_name' => $ticket->attendee_name,
                    'status' => $ticket->status,
                    'checked_in' => !is_null($ticket->checked_in_at),
                    'checked_in_at' => $ticket->checked_in_at,
                    'event' => [
                        'title' => $ticket->booking->event->title,
                        'start_date' => $ticket->booking->event->start_date,
                        'venue' => $ticket->booking->event->venue
                    ]
                ]
            ]
        ]);
    }
}