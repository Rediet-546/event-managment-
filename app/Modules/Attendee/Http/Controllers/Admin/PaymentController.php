<?php

namespace Modules\Attendee\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Attendee\Models\Payment;
use Modules\Attendee\Models\Booking;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['booking.event', 'booking.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->latest()->paginate(20);

        $stats = [
            'total' => Payment::sum('amount'),
            'completed' => Payment::where('status', 'completed')->sum('amount'),
            'pending' => Payment::where('status', 'pending')->sum('amount'),
            'failed' => Payment::where('status', 'failed')->sum('amount'),
        ];

        $methods = ['stripe', 'paypal', 'bank_transfer', 'cash'];

        return view('attendee::admin.payments.index', compact('payments', 'stats', 'methods', 'request'));
    }

    /**
     * Display the specified payment
     */
    public function show(Payment $payment)
    {
        $payment->load(['booking.event', 'booking.user']);
        
        return view('attendee::admin.payments.show', compact('payment'));
    }

    /**
     * Mark a booking as paid (manual payment)
     */
    public function markAsPaid(Request $request, Booking $booking)
    {
        $request->validate([
            'payment_method' => 'required|string',
            'reference' => 'nullable|string'
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'transaction_id' => $request->reference ?? 'MANUAL-' . uniqid(),
            'amount' => $booking->final_price,
            'currency' => 'USD',
            'payment_method' => $request->payment_method,
            'status' => 'completed',
            'paid_at' => now()
        ]);

        $booking->update([
            'payment_id' => $payment->id,
            'payment_status' => 'paid',
            'status' => 'confirmed'
        ]);

        return redirect()->route('admin.attendee.bookings.show', $booking)
            ->with('success', 'Payment marked as paid successfully.');
    }
}