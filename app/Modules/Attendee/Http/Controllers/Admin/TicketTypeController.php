<?php

namespace Modules\Attendee\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Attendee\Models\TicketType;
use Illuminate\Http\Request;

class TicketTypeController extends Controller
{
    /**
     * Display a listing of ticket types
     */
    public function index(Request $request)
    {
        $query = TicketType::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $ticketTypes = $query->latest()->paginate(20);

        return view('attendee::admin.ticket-types.index', compact('ticketTypes', 'request'));
    }

    /**
     * Show the form for creating a new ticket type
     */
    public function create()
    {
        return view('attendee::admin.ticket-types.create');
    }

    /**
     * Store a newly created ticket type
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'nullable|integer|min:0',
            'max_per_order' => 'required|integer|min:1',
            'min_per_order' => 'required|integer|min:1',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after:sale_start_date',
            'status' => 'required|in:active,inactive'
        ]);

        TicketType::create($validated);

        return redirect()->route('admin.attendee.ticket-types.index')
            ->with('success', 'Ticket type created successfully.');
    }

    /**
     * Show the form for editing the specified ticket type
     */
    public function edit(TicketType $ticketType)
    {
        return view('attendee::admin.ticket-types.edit', compact('ticketType'));
    }

    /**
     * Update the specified ticket type
     */
    public function update(Request $request, TicketType $ticketType)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity_available' => 'nullable|integer|min:0',
            'max_per_order' => 'required|integer|min:1',
            'min_per_order' => 'required|integer|min:1',
            'sale_start_date' => 'nullable|date',
            'sale_end_date' => 'nullable|date|after:sale_start_date',
            'status' => 'required|in:active,inactive'
        ]);

        $ticketType->update($validated);

        return redirect()->route('admin.attendee.ticket-types.index')
            ->with('success', 'Ticket type updated successfully.');
    }

    /**
     * Remove the specified ticket type
     */
    public function destroy(TicketType $ticketType)
    {
        if ($ticketType->bookings()->exists()) {
            return back()->with('error', 'Cannot delete ticket type with existing bookings.');
        }

        $ticketType->delete();

        return redirect()->route('admin.attendee.ticket-types.index')
            ->with('success', 'Ticket type deleted successfully.');
    }
}