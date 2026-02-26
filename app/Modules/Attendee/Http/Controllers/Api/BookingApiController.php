<?php

namespace Modules\Attendee\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\Attendee\Models\Booking;
use Modules\Attendee\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingApiController extends Controller
{
    /**
     * Get user's bookings
     */
    public function index(Request $request)
    {
        $user = $request->user();
        
        $bookings = Booking::with(['event', 'ticketType'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($request->per_page ?? 15);

        return response()->json([
            'success' => true,
            'data' => [
                'bookings' => $bookings->items(),
                'pagination' => [
                    'total' => $bookings->total(),
                    'per_page' => $bookings->perPage(),
                    'current_page' => $bookings->currentPage(),
                    'last_page' => $bookings->lastPage()
                ]
            ]
        ]);
    }

    /**
     * Create a new booking
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'event_id' => 'required|integer|exists:events,id',
            'ticket_type_id' => 'required|integer|exists:attendee_ticket_types,id',
            'quantity' => 'required|integer|min:1|max:10',
            'payment_method' => 'required|string|in:stripe,paypal,bank_transfer',
            'special_requests' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $ticketType = TicketType::findOrFail($request->ticket_type_id);
            
            $booking = Booking::create([
                'booking_number' => Booking::generateBookingNumber(),
                'event_id' => $request->event_id,
                'user_id' => $request->user()->id,
                'ticket_type_id' => $request->ticket_type_id,
                'quantity' => $request->quantity,
                'unit_price' => $ticketType->price,
                'total_price' => $ticketType->price * $request->quantity,
                'final_price' => $ticketType->price * $request->quantity,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
                'booking_date' => now(),
                'special_requests' => $request->special_requests,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Booking created successfully',
                'data' => $booking->load(['event', 'ticketType'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Booking failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get booking details
     */
    public function show(Booking $booking, Request $request)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $booking->load(['event', 'ticketType', 'tickets'])
        ]);
    }

    /**
     * Cancel a booking
     */
    public function cancel(Booking $booking, Request $request)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        if ($booking->status !== 'confirmed' && $booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'This booking cannot be cancelled'
            ], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully'
        ]);
    }

    /**
     * Check event availability
     */
    public function checkAvailability($eventId, Request $request)
    {
        $ticketTypes = TicketType::where('status', 'active')->get();

        $availability = $ticketTypes->map(function ($type) {
            return [
                'id' => $type->id,
                'name' => $type->name,
                'price' => $type->price,
                'available' => $type->quantity_available ?? 100,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'event_id' => $eventId,
                'ticket_types' => $availability
            ]
        ]);
    }
}