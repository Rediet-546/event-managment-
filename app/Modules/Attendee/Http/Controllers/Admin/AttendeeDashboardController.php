<?php

namespace Modules\Attendee\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Attendee\Models\Booking;
use Modules\Attendee\Models\TicketType;
use Illuminate\Http\Request;

class AttendeeDashboardController extends Controller
{
    /**
     * Display the admin dashboard with statistics
     */
    public function index()
    {
        // Get statistics
        $stats = [
            'total_bookings' => Booking::count(),
            'confirmed_bookings' => Booking::where('status', 'confirmed')->count(),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'cancelled_bookings' => Booking::where('status', 'cancelled')->count(),
            'total_revenue' => Booking::where('status', 'confirmed')->sum('final_price'),
            'checked_in' => Booking::whereNotNull('checked_in_at')->count(),
            'today_bookings' => Booking::whereDate('created_at', today())->count(),
            'today_revenue' => Booking::whereDate('created_at', today())->where('status', 'confirmed')->sum('final_price'),
            'today_checkins' => Booking::whereDate('checked_in_at', today())->count(),
        ];

        // Recent bookings
        $recentBookings = Booking::with(['event', 'user'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($booking) {
                return (object)[
                    'id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'event' => (object)['title' => $booking->event->title ?? 'N/A'],
                    'user' => (object)['name' => $booking->user->name ?? 'N/A'],
                    'final_price' => $booking->final_price,
                    'status' => $booking->status,
                    'status_label' => $this->getStatusLabel($booking->status),
                ];
            });

        // Popular events
        $popularEvents = Booking::selectRaw('event_id, count(*) as bookings, sum(final_price) as revenue')
            ->with('event')
            ->where('status', 'confirmed')
            ->groupBy('event_id')
            ->orderBy('bookings', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return (object)[
                    'title' => $item->event->title ?? 'N/A',
                    'bookings' => $item->bookings,
                    'revenue' => $item->revenue,
                    'utilization' => $item->event ? min(100, ($item->bookings / $item->event->capacity) * 100) : 0,
                ];
            });

        // Chart data
        $chartLabels = [];
        $chartData = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $chartLabels[] = $date->format('M d');
            $revenue = Booking::whereDate('created_at', $date)
                ->where('status', 'confirmed')
                ->sum('final_price');
            $chartData[] = $revenue;
        }

        return view('attendee::admin.dashboard.index', compact(
            'stats', 
            'recentBookings', 
            'popularEvents', 
            'chartLabels', 
            'chartData'
        ));
    }

    /**
     * Get status label HTML
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'confirmed' => '<span class="badge badge-success">Confirmed</span>',
            'cancelled' => '<span class="badge badge-danger">Cancelled</span>',
            'refunded' => '<span class="badge badge-info">Refunded</span>',
            'expired' => '<span class="badge badge-secondary">Expired</span>',
        ];

        return $labels[$status] ?? '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
    }
}