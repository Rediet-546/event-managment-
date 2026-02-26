<?php

namespace App\Modules\Events\Http\Controllers\Admin;

use App\Modules\Core\Base\BaseController;
use App\Models\User;
use App\Modules\Events\Models\Event;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class VendorController extends BaseController
{
    /**
     * Display list of vendors.
     */
    public function index(Request $request): View
    {
        $this->authorize('view vendor reports');

        $vendors = User::role('vendor')
            ->withCount(['events' => function ($query) {
                $query->withTrashed();
            }])
            ->withCount(['events as published_events_count' => function ($query) {
                $query->where('status', 'published');
            }])
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->when($request->status, function ($query, $status) {
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'suspended') {
                    $query->where('is_active', false);
                }
            })
            ->paginate(15);

        return view('events::admin.vendors.index', compact('vendors'));
    }

    /**
     * Show vendor details.
     */
    public function show(User $vendor): View
    {
        $this->authorize('view vendor reports');

        if (!$vendor->hasRole('vendor')) {
            abort(404);
        }

        $vendor->loadCount(['events', 'events as total_revenue' => function ($query) {
            $query->select(\DB::raw('SUM(price * current_attendees)'));
        }]);

        $recentEvents = $vendor->events()
            ->with(['category'])
            ->latest()
            ->limit(5)
            ->get();

        $bookingsCount = \DB::table('bookings')
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->where('events.user_id', $vendor->id)
            ->count();

        $stats = [
            'total_events' => $vendor->events_count,
            'published_events' => $vendor->events()->where('status', 'published')->count(),
            'total_bookings' => $bookingsCount,
            'total_revenue' => $vendor->total_revenue ?? 0,
            'avg_rating' => $vendor->events()->avg('rating') ?? 0,
        ];

        return view('events::admin.vendors.show', compact('vendor', 'recentEvents', 'stats'));
    }

    /**
     * Approve vendor.
     */
    public function approve(User $vendor): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('approve vendors');

        if (!$vendor->hasRole('vendor')) {
            return back()->with('error', 'User is not a vendor');
        }

        $vendor->update([
            'vendor_approved_at' => now(),
            'approved_by' => auth()->id(),
            'is_active' => true,
        ]);

        // Send notification to vendor
        // $vendor->notify(new VendorApprovedNotification());

        return back()->with('success', 'Vendor approved successfully');
    }

    /**
     * Suspend vendor.
     */
    public function suspend(Request $request, User $vendor): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('suspend vendors');

        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $vendor->update([
            'is_active' => false,
            'suspended_at' => now(),
            'suspended_by' => auth()->id(),
            'suspension_reason' => $request->reason,
        ]);

        // Cancel all upcoming events?
        if ($request->cancel_events) {
            $vendor->events()
                ->where('start_date', '>', now())
                ->where('status', 'published')
                ->update(['status' => 'cancelled']);
        }

        // Send notification to vendor
        // $vendor->notify(new VendorSuspendedNotification($request->reason));

        return back()->with('success', 'Vendor suspended successfully');
    }

    /**
     * Reactivate vendor.
     */
    public function reactivate(User $vendor): \Illuminate\Http\RedirectResponse
    {
        $this->authorize('approve vendors');

        $vendor->update([
            'is_active' => true,
            'suspended_at' => null,
            'suspended_by' => null,
            'suspension_reason' => null,
        ]);

        return back()->with('success', 'Vendor reactivated successfully');
    }

    /**
     * Show vendor events.
     */
    public function events(User $vendor, Request $request): View
    {
        $this->authorize('view vendor reports');

        $events = $vendor->events()
            ->with(['category'])
            ->when($request->status, function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc')
            ->paginate(15);

        return view('events::admin.vendors.events', compact('vendor', 'events'));
    }

    /**
     * Show vendor earnings report.
     */
    public function earnings(User $vendor, Request $request): View
    {
        $this->authorize('view vendor reports');

        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $earnings = \DB::table('bookings')
            ->join('events', 'bookings.event_id', '=', 'events.id')
            ->where('events.user_id', $vendor->id)
            ->whereBetween('bookings.created_at', [$startDate, $endDate])
            ->select(
                \DB::raw('DATE(bookings.created_at) as date'),
                \DB::raw('COUNT(*) as total_bookings'),
                \DB::raw('SUM(bookings.amount_paid) as total_earnings')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $totalEarnings = $earnings->sum('total_earnings');
        $totalBookings = $earnings->sum('total_bookings');

        return view('events::admin.vendors.earnings', compact('vendor', 'earnings', 'totalEarnings', 'totalBookings', 'startDate', 'endDate'));
    }
}