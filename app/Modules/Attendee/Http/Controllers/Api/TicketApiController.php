<?php

namespace Modules\Attendee\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Attendee\Models\Ticket;
use Illuminate\Http\Request;

class TicketApiController extends Controller
{
    /**
     * Get user's tickets
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $tickets = Ticket::with(['booking.event'])
            ->whereHas('booking', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => [
                'tickets' => $tickets->items(),
                'pagination' => [
                    'total' => $tickets->total(),
                    'per_page' => $tickets->perPage(),
                    'current_page' => $tickets->currentPage(),
                    'last_page' => $tickets->lastPage()
                ]
            ]
        ]);
    }

    /**
     * Get ticket details
     */
    public function show($ticketNumber, Request $request)
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

        if ($ticket->booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'ticket_number' => $ticket->ticket_number,
                'attendee_name' => $ticket->attendee_name,
                'attendee_email' => $ticket->attendee_email,
                'status' => $ticket->status,
                'checked_in' => !is_null($ticket->checked_in_at),
                'checked_in_at' => $ticket->checked_in_at,
                'qr_code_url' => $ticket->qr_code_url,
                'booking' => [
                    'booking_number' => $ticket->booking->booking_number,
                    'event' => [
                        'id' => $ticket->booking->event->id,
                        'title' => $ticket->booking->event->title,
                        'start_date' => $ticket->booking->event->start_date,
                        'venue' => $ticket->booking->event->venue
                    ]
                ]
            ]
        ]);
    }

    /**
     * Get ticket QR code
     */
    public function qrCode($ticketNumber, Request $request)
    {
        $ticket = Ticket::where('ticket_number', $ticketNumber)->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        if ($ticket->booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'qr_code' => $ticket->qr_code,
                'qr_code_url' => $ticket->qr_code_url
            ]
        ]);
    }

    /**
     * Verify a ticket (public)
     */
    public function verify($ticketNumber)
    {
        $ticket = Ticket::with(['booking.event'])
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
                'ticket_number' => $ticket->ticket_number,
                'attendee_name' => $ticket->attendee_name,
                'event_name' => $ticket->booking->event->title,
                'event_date' => $ticket->booking->event->start_date,
                'is_valid' => !$ticket->checked_in_at,
                'checked_in' => !is_null($ticket->checked_in_at),
                'checked_in_at' => $ticket->checked_in_at
            ]
        ]);
    }
}