<?php

namespace App\Modules\Events\Http\Controllers;

use App\Modules\Core\Base\BaseController;
use App\Modules\Events\Models\Booking;
use App\Modules\Events\Models\BookingGuest;
use App\Modules\Events\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingController extends BaseController
{
    public function myBookings(): View
    {
        $bookings = Booking::query()
            ->where('user_id', auth()->id())
            ->with('event')
            ->orderByDesc('created_at')
            ->get();

        return view('events::bookings.my-bookings', compact('bookings'));
    }

    public function create(Event $event): View
    {
        if (!$event->isBookable()) {
            abort(403);
        }

        return view('events::bookings.create', compact('event'));
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        if (!$event->isBookable()) {
            abort(403);
        }

        $validated = $request->validate([
            'tickets' => ['required', 'integer', 'min:1'],
            'attendees' => ['required', 'array', 'min:1'],
            'attendees.*.name' => ['required', 'string', 'max:255'],
            'attendees.*.email' => ['required', 'email', 'max:255'],
            'payment_method' => ['nullable', 'string', 'max:50'],
            'terms_accepted' => ['required', 'accepted'],
        ]);

        $tickets = (int) $validated['tickets'];

        // Capacity check
        if ($event->max_attendees) {
            $available = max(0, (int) $event->max_attendees - (int) $event->current_attendees);
            if ($tickets > $available) {
                return back()
                    ->withErrors([
                        'tickets' => "There are only {$available} spots available for this event.",
                    ])
                    ->withInput();
            }
        }

        $attendees = array_slice($validated['attendees'], 0, $tickets);
        if (count($attendees) !== $tickets) {
            return back()
                ->withErrors([
                    'attendees' => 'Attendee information must be provided for each ticket.',
                ])
                ->withInput();
        }

        $booking = DB::transaction(function () use ($event, $tickets, $attendees, $validated) {
            $amountPaid = $event->is_free ? 0 : (float) $event->price * $tickets;

            $booking = Booking::create([
                'event_id' => $event->id,
                'user_id' => auth()->id(),
                'booking_reference' => 'BK-' . strtoupper(Str::random(10)),
                'tickets_count' => $tickets,
                'amount_paid' => $amountPaid,
                'payment_method' => $validated['payment_method'] ?? null,
                'status' => 'confirmed',
                'booking_date' => now(),
            ]);

            foreach ($attendees as $attendee) {
                BookingGuest::create([
                    'booking_id' => $booking->id,
                    'name' => $attendee['name'],
                    'email' => $attendee['email'],
                ]);
            }

            $event->increment('current_attendees', $tickets);

            return $booking;
        });

        return redirect()
            ->route('events.booking.confirmation', $booking)
            ->with('success', 'Booking confirmed.');
    }

    public function confirmation(Booking $booking): View
    {
        $this->authorizeBookingOwner($booking);

        $booking->load(['event', 'guests']);

        return view('events::bookings.confirmation', compact('booking'));
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorizeBookingOwner($booking);

        $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Booking is already cancelled.');
        }

        DB::transaction(function () use ($booking, $request) {
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $request->input('reason'),
            ]);

            $booking->event()->decrement('current_attendees', (int) $booking->tickets_count);
        });

        return redirect()
            ->route('events.my-bookings')
            ->with('success', 'Booking cancelled successfully.');
    }

    public function cancelEventBooking(Event $event, Booking $booking): RedirectResponse
    {
        if ((int) $booking->event_id !== (int) $event->id) {
            abort(404);
        }

        return $this->cancel(request(), $booking);
    }

    public function manageBookings(Event $event): View
    {
        $this->authorize('manageBookings', $event);

        $bookings = Booking::query()
            ->where('event_id', $event->id)
            ->with(['user', 'guests'])
            ->orderByDesc('created_at')
            ->get();

        return view('events::bookings.manage', compact('event', 'bookings'));
    }

    public function checkIn(Event $event, Booking $booking): RedirectResponse
    {
        $this->authorize('checkInAttendees', $event);

        if ((int) $booking->event_id !== (int) $event->id) {
            abort(404);
        }

        if (!$booking->checked_in_at) {
            $booking->update(['checked_in_at' => now()]);
        }

        return back()->with('success', 'Attendee checked in successfully.');
    }

    private function authorizeBookingOwner(Booking $booking): void
    {
        if ((int) $booking->user_id !== (int) auth()->id()) {
            abort(403);
        }
    }
}

