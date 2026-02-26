<?php

namespace App\Modules\Events\Http\Controllers;

use App\Modules\Core\Base\BaseController;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Models\EventCategory;
use App\Modules\Events\Services\EventService;
use App\Modules\Events\Services\CapacityService;
use App\Modules\Events\Http\Requests\StoreEventRequest;
use App\Modules\Events\Http\Requests\UpdateEventRequest;
use App\Modules\Events\Repositories\EventRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;

class EventController extends BaseController
{
    protected EventService $eventService;
    protected CapacityService $capacityService;
    protected EventRepositoryInterface $eventRepository;

    public function __construct(
        EventService $eventService,
        CapacityService $capacityService,
        EventRepositoryInterface $eventRepository
        )
    {
        $this->eventService = $eventService;
        $this->capacityService = $capacityService;
        $this->eventRepository = $eventRepository;
    }

    /**
     * Display a listing of events.
     */
    public function index(Request $request): View
    {
        Log::debug('EventController@index called', ['filters' => $request->all()]);

        $filters = $request->only(['search', 'category', 'city', 'start_date', 'end_date', 'is_free']);
        $events = $this->eventRepository->searchEvents($filters);

        $categories = EventCategory::active()->ordered()->get();

        Log::debug('Events found', ['count' => $events->total()]);

        return view('events::index', compact('events', 'categories', 'filters'));
    }

    /**
     * Show the form for creating a new event.
     */
    public function create(): View
    {
        Log::debug('EventController@create called');

        $categories = EventCategory::active()->ordered()->get();

        Log::debug('Categories loaded', ['count' => $categories->count()]);

        return view('events::create', compact('categories'));
    }

