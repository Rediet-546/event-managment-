<?php

namespace Modules\Attendee\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Attendee\Models\AttendeeSetting;
use Illuminate\Http\Request;

class AttendeeSettingController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        $settings = [
            'module_name' => AttendeeSetting::get('module_name', 'Attendee Module'),
            'booking_prefix' => AttendeeSetting::get('booking_prefix', 'BKG'),
            'max_per_order' => AttendeeSetting::get('max_per_order', 10),
            'expiry_hours' => AttendeeSetting::get('expiry_hours', 24),
            'cancellation_cutoff' => AttendeeSetting::get('cancellation_cutoff', 48),
            'auto_confirm_free' => AttendeeSetting::get('auto_confirm_free', true),
            'require_login' => AttendeeSetting::get('require_login', true),
            'currency' => AttendeeSetting::get('currency', 'USD'),
            'tax_rate' => AttendeeSetting::get('tax_rate', 0),
            'service_fee' => AttendeeSetting::get('service_fee', 0),
            'stripe_enabled' => AttendeeSetting::get('stripe_enabled', false),
            'paypal_enabled' => AttendeeSetting::get('paypal_enabled', false),
            'bank_transfer_enabled' => AttendeeSetting::get('bank_transfer_enabled', true),
            'ticket_prefix' => AttendeeSetting::get('ticket_prefix', 'TIC'),
            'qr_size' => AttendeeSetting::get('qr_size', 200),
            'ticket_format' => AttendeeSetting::get('ticket_format', 'pdf'),
            'date_format' => AttendeeSetting::get('date_format', 'Y-m-d'),
            'time_format' => AttendeeSetting::get('time_format', 'H:i'),
            'from_email' => AttendeeSetting::get('from_email', config('mail.from.address')),
            'from_name' => AttendeeSetting::get('from_name', config('mail.from.name')),
            'confirmation_subject' => AttendeeSetting::get('confirmation_subject', 'Booking Confirmation'),
            'reminder_hours' => AttendeeSetting::get('reminder_hours', 24),
        ];

        return view('attendee::admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'module_name' => 'required|string|max:255',
            'booking_prefix' => 'required|string|max:10',
            'max_per_order' => 'required|integer|min:1|max:100',
            'expiry_hours' => 'required|integer|min:1|max:168',
            'cancellation_cutoff' => 'required|integer|min:0|max:720',
            'auto_confirm_free' => 'boolean',
            'require_login' => 'boolean',
            'currency' => 'required|string|size:3',
            'tax_rate' => 'required|numeric|min:0|max:100',
            'service_fee' => 'required|numeric|min:0',
            'stripe_enabled' => 'boolean',
            'paypal_enabled' => 'boolean',
            'bank_transfer_enabled' => 'boolean',
            'ticket_prefix' => 'required|string|max:10',
            'qr_size' => 'required|integer|min:100|max:500',
            'ticket_format' => 'required|in:pdf,html',
            'date_format' => 'required|string',
            'time_format' => 'required|string',
            'from_email' => 'required|email',
            'from_name' => 'required|string|max:255',
            'confirmation_subject' => 'required|string|max:255',
            'reminder_hours' => 'required|integer|min:1|max:168',
        ]);

        foreach ($validated as $key => $value) {
            if (in_array($key, ['auto_confirm_free', 'require_login', 'stripe_enabled', 'paypal_enabled', 'bank_transfer_enabled'])) {
                $value = $request->has($key);
            }
            
            AttendeeSetting::set($key, $value);
        }

        return redirect()->route('admin.attendee.settings')
            ->with('success', 'Settings saved successfully.');
    }
}