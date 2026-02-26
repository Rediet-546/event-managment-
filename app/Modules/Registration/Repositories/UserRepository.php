<?php

namespace App\Modules\Registration\Repositories;

use App\Modules\Registration\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Carbon\Carbon;

class UserRepository implements UserRepositoryInterface
{
    /**
     * @var User
     */
    protected $model;

    /**
     * Cache time in seconds (1 hour)
     */
    const CACHE_TIME = 3600;

    /**
     * UserRepository constructor.
     *
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id): ?User
    {
        return Cache::remember("user.{$id}", self::CACHE_TIME, function () use ($id) {
            return $this->model->with(['profile', 'roles'])->find($id);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function findOrFail(int $id): User
    {
        $user = $this->findById($id);
        
        if (!$user) {
            throw (new ModelNotFoundException)->setModel(
                User::class, $id
            );
        }
        
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findByUsername(string $username): ?User
    {
        return $this->model->where('username', $username)->first();
    }

    /**
     * {@inheritdoc}
     */
    public function findByEmailOrUsername(string $login): ?User
    {
        return $this->model->where('email', $login)
            ->orWhere('username', $login)
            ->first();
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->query()->with(['profile', 'roles']);

        // Apply search filter
        if (isset($filters['search']) && !empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%")
                  ->orWhere('username', 'LIKE', "%{$search}%");
            });
        }

        // Apply user type filter
        if (isset($filters['user_type']) && !empty($filters['user_type'])) {
            if ($filters['user_type'] === 'all') {
                // No filter
            } elseif ($filters['user_type'] === 'pending_creators') {
                $query->where('user_type', 'event_creator')
                      ->where('is_approved', false);
            } else {
                $query->where('user_type', $filters['user_type']);
            }
        }

        // Apply status filter
        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Apply approval status for creators
        if (isset($filters['approval_status'])) {
            if ($filters['approval_status'] === 'approved') {
                $query->where('user_type', 'event_creator')
                      ->where('is_approved', true);
            } elseif ($filters['approval_status'] === 'pending') {
                $query->where('user_type', 'event_creator')
                      ->where('is_approved', false);
            }
        }

        // Apply role filter
        if (isset($filters['role']) && !empty($filters['role'])) {
            $query->role($filters['role']);
        }

        // Apply age range filter
        if (isset($filters['age_from'])) {
            $query->where('age', '>=', $filters['age_from']);
        }
        if (isset($filters['age_to'])) {
            $query->where('age', '<=', $filters['age_to']);
        }

        // Apply date range filter
        if (isset($filters['registered_from'])) {
            $query->whereDate('created_at', '>=', $filters['registered_from']);
        }
        if (isset($filters['registered_to'])) {
            $query->whereDate('created_at', '<=', $filters['registered_to']);
        }

        // Apply last login filter
        if (isset($filters['last_login_from'])) {
            $query->whereDate('last_login_at', '>=', $filters['last_login_from']);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        $sortOrder = $filters['sort_order'] ?? 'desc';
        $query->orderBy($sortBy, $sortOrder);

        // Apply pagination
        return $query->paginate($perPage);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data): User
    {
        $user = $this->model->create($data);
        
        // Clear cache
        $this->clearCache();
        
        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function update(int $id, array $data): bool
    {
        $user = $this->findOrFail($id);
        $result = $user->update($data);
        
        // Clear cache
        $this->clearCache($id);
        
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(int $id): bool
    {
        $user = $this->findOrFail($id);
        $result = $user->delete();
        
        // Clear cache
        $this->clearCache($id);
        
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function forceDelete(int $id): bool
    {
        $user = $this->findOrFail($id);
        $result = $user->forceDelete();
        
        // Clear cache
        $this->clearCache($id);
        
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function restore(int $id): bool
    {
        $user = User::withTrashed()->findOrFail($id);
        $result = $user->restore();
        
        // Clear cache
        $this->clearCache($id);
        
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function getActiveUsers(): Collection
    {
        return $this->model->active()->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getInactiveUsers(): Collection
    {
        return $this->model->where('is_active', false)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsersByType(string $type): Collection
    {
        return $this->model->where('user_type', $type)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsersByAgeRange(int $min, int $max): Collection
    {
        return $this->model->whereBetween('age', [$min, $max])->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getPendingCreators(): Collection
    {
        return Cache::remember('users.pending_creators', self::CACHE_TIME, function () {
            return $this->model->where('user_type', 'event_creator')
                ->where('is_approved', false)
                ->with('profile')
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function getApprovedCreators(): Collection
    {
        return $this->model->where('user_type', 'event_creator')
            ->where('is_approved', true)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsersByRole(string $role): Collection
    {
        return $this->model->role($role)->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsersRegisteredBetween(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getUsersLoggedInBetween(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('last_login_at', [$startDate, $endDate])->get();
    }

    /**
     * {@inheritdoc}
     */
    public function count(): int
    {
        return Cache::remember('users.count', self::CACHE_TIME, function () {
            return $this->model->count();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function countByType(string $type): int
    {
        return $this->model->where('user_type', $type)->count();
    }

    /**
     * {@inheritdoc}
     */
    public function countByRole(string $role): int
    {
        return Cache::remember("users.count.role.{$role}", self::CACHE_TIME, function () use ($role) {
            return $this->model->role($role)->count();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function updateLastLogin(int $id, string $ip): bool
    {
        return $this->update($id, [
            'last_login_at' => now(),
            'last_login_ip' => $ip
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toggleActive(int $id): bool
    {
        $user = $this->findOrFail($id);
        return $this->update($id, ['is_active' => !$user->is_active]);
    }

    /**
     * {@inheritdoc}
     */
    public function approveCreator(int $id, int $approvedBy): bool
    {
        $result = $this->update($id, [
            'is_approved' => true,
            'approved_at' => now(),
            'approved_by' => $approvedBy
        ]);

        if ($result) {
            $user = $this->findById($id);
            $user->assignRole('event-creator');
            
            // Clear specific caches
            Cache::forget('users.pending_creators');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function rejectCreator(int $id): bool
    {
        $result = $this->update($id, [
            'is_approved' => false,
            'approved_at' => null,
            'approved_by' => null
        ]);

        if ($result) {
            Cache::forget('users.pending_creators');
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function bulkApproveCreators(array $ids, int $approvedBy): int
    {
        $count = 0;
        
        DB::transaction(function () use ($ids, $approvedBy, &$count) {
            foreach ($ids as $id) {
                if ($this->approveCreator($id, $approvedBy)) {
                    $count++;
                }
            }
        });

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnverifiedUsers(): Collection
    {
        return $this->model->whereNull('email_verified_at')->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getInactiveSince(int $days): Collection
    {
        $date = now()->subDays($days);
        return $this->model->where('last_login_at', '<', $date)
            ->orWhereNull('last_login_at')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function search(string $keyword, int $limit = 10): Collection
    {
        return $this->model->where('first_name', 'LIKE', "%{$keyword}%")
            ->orWhere('last_name', 'LIKE', "%{$keyword}%")
            ->orWhere('email', 'LIKE', "%{$keyword}%")
            ->orWhere('username', 'LIKE', "%{$keyword}%")
            ->limit($limit)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getRegistrationStats(string $period = 'month'): array
    {
        $query = $this->model->query();

        switch ($period) {
            case 'week':
                $start = Carbon::now()->startOfWeek();
                $groupBy = 'day';
                $format = '%Y-%m-%d';
                break;
            case 'month':
                $start = Carbon::now()->startOfMonth();
                $groupBy = 'day';
                $format = '%Y-%m-%d';
                break;
            case 'year':
                $start = Carbon::now()->startOfYear();
                $groupBy = 'month';
                $format = '%Y-%m';
                break;
            default:
                $start = Carbon::now()->subDays(30);
                $groupBy = 'day';
                $format = '%Y-%m-%d';
        }

        $stats = $query->where('created_at', '>=', $start)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '{$format}') as date"),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN user_type = 'attendee' THEN 1 ELSE 0 END) as attendees"),
                DB::raw("SUM(CASE WHEN user_type = 'event_creator' THEN 1 ELSE 0 END) as creators")
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $total = $stats->sum('total');
        $average = $stats->avg('total');

        return [
            'period' => $period,
            'total' => $total,
            'average' => round($average, 2),
            'attendees' => $stats->sum('attendees'),
            'creators' => $stats->sum('creators'),
            'daily' => $stats,
            'by_type' => [
                'attendee' => $this->countByType('attendee'),
                'event_creator' => $this->countByType('event_creator'),
                'admin' => $this->countByRole('admin'),
                'super_admin' => $this->countByRole('super-admin')
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getAgeGroupStats(): array
    {
        return [
            '18-24' => $this->model->whereBetween('age', [18, 24])->count(),
            '25-34' => $this->model->whereBetween('age', [25, 34])->count(),
            '35-44' => $this->model->whereBetween('age', [35, 44])->count(),
            '45-54' => $this->model->whereBetween('age', [45, 54])->count(),
            '55+' => $this->model->where('age', '>=', 55)->count(),
            'unknown' => $this->model->whereNull('age')->count()
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getCountryStats(): Collection
    {
        return DB::table('user_profiles')
            ->join('users', 'users.id', '=', 'user_profiles.user_id')
            ->select('country', DB::raw('count(*) as total'))
            ->whereNotNull('country')
            ->groupBy('country')
            ->orderBy('total', 'desc')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getUserGrowthData(int $days = 30): Collection
    {
        $startDate = Carbon::now()->subDays($days);
        
        return DB::table('users')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function emailExists(string $email, ?int $excludeId = null): bool
    {
        $query = $this->model->where('email', $email);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function usernameExists(string $username, ?int $excludeId = null): bool
    {
        $query = $this->model->where('username', $username);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->exists();
    }

    /**
     * {@inheritdoc}
     */
    public function getForExport(array $filters = []): Collection
    {
        $query = $this->model->query()->with('profile');

        if (isset($filters['user_type'])) {
            $query->where('user_type', $filters['user_type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * {@inheritdoc}
     */
    public function bulkDelete(array $ids): int
    {
        $count = 0;
        
        DB::transaction(function () use ($ids, &$count) {
            $count = $this->model->whereIn('id', $ids)->delete();
            
            // Clear cache for each deleted user
            foreach ($ids as $id) {
                Cache::forget("user.{$id}");
            }
        });

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function bulkUpdate(array $ids, array $data): int
    {
        $count = 0;
        
        DB::transaction(function () use ($ids, $data, &$count) {
            $count = $this->model->whereIn('id', $ids)->update($data);
            
            // Clear cache for each updated user
            foreach ($ids as $id) {
                Cache::forget("user.{$id}");
            }
        });

        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function getTopAttendees(int $limit = 10): Collection
    {
        return $this->model->where('user_type', 'attendee')
            ->withCount('bookings')
            ->withSum('bookings', 'total_amount')
            ->having('bookings_count', '>', 0)
            ->orderBy('bookings_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function getTopCreators(int $limit = 10): Collection
    {
        return $this->model->where('user_type', 'event_creator')
            ->where('is_approved', true)
            ->withCount('createdEvents')
            ->withCount('createdEvents as total_bookings')
            ->having('created_events_count', '>', 0)
            ->orderBy('created_events_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * {@inheritdoc}
     */
    public function findWithRelations(int $id, array $relations = []): ?User
    {
        return $this->model->with($relations)->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function beginTransaction(): void
    {
        DB::beginTransaction();
    }

    /**
     * {@inheritdoc}
     */
    public function commitTransaction(): void
    {
        DB::commit();
    }

    /**
     * {@inheritdoc}
     */
    public function rollbackTransaction(): void
    {
        DB::rollBack();
    }

    /**
     * Clear user cache
     *
     * @param int|null $userId
     * @return void
     */
    private function clearCache(?int $userId = null): void
    {
        if ($userId) {
            Cache::forget("user.{$userId}");
        }
        
        Cache::forget('users.count');
        Cache::forget('users.pending_creators');
        Cache::forget('users.count.role.super-admin');
        Cache::forget('users.count.role.admin');
        Cache::forget('users.count.role.event-creator');
        Cache::forget('users.count.role.attendee');
    }
}