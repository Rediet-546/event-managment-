<?php

namespace Modules\Attendee\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Attendee\Models\EmailTemplate;
use Illuminate\Http\Request;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates
     */
    public function index(Request $request)
    {
        $query = EmailTemplate::query();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $templates = $query->paginate(20);

        $types = ['confirmation', 'reminder', 'cancellation', 'receipt', 'custom'];

        return view('attendee::admin.email-templates.index', compact('templates', 'types', 'request'));
    }

    /**
     * Show the form for creating a new email template
     */
    public function create()
    {
        $types = ['confirmation', 'reminder', 'cancellation', 'receipt', 'custom'];
        $variables = [
            '{booking_number}',
            '{event_name}',
            '{event_date}',
            '{event_venue}',
            '{attendee_name}',
            '{ticket_type}',
            '{quantity}',
            '{total_price}',
            '{booking_date}',
            '{check_in_url}'
        ];

        return view('attendee::admin.email-templates.create', compact('types', 'variables'));
    }

    /**
     * Store a newly created email template
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');

        if ($validated['is_default']) {
            EmailTemplate::where('type', $validated['type'])->update(['is_default' => false]);
        }

        EmailTemplate::create($validated);

        return redirect()->route('admin.attendee.email-templates.index')
            ->with('success', 'Email template created successfully.');
    }

    /**
     * Show the form for editing the specified email template
     */
    public function edit(EmailTemplate $emailTemplate)
    {
        $types = ['confirmation', 'reminder', 'cancellation', 'receipt', 'custom'];
        $variables = [
            '{booking_number}',
            '{event_name}',
            '{event_date}',
            '{event_venue}',
            '{attendee_name}',
            '{ticket_type}',
            '{quantity}',
            '{total_price}',
            '{booking_date}',
            '{check_in_url}'
        ];

        return view('attendee::admin.email-templates.edit', compact('emailTemplate', 'types', 'variables'));
    }

    /**
     * Update the specified email template
     */
    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'is_active' => 'boolean',
            'is_default' => 'boolean'
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_default'] = $request->has('is_default');

        if ($validated['is_default']) {
            EmailTemplate::where('type', $validated['type'])
                ->where('id', '!=', $emailTemplate->id)
                ->update(['is_default' => false]);
        }

        $emailTemplate->update($validated);

        return redirect()->route('admin.attendee.email-templates.index')
            ->with('success', 'Email template updated successfully.');
    }

    /**
     * Remove the specified email template
     */
    public function destroy(EmailTemplate $emailTemplate)
    {
        if ($emailTemplate->is_default) {
            return back()->with('error', 'Cannot delete default template.');
        }

        $emailTemplate->delete();

        return redirect()->route('admin.attendee.email-templates.index')
            ->with('success', 'Email template deleted successfully.');
    }

    /**
     * Preview email template
     */
    public function preview(EmailTemplate $emailTemplate)
    {
        $content = $emailTemplate->content;
        
        // Replace variables with sample data
        $replacements = [
            '{booking_number}' => 'BKG202401010001',
            '{event_name}' => 'Sample Event',
            '{event_date}' => now()->format('F d, Y - h:i A'),
            '{event_venue}' => 'Sample Venue',
            '{attendee_name}' => 'John Doe',
            '{ticket_type}' => 'General Admission',
            '{quantity}' => '2',
            '{total_price}' => '$100.00',
            '{booking_date}' => now()->format('Y-m-d H:i'),
            '{check_in_url}' => url('/checkin/sample')
        ];
        
        $content = str_replace(array_keys($replacements), array_values($replacements), $content);
        
        return view('attendee::admin.email-templates.preview', compact('content'));
    }
}