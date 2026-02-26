<?php

namespace App\Modules\Events\Repositories;

use App\Modules\Core\Base\BaseRepository;
use App\Modules\Events\Models\EventCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CategoryRepository extends BaseRepository
{
    public function __construct(EventCategory $model)
    {
        parent::__construct($model);
    }

    /**
     * Get all active categories with event counts.
     */
    public function getActiveWithCounts(): Collection
    {
        return Cache::remember('categories_with_counts', 3600, function () {
            return $this->model
                ->active()
                ->ordered()
                ->withCount(['events' => function ($query) {
                    $query->published()->upcoming();
                }])
                ->get();
        });
    }

    /**
     * Find category by slug.
     */
    public function findBySlug(string $slug): ?EventCategory
    {
        return $this->model
            ->where('slug', $slug)
            ->first();
    }

    /**
     * Get popular categories based on event count.
     */
    public function getPopular(int $limit = 5): Collection
    {
        return $this->model
            ->active()
            ->withCount('events')
            ->orderBy('events_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get categories as key-value pairs for dropdowns.
     */
    public function getForDropdown(): array
    {
        return $this->model
            ->active()
            ->ordered()
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * Update category order.
     */
    public function updateOrder(array $order): bool
    {
        foreach ($order as $position => $categoryId) {
            $this->update($categoryId, ['sort_order' => $position]);
        }
        
        Cache::forget('categories_with_counts');
        
        return true;
    }
}