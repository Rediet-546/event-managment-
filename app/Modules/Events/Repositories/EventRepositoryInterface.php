<?php

namespace App\Modules\Events\Repositories;

use App\Modules\Core\Interfaces\RepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

interface EventRepositoryInterface extends RepositoryInterface
{
    public function getUpcomingEvents(int $perPage = 10): LengthAwarePaginator;
    public function getFeaturedEvents(int $limit = 5): array;
    public function searchEvents(array $filters): LengthAwarePaginator;
    public function getEventsByCategory(int $categoryId, int $perPage = 10): LengthAwarePaginator;
    public function getEventsByCity(string $city, int $perPage = 10): LengthAwarePaginator;
    public function incrementEventViews(int $eventId): bool;
}