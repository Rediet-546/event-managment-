<?php

namespace App\Modules\Registration\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Modules\Events\Models\Event;
use App\Modules\Attendee\Models\Booking;

class CreatorDashboardController extends Controller
{
    /**
     * Display the creator dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get creator's events
        $events = Event::where('creator_id', $user->id)
            ->withCount('bookings')
            ->withSum('bookings', 'total_amount')
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        // Calculate statistics
        $statistics = [
            'total_events' => Event::where('creator_id', $user->id)->count(),
            'active_events' => Event::where('creator_id', $user->id)
                ->where('status', 'published')
                ->where('start_date', '>', now())
                ->count(),
            'total_bookings' => Booking::whereHas('event', function($q) use ($user) {
                    $q->where('creator_id', $user->id);
                })->count(),
            'total_revenue' => Booking::whereHas('event', function($q) use ($user) {
                    $q->where('creator_id', $user->id);
                })->where('status', 'confirmed')
                ->sum('total_amount'),
        ];
        
        // Get recent bookings
        $recentBookings = Booking::whereHas('event', function($q) use ($user) {
                $q->where('creator_id', $user->id);
            })
            ->with(['user', 'event'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Analytics for charts
        $analytics = [
            'bookings_over_time' => Booking::whereHas('event', function($q) use ($user) {
                    $q->where('creator_id', $user->id);
                })
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as revenue')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
        ];
        
        // Events by status for pie chart
        $statuses = Event::where('creator_id', $user->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();
        
        return view('registration::creator.dashboard', compact(
            'user', 
            'events', 
            'statistics', 
            'recentBookings', 
            'analytics', 
            'statuses'
        ));
    }

    /**
     * Display creator analytics.
     */
    public function analytics()
    {
        $user = Auth::user();
        
        // Add your analytics logic here
        return view('registration::creator.analytics', compact('user'));
    }

    /**
     * Display event bookings for creators.
     */
    public function eventBookings($eventId)
    {
        $user = Auth::user();
        $event = Event::where('creator_id', $user->id)
            ->with('bookings.user')
            ->findOrFail($eventId);
            
        return view('registration::creator.event-bookings', compact('event'));
    }

    /**
     * Display pending approval page.
     */
    public function pending()
    {
        return view('registration::creator.pending');
    }
}