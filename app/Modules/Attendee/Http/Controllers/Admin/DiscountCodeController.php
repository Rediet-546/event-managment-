<?php

namespace Modules\Attendee\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Attendee\Models\DiscountCode;
use Illuminate\Http\Request;

class DiscountCodeController extends Controller
{
    /**
     * Display a listing of discount codes
     */
    public function index(Request $request)
    {
        $query = DiscountCode::query();

        if ($request->filled('search')) {
            $query->where('code', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active === 'yes');
        }

        $discountCodes = $query->latest()->paginate(20);

        return view('attendee::admin.discounts.index', compact('discountCodes', 'request'));
    }

    /**
     * Show the form for creating a new discount code
     */
    public function create()
    {
        return view('attendee::admin.discounts.create');
    }

    /**
     * Store a newly created discount code
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:attendee_discount_codes,code',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'min_order_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        DiscountCode::create($validated);

        return redirect()->route('admin.attendee.discounts.index')
            ->with('success', 'Discount code created successfully.');
    }

    /**
     * Show the form for editing the specified discount code
     */
    public function edit(DiscountCode $discountCode)
    {
        return view('attendee::admin.discounts.edit', compact('discountCode'));
    }

    /**
     * Update the specified discount code
     */
    public function update(Request $request, DiscountCode $discountCode)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:attendee_discount_codes,code,' . $discountCode->id,
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'usage_limit' => 'nullable|integer|min:1',
            'valid_from' => 'nullable|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'min_order_amount' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        
        $discountCode->update($validated);

        return redirect()->route('admin.attendee.discounts.index')
            ->with('success', 'Discount code updated successfully.');
    }

    /**
     * Remove the specified discount code
     */
    public function destroy(DiscountCode $discountCode)
    {
        $discountCode->delete();

        return redirect()->route('admin.attendee.discounts.index')
            ->with('success', 'Discount code deleted successfully.');
    }
}