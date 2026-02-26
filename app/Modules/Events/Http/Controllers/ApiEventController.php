<?php

namespace App\Modules\Events\Http\Controllers;

use App\Modules\Core\Base\BaseController;
use App\Modules\Events\Models\Event;
use App\Modules\Events\Http\Requests\StoreEventRequest;
use App\Modules\Events\Http\Requests\UpdateEventRequest;
use App\Modules\Events\Repositories\EventRepositoryInterface;
use App\Modules\Events\Services\EventService;
use App\Modules\Events\Services\CapacityService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ApiEventController extends BaseController
{
    protected EventService $eventService;
    protected CapacityService $capacityService;
    protected EventRepositoryInterface $eventRepository;

    public function __construct(
        EventService $eventService,
        CapacityService $capacityService,
        EventRepositoryInterface $eventRepository
    ) {
        $this->eventService = $eventService;
        $this->capacityService = $capacityService;
        $this->eventRepository = $eventRepository;
    }

    /**
     * List all events.
     */
    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'category', 'city', 'start_date', 'end_date', 'is_free']);
        $events = $this->eventRepository->searchEvents($filters);

        return response()->json([
            'success' => true,
            'data' => $events,
            'message' => 'Events retrieved successfully'
        ]);
    }

    /**
     * Get upcoming events.
     */
    public function upcoming(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $events = $this->eventRepository->getUpcomingEvents($perPage);

        return response()->json([
            'success' => true,
            'data' => $events,
            'message' => 'Upcoming events retrieved successfully'
        ]);
    }

    /**
     * Get featured events.
     */
    public function featured(Request $request): JsonResponse
    {
        $limit = $request->get('limit', 5);
        $events = $this->eventRepository->getFeaturedEvents($limit);

        return response()->json([
            'success' => true,
            'data' => $events,
            'message' => 'Featured events retrieved successfully'
        ]);
    }

    /**
     * Store a new event.
     */
    public function store(StoreEventRequest $request): JsonResponse
    {
        $event = $this->eventService->createEvent($request->validated());

        return response()->json([
            'success' => true,
            'data' => $event->load('category'),
            'message' => 'Event created successfully'
        ], 201);
    }

    /**
     * Show event details.
     */
    public function show(Event $event): JsonResponse
    {
        $this->eventRepository->incrementEventViews($event->id);
        
        $event->load(['category', 'organizer', 'media']);
        
        $data = [
            'event' => $event,
            'available_spots' => $this->capacityService->getAvailableSpots($event),
            'occupancy_percentage' => $this->capacityService->getOccupancyPercentage($event),
            'is_bookable' => $event->isBookable(),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => 'Event details retrieved successfully'
        ]);
    }

    /**
     * Update an event.
     */
    public function update(UpdateEventRequest $request, Event $event): JsonResponse
    {
        $this->authorize('update', $event);
        
        $event = $this->eventService->updateEvent($event->id, $request->validated());

        return response()->json([
            'success' => true,
            'data' => $event,
            'message' => 'Event updated successfully'
        ]);
    }

    /**
     * Delete an event.
     */
    public function destroy(Event $event): JsonResponse
    {
        $this->authorize('delete', $event);
        
        $event->delete();

        return response()->json([
            'success' => true,
            'message' => 'Event deleted successfully'
        ]);
    }

    /**
     * Check event availability.
     */
    public function checkAvailability(Event $event, Request $request): JsonResponse
    {
        $requestedSpots = $request->get('spots', 1);
        
        $isAvailable = $this->capacityService->hasAvailableCapacity($event, $requestedSpots);
        
        return response()->json([
            'success' => true,
            'data' => [
                'is_available' => $isAvailable,
                'available_spots' => $this->capacityService->getAvailableSpots($event),
                'requested_spots' => $requestedSpots
            ],
            'message' => $isAvailable ? 'Spots available' : 'Insufficient capacity'
        ]);
    }
}