<?php

namespace App\Modules\Events\Services;

use App\Modules\Events\Repositories\EventRepositoryInterface;
use App\Modules\Events\Models\Event;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class EventService
{
    protected EventRepositoryInterface $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function createEvent(array $data): Event
    {
        $this->validateEventData($data);

        return DB::transaction(function () use ($data) {
            // Set default values
            $data['user_id'] = auth()->id();
            $data['status'] = $data['status'] ?? 'draft';
            $data['is_free'] = ($data['price'] ?? 0) <= 0;
            $data['is_featured'] = $data['is_featured'] ?? false;

            // Handle scheduled publishing
            if (isset($data['publish_at']) && !empty($data['publish_at'])) {
                $publishAt = Carbon::parse($data['publish_at']);

                // If publish_at is in the future, keep as draft (will be published by scheduler)
                if ($publishAt->isFuture()) {
                    $data['status'] = 'draft';
                    $data['publish_at'] = $publishAt;
                }
                // If publish_at is now or in the past, publish immediately
                else {
                    $data['status'] = 'published';
                    $data['published_at'] = now();
                    unset($data['publish_at']);
                }
            }
            // If no publish_at but status is published, set published_at now
            elseif (($data['status'] ?? 'draft') === 'published') {
                $data['published_at'] = now();
            }

            return $this->eventRepository->create($data);
        });
    }

    public function updateEvent(int $id, array $data): Event
    {
        $this->validateEventData($data, $id);

        return DB::transaction(function () use ($id, $data) {
            if (isset($data['price'])) {
                $data['is_free'] = $data['price'] <= 0;
            }

            // Handle featured status
            if (isset($data['is_featured'])) {
                $data['is_featured'] = filter_var($data['is_featured'], FILTER_VALIDATE_BOOLEAN);
            }

            // Handle status changes
            if (isset($data['status']) && $data['status'] === 'published') {
                $data['published_at'] = now();
            }

            return $this->eventRepository->update($id, $data);
        });
    }

    public function publishEvent(int $id): Event
    {
        return DB::transaction(function () use ($id) {
            return $this->eventRepository->update($id, [
                'status' => 'published',
                'published_at' => now()
            ]);
        });
    }

    public function cancelEvent(int $id, ?string $reason = null): Event
    {
        return DB::transaction(function () use ($id, $reason) {
            return $this->eventRepository->update($id, [
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $reason
            ]);
        });
    }

    public function duplicateEvent(int $id): Event
    {
        $event = $this->eventRepository->find($id);

        return DB::transaction(function () use ($event) {
            $newEventData = $event->toArray();
            unset(
            $newEventData['id'],
            $newEventData['slug'],
            $newEventData['created_at'],
            $newEventData['updated_at'],
            $newEventData['published_at'],
            $newEventData['cancelled_at'],
            $newEventData['views']
            );

            $newEventData['title'] = $newEventData['title'] . ' (Copy)';
            $newEventData['status'] = 'draft';
            $newEventData['is_featured'] = false;
            $newEventData['views'] = 0;
            $newEventData['current_attendees'] = 0;

            return $this->eventRepository->create($newEventData);
        });
    }

    protected function validateEventData(array $data, ?int $id = null): void
    {
        $rules = [
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:event_categories,id',
            'description' => 'required|string',
            'short_description' => 'nullable|string|max:500',
            'venue' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'registration_deadline' => 'nullable|date|before:start_date',
            'max_attendees' => 'nullable|integer|min:1',
            'price' => 'nullable|numeric|min:0',
            'currency' => 'required_with:price|string|size:3',
            'is_virtual' => 'sometimes|boolean',
            'virtual_link' => 'required_if:is_virtual,true|nullable|url',
            'status' => 'sometimes|in:draft,published,cancelled',
            'is_featured' => 'sometimes|boolean',
            'publish_at' => 'nullable|date',
            'meta_data' => 'nullable|array',
        ];

        // Add custom validation for publish_at
        $validator = Validator::make($data, $rules);

        $validator->sometimes('publish_at', 'after:now', function ($input) {
            return isset($input['status']) && $input['status'] === 'draft';
        });

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Schedule events for publishing (to be called by cron job)
     */
    public function processScheduledPublications(): int
    {
        $events = Event::where('status', 'draft')
            ->whereNotNull('publish_at')
            ->where('publish_at', '<=', now())
            ->get();

        $count = 0;
        foreach ($events as $event) {
            $event->update([
                'status' => 'published',
                'published_at' => now(),
                'publish_at' => null
            ]);
            $count++;
        }

        return $count;
    }
}