    /**
     * Store a newly created event.
     */
    public function store(StoreEventRequest $request): RedirectResponse
    {
        Log::debug('========== EVENT CREATION STARTED ==========');
        Log::debug('Store method called', [
            'all_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->fullUrl()
        ]);

        // Log the validated data
        $validatedData = $request->validated();
        Log::debug('StoreEventRequest validated data:', $validatedData);

        try {
            // Log before service call
            Log::debug('Calling EventService@createEvent');

            $event = $this->eventService->createEvent($validatedData);

            // Log after service call
            Log::debug('EventService@createEvent completed', [
                'event_id' => $event->id ?? 'null',
                'event_title' => $event->title ?? 'null'
            ]);

            // Handle media uploads
            if ($request->hasFile('media')) {
                Log::debug('Media files detected', ['count' => count($request->file('media'))]);

                foreach ($request->file('media') as $index => $media) {
                    try {
                        $path = $media->store('events/' . $event->id, 'public');

                        $event->media()->create([
                            'path' => $path,
                            'type' => 'image',
                            'mime_type' => $media->getMimeType(),
                            'size' => $media->getSize(),
                        ]);

                        Log::debug('Media uploaded', ['index' => $index, 'path' => $path]);
                    }
                    catch (\Exception $e) {
                        Log::error('Media upload failed', [
                            'index' => $index,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
            else {
                Log::debug('No media files in request');
            }

            // Double-check the event was saved
            if ($event && $event->id) {
                Log::debug('Event successfully created', [
                    'id' => $event->id,
                    'title' => $event->title,
                    'status' => $event->status,
                    'created_at' => $event->created_at
                ]);

                // Verify event exists in database
                $checkEvent = Event::find($event->id);
                Log::debug('Database verification', [
                    'found_in_db' => $checkEvent ? 'Yes' : 'No',
                    'db_status' => $checkEvent->status ?? 'N/A'
                ]);

                $redirectUrl = route('events.show', $event);
                Log::debug('Redirecting to:', ['url' => $redirectUrl]);

                return redirect()
                    ->route('events.show', $event)
                    ->with('success', 'Event created successfully!');
            }
            else {
                Log::error('Event creation returned invalid object');
                throw new \Exception('Event creation failed - no event object returned');
            }

        }
        catch (\Exception $e) {
            // Log any errors
            Log::error('========== EVENT CREATION FAILED ==========');
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Error file: ' . $e->getFile() . ':' . $e->getLine());
            Log::error('Error trace: ' . $e->getTraceAsString());

            // Log the request data for debugging
            Log::error('Request data that caused error:', $request->all());

            return back()
                ->with('error', 'Failed to create event: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified event.
     */
    public function show(Event $event): View
    {
        Log::debug('EventController@show called', ['event_id' => $event->id, 'event_slug' => $event->slug]);

        // Increment view count
        $this->eventRepository->incrementEventViews($event->id);

        $event->load(['category', 'organizer', 'media']);
        $availableSpots = $this->capacityService->getAvailableSpots($event);
        $occupancyPercentage = $this->capacityService->getOccupancyPercentage($event);

        // Get related events
        $relatedEvents = Event::published()
            ->where('category_id', $event->category_id)
            ->where('id', '!=', $event->id)
            ->upcoming()
            ->limit(3)
            ->get();

        Log::debug('Related events found', ['count' => $relatedEvents->count()]);

        return view('events::show', compact(
            'event',
            'availableSpots',
            'occupancyPercentage',
            'relatedEvents'
        ));
    }

    /**
     * Show the form for editing the specified event.
     */
    public function edit(Event $event): View
    {
        Log::debug('EventController@edit called', ['event_id' => $event->id]);

        $this->authorize('update', $event);

        $categories = EventCategory::active()->ordered()->get();

        return view('events::edit', compact('event', 'categories'));
    }

    /**
     * Update the specified event.
     */
    public function update(UpdateEventRequest $request, Event $event): RedirectResponse
    {
        Log::debug('EventController@update called', ['event_id' => $event->id]);

        $this->authorize('update', $event);

        try {
            $event = $this->eventService->updateEvent($event->id, $request->validated());

            Log::debug('Event updated successfully', ['event_id' => $event->id]);

            return redirect()
                ->route('events.show', $event)
                ->with('success', 'Event updated successfully!');

        }
        catch (\Exception $e) {
            Log::error('Event update failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);

            return back()
                ->with('error', 'Failed to update event: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified event.
     */
    public function destroy(Event $event): RedirectResponse
    {
        Log::debug('EventController@destroy called', ['event_id' => $event->id]);

        $this->authorize('delete', $event);

        try {
            $event->delete();

            Log::debug('Event deleted successfully', ['event_id' => $event->id]);

            return redirect()
                ->route('events.index')
                ->with('success', 'Event deleted successfully!');

        }
        catch (\Exception $e) {
            Log::error('Event deletion failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to delete event');
        }
    }

    /**
     * Display calendar view of events.
     */
    public function calendar(Request $request): View
    {
        Log::debug('EventController@calendar called', ['month' => $request->get('month'), 'year' => $request->get('year')]);

        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $events = Event::published()
            ->whereMonth('start_date', $month)
            ->whereYear('start_date', $year)
            ->get();

        Log::debug('Calendar events found', ['count' => $events->count()]);

        return view('events::calendar', compact('events', 'month', 'year'));
    }

    /**
     * Publish an event.
     */
    public function publish(Event $event): RedirectResponse
    {
        Log::debug('EventController@publish called', ['event_id' => $event->id]);

        $this->authorize('publish', $event);

        try {
            $this->eventService->publishEvent($event->id);

            Log::debug('Event published successfully', ['event_id' => $event->id]);

            return redirect()
                ->back()
                ->with('success', 'Event published successfully!');

        }
        catch (\Exception $e) {
            Log::error('Event publishing failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to publish event');
        }
    }

    /**
     * Cancel an event.
     */
    public function cancel(Event $event): RedirectResponse
    {
        Log::debug('EventController@cancel called', ['event_id' => $event->id]);

        $this->authorize('cancel', $event);

        try {
            $this->eventService->cancelEvent($event->id);

            Log::debug('Event cancelled successfully', ['event_id' => $event->id]);

            return redirect()
                ->back()
                ->with('success', 'Event cancelled successfully!');

        }
        catch (\Exception $e) {
            Log::error('Event cancellation failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to cancel event');
        }
    }

    /**
     * Duplicate an event.
     */
    public function duplicate(Event $event): RedirectResponse
    {
        Log::debug('EventController@duplicate called', ['event_id' => $event->id]);

        $this->authorize('duplicate', $event);

        try {
            $newEvent = $this->eventService->duplicateEvent($event->id);

            Log::debug('Event duplicated successfully', [
                'original_id' => $event->id,
                'new_id' => $newEvent->id
            ]);

            return redirect()
                ->route('events.edit', $newEvent)
                ->with('success', 'Event duplicated successfully! Please review the copy.');

        }
        catch (\Exception $e) {
            Log::error('Event duplication failed', [
                'event_id' => $event->id,
                'error' => $e->getMessage()
            ]);

            return back()->with('error', 'Failed to duplicate event');
        }
    }
}