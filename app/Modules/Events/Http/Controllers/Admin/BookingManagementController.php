<?php

namespace App\Modules\Events\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules\Events\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class BookingManagementController extends Controller
{
    public function index()
    {
        $bookings = Booking::query()
            ->with(['event', 'user', 'guests'])
            ->orderByDesc('created_at')
            ->paginate(25);

        return view('events::admin.bookings.index', compact('bookings'));
    }

    public function show(Booking $booking)
    {
        $booking->load(['event', 'user', 'guests']);

        return view('events::admin.bookings.show', compact('booking'));
    }

    public function analytics()
    {
        $stats = [
            'total' => Booking::count(),
            'confirmed' => Booking::where('status', 'confirmed')->count(),
            'cancelled' => Booking::where('status', 'cancelled')->count(),
            'checked_in' => Booking::whereNotNull('checked_in_at')->count(),
            'revenue' => (float) Booking::where('status', 'confirmed')->sum('amount_paid'),
        ];

        return view('events::admin.bookings.analytics', compact('stats'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:confirmed,cancelled'],
        ]);

        $booking->update(['status' => $validated['status']]);

        return back()->with('success', 'Booking status updated.');
    }

    public function checkIn(Booking $booking)
    {
        if (!$booking->checked_in_at) {
            $booking->update(['checked_in_at' => now()]);
        }

        return back()->with('success', 'Booking checked in.');
    }

    public function bulkCheckIn(Request $request)
    {
        $validated = $request->validate([
            'booking_ids' => ['required', 'array', 'min:1'],
            'booking_ids.*' => ['integer'],
        ]);

        DB::transaction(function () use ($validated) {
            Booking::whereIn('id', $validated['booking_ids'])
                ->whereNull('checked_in_at')
                ->update(['checked_in_at' => now()]);
        });

        return back()->with('success', 'Selected bookings checked in.');
    }

    public function export(): Response
    {
        $rows = Booking::query()
            ->with(['event', 'user'])
            ->orderByDesc('created_at')
            ->limit(5000)
            ->get()
            ->map(function (Booking $b) {
                return [
                    $b->booking_reference,
                    $b->status,
                    $b->tickets_count,
                    (string) $b->amount_paid,
                    optional($b->event)->title,
                    optional($b->user)->email,
                    optional($b->created_at)->toDateTimeString(),
                ];
            });

        $csv = "reference,status,tickets,amount,event,user_email,created_at\n";
        foreach ($rows as $r) {
            $csv .= '"' . implode('","', array_map(static fn ($v) => str_replace('"', '""', (string) $v), $r)) . "\"\n";
        }

        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="bookings.csv"',
        ]);
    }
}

