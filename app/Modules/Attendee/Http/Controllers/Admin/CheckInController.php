<?php

namespace Modules\Attendee\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Attendee\Models\CheckIn;
use Modules\Attendee\Models\Ticket;
use Illuminate\Http\Request;

class CheckInController extends Controller
{
    /**
     * Display a listing of check-ins
     */
    public function index(Request $request)
    {
        $query = CheckIn::with(['booking.event', 'booking.user', 'ticket', 'checker']);

        if ($request->filled('date_from')) {
            $query->whereDate('checked_in_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('checked_in_at', '<=', $request->date_to);
        }

        $checkIns = $query->latest('checked_in_at')->paginate(20);

        return view('attendee::admin.checkins.index', compact('checkIns', 'request'));
    }

    /**
     * Show the QR scanner page
     */
    public function scan()
    {
        return view('attendee::admin.checkins.scan');
    }

    /**
     * Verify a ticket by QR code
     */
    public function verify(Request $request)
    {
        $request->validate([
            'qr_code' => 'required|string'
        ]);

        $ticket = Ticket::with(['booking.event', 'booking.user'])
            ->where('qr_code', $request->qr_code)
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid ticket QR code.'
            ], 404);
        }

        if ($ticket->checked_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket already used at ' . $ticket->checked_in_at->format('Y-m-d H:i:s'),
                'ticket' => $ticket
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Valid ticket',
            'ticket' => $ticket
        ]);
    }

    /**
     * Process a check-in
     */
    public function process(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:attendee_tickets,id'
        ]);

        $ticket = Ticket::with('booking')->findOrFail($request->ticket_id);

        if ($ticket->checked_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket already checked in'
            ]);
        }

        $checkIn = CheckIn::create([
            'booking_id' => $ticket->booking_id,
            'ticket_id' => $ticket->id,
            'checked_in_by' => auth()->id(),
            'checked_in_at' => now(),
            'method' => 'qr',
            'ip_address' => $request->ip(),
        ]);

        $ticket->update([
            'checked_in_at' => now(),
            'checked_in_by' => auth()->id(),
            'status' => 'used'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful!',
            'check_in' => $checkIn
        ]);
    }
}