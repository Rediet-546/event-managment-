<?php

namespace Modules\Attendee\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Attendee\Models\Booking;
use Modules\Attendee\Models\TicketType;
use Modules\Attendee\Exports\BookingsExport;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BookingController extends Controller
{
    /**
     * Display a listing of bookings
     */
    public function index(Request $request)
    {
        $query = Booking::with(['event', 'user', 'ticketType']);

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('booking_number', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', function ($uq) use ($request) {
                      $uq->where('name', 'like', '%' . $request->search . '%')
                         ->orWhere('email', 'like', '%' . $request->search . '%');
                  });
            });
        }

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Get statistics
        $stats = [
            'total' => (clone $query)->count(),
            'confirmed' => (clone $query)->where('status', 'confirmed')->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'cancelled' => (clone $query)->where('status', 'cancelled')->count(),
            'revenue' => (clone $query)->where('status', 'confirmed')->sum('final_price'),
        ];

        // Paginate results
        $bookings = $query->latest()->paginate(20);

        // Get filter data
        $events = Event::all(['id', 'title']);
        $statuses = ['pending', 'confirmed', 'cancelled', 'refunded', 'expired'];
        $paymentStatuses = ['pending', 'paid', 'failed', 'refunded'];

        return view('attendee::admin.bookings.index', compact(
            'bookings', 'events', 'statuses', 'paymentStatuses', 'stats', 'request'
        ));
    }

    /**
     * Show the form for creating a new booking
     */
    public function create()
    {
        $events = Event::where('start_date', '>', now())->get(['id', 'title', 'start_date']);
        $ticketTypes = TicketType::where('status', 'active')->get();
        $users = User::orderBy('name')->limit(100)->get(['id', 'name', 'email']);
        
        return view('attendee::admin.bookings.create', compact('events', 'ticketTypes', 'users'));
    }

    /**
     * Store a newly created booking
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'user_id' => 'required|exists:users,id',
            'ticket_type_id' => 'required|exists:attendee_ticket_types,id',
            'quantity' => 'required|integer|min:1|max:10',
            'payment_method' => 'required|string',
            'special_requests' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            // Get ticket type
            $ticketType = TicketType::findOrFail($validated['ticket_type_id']);
            
            // Calculate price
            $totalPrice = $ticketType->price * $validated['quantity'];
            
            // Create booking
            $booking = Booking::create([
                'event_id' => $validated['event_id'],
                'user_id' => $validated['user_id'],
                'ticket_type_id' => $validated['ticket_type_id'],
                'quantity' => $validated['quantity'],
                'unit_price' => $ticketType->price,
                'total_price' => $totalPrice,
                'final_price' => $totalPrice,
                'status' => 'confirmed',
                'payment_status' => 'paid',
                'payment_method' => $validated['payment_method'],
                'booking_date' => now(),
                'special_requests' => $validated['special_requests'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()->route('admin.attendee.bookings.show', $booking)
                ->with('success', 'Booking created successfully.');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create booking: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified booking
     */
    public function show(Booking $booking)
    {
        $booking->load(['event', 'user', 'ticketType', 'tickets', 'payment', 'history.user']);
        
        return view('attendee::admin.bookings.show', compact('booking'));
    }

    /**
     * Show the form for editing the specified booking
     */
    public function edit(Booking $booking)
    {
        $events = Event::all(['id', 'title']);
        $ticketTypes = TicketType::all();
        $statuses = ['pending', 'confirmed', 'cancelled', 'refunded', 'expired'];
        
        return view('attendee::admin.bookings.edit', compact('booking', 'events', 'ticketTypes', 'statuses'));
    }

    /**
     * Update the specified booking
     */
    public function update(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,refunded,expired',
            'payment_status' => 'nullable|in:pending,paid,failed,refunded',
            'notes' => 'nullable|string',
        ]);

        $oldStatus = $booking->status;
        $booking->update([
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status'] ?? $booking->payment_status,
            'notes' => $validated['notes'] ?? $booking->notes,
        ]);

        return redirect()->route('admin.attendee.bookings.show', $booking)
            ->with('success', 'Booking updated successfully.');
    }

    /**
     * Remove the specified booking
     */
    public function destroy(Booking $booking)
    {
        $booking->delete();
        
        return redirect()->route('admin.attendee.bookings.index')
            ->with('success', 'Booking deleted successfully.');
    }

    /**
     * Export bookings to Excel
     */
    public function export(Request $request)
    {
        return Excel::download(
            new BookingsExport($request->all()),
            'bookings-' . date('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Check-in a booking
     */
    public function checkIn(Request $request, Booking $booking)
    {
        if ($booking->checked_in_at) {
            return response()->json([
                'success' => false,
                'message' => 'Already checked in at ' . $booking->checked_in_at->format('Y-m-d H:i')
            ], 422);
        }

        $booking->update([
            'checked_in_at' => now(),
            'checked_in_by' => auth()->id()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Check-in successful.'
        ]);
    }

    /**
     * Bulk actions on bookings
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:export,checkin,cancel,delete',
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:attendee_bookings,id'
        ]);

        switch ($request->action) {
            case 'export':
                return $this->export($request);
                
            case 'checkin':
                $count = Booking::whereIn('id', $request->booking_ids)
                    ->whereNull('checked_in_at')
                    ->update([
                        'checked_in_at' => now(),
                        'checked_in_by' => auth()->id()
                    ]);
                return response()->json([
                    'success' => true,
                    'message' => "{$count} attendees checked in successfully."
                ]);
                
            case 'cancel':
                $count = Booking::whereIn('id', $request->booking_ids)
                    ->whereIn('status', ['pending', 'confirmed'])
                    ->update(['status' => 'cancelled']);
                return response()->json([
                    'success' => true,
                    'message' => "{$count} bookings cancelled successfully."
                ]);
                
            case 'delete':
                $count = Booking::whereIn('id', $request->booking_ids)->delete();
                return response()->json([
                    'success' => true,
                    'message' => "{$count} bookings deleted successfully."
                ]);
        }
    }
}