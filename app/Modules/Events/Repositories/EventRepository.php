<?php

namespace App\Modules\Events\Repositories;

use App\Modules\Core\Base\BaseRepository;
use App\Modules\Events\Models\Event;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class EventRepository extends BaseRepository implements EventRepositoryInterface
{
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    public function getUpcomingEvents(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->published()
            ->upcoming()
            ->with(['category', 'primaryMedia'])
            ->paginate($perPage);
    }

    public function getFeaturedEvents(int $limit = 5): array
    {
        $cacheKey = 'featured_events_' . $limit;
        
        return Cache::remember($cacheKey, 3600, function () use ($limit) {
            return $this->model
                ->published()
                ->featured()
                ->upcoming()
                ->with(['category', 'primaryMedia'])
                ->limit($limit)
                ->get()
                ->toArray();
        });
    }

    public function searchEvents(array $filters): LengthAwarePaginator
    {
        $query = $this->model->published()->with(['category', 'primaryMedia']);

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('title', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('description', 'LIKE', "%{$filters['search']}%")
                  ->orWhere('venue', 'LIKE', "%{$filters['search']}%");
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        if (!empty($filters['city'])) {
            $query->where('city', 'LIKE', "%{$filters['city']}%");
        }

        if (!empty($filters['start_date'])) {
            $query->where('start_date', '>=', $filters['start_date']);
        }

        if (!empty($filters['end_date'])) {
            $query->where('end_date', '<=', $filters['end_date']);
        }

        if (!empty($filters['is_free'])) {
            $query->where('is_free', true);
        }

        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', $filters['price_max']);
        }

        // Sort
        $sortField = $filters['sort_by'] ?? 'start_date';
        $sortOrder = $filters['sort_order'] ?? 'asc';
        $query->orderBy($sortField, $sortOrder);

        return $query->paginate($filters['per_page'] ?? 15);
    }

    public function getEventsByCategory(int $categoryId, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->published()
            ->where('category_id', $categoryId)
            ->upcoming()
            ->with(['category', 'primaryMedia'])
            ->paginate($perPage);
    }

    public function getEventsByCity(string $city, int $perPage = 10): LengthAwarePaginator
    {
        return $this->model
            ->published()
            ->where('city', 'LIKE', "%{$city}%")
            ->upcoming()
            ->with(['category', 'primaryMedia'])
            ->paginate($perPage);
    }

    public function incrementEventViews(int $eventId): bool
    {
        return $this->model->where('id', $eventId)->increment('views');
    }
}