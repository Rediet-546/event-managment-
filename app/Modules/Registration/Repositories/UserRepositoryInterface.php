<?php

namespace App\Modules\Registration\Repositories;

use App\Modules\Registration\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;

interface UserRepositoryInterface
{
    /**
     * Find user by ID
     * 
     * @param int $id
     * @return User|null
     */
    public function findById(int $id): ?User;

    /**
     * Find user by ID or fail
     * 
     * @param int $id
     * @return User
     * @throws ModelNotFoundException
     */
    public function findOrFail(int $id): User;

    /**
     * Find user by email
     * 
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find user by username
     * 
     * @param string $username
     * @return User|null
     */
    public function findByUsername(string $username): ?User;

    /**
     * Find user by email or username (for login)
     * 
     * @param string $login
     * @return User|null
     */
    public function findByEmailOrUsername(string $login): ?User;

    /**
     * Get all users with optional filters
     * 
     * @param array $filters
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    /**
     * Create new user
     * 
     * @param array $data
     * @return User
     */
    public function create(array $data): User;

    /**
     * Update user
     * 
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool;

    /**
     * Delete user
     * 
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool;

    /**
     * Force delete user (permanent)
     * 
     * @param int $id
     * @return bool
     */
    public function forceDelete(int $id): bool;

    /**
     * Restore soft-deleted user
     * 
     * @param int $id
     * @return bool
     */
    public function restore(int $id): bool;

    /**
     * Get active users
     * 
     * @return Collection
     */
    public function getActiveUsers(): Collection;

    /**
     * Get inactive users
     * 
     * @return Collection
     */
    public function getInactiveUsers(): Collection;

    /**
     * Get users by type (attendee/creator)
     * 
     * @param string $type
     * @return Collection
     */
    public function getUsersByType(string $type): Collection;

    /**
     * Get users by age range
     * 
     * @param int $min
     * @param int $max
     * @return Collection
     */
    public function getUsersByAgeRange(int $min, int $max): Collection;

    /**
     * Get pending creators (unapproved event creators)
     * 
     * @return Collection
     */
    public function getPendingCreators(): Collection;

    /**
     * Get approved creators
     * 
     * @return Collection
     */
    public function getApprovedCreators(): Collection;

    /**
     * Get users with role
     * 
     * @param string $role
     * @return Collection
     */
    public function getUsersByRole(string $role): Collection;

    /**
     * Get users registered between dates
     * 
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getUsersRegisteredBetween(string $startDate, string $endDate): Collection;

    /**
     * Get users who logged in between dates
     * 
     * @param string $startDate
     * @param string $endDate
     * @return Collection
     */
    public function getUsersLoggedInBetween(string $startDate, string $endDate): Collection;

    /**
     * Count total users
     * 
     * @return int
     */
    public function count(): int;

    /**
     * Count users by type
     * 
     * @param string $type
     * @return int
     */
    public function countByType(string $type): int;

    /**
     * Count users by role
     * 
     * @param string $role
     * @return int
     */
    public function countByRole(string $role): int;

    /**
     * Update last login information
     * 
     * @param int $id
     * @param string $ip
     * @return bool
     */
    public function updateLastLogin(int $id, string $ip): bool;

    /**
     * Toggle user active status
     * 
     * @param int $id
     * @return bool
     */
    public function toggleActive(int $id): bool;

    /**
     * Approve creator account
     * 
     * @param int $id
     * @param int $approvedBy
     * @return bool
     */
    public function approveCreator(int $id, int $approvedBy): bool;

    /**
     * Reject creator account
     * 
     * @param int $id
     * @return bool
     */
    public function rejectCreator(int $id): bool;

    /**
     * Bulk approve creators
     * 
     * @param array $ids
     * @param int $approvedBy
     * @return int Number of approved creators
     */
    public function bulkApproveCreators(array $ids, int $approvedBy): int;

    /**
     * Get users with pending email verification
     * 
     * @return Collection
     */
    public function getUnverifiedUsers(): Collection;

    /**
     * Get users who haven't logged in for X days
     * 
     * @param int $days
     * @return Collection
     */
    public function getInactiveSince(int $days): Collection;

    /**
     * Search users by keyword
     * 
     * @param string $keyword
     * @param int $limit
     * @return Collection
     */
    public function search(string $keyword, int $limit = 10): Collection;

    /**
     * Get registration statistics
     * 
     * @param string $period (day, week, month, year)
     * @return array
     */
    public function getRegistrationStats(string $period = 'month'): array;

    /**
     * Get user statistics by age group
     * 
     * @return array
     */
    public function getAgeGroupStats(): array;

    /**
     * Get user statistics by country
     * 
     * @return Collection
     */
    public function getCountryStats(): Collection;

    /**
     * Get user growth chart data
     * 
     * @param int $days
     * @return Collection
     */
    public function getUserGrowthData(int $days = 30): Collection;

    /**
     * Check if email exists (excluding specific user)
     * 
     * @param string $email
     * @param int|null $excludeId
     * @return bool
     */
    public function emailExists(string $email, ?int $excludeId = null): bool;

    /**
     * Check if username exists (excluding specific user)
     * 
     * @param string $username
     * @param int|null $excludeId
     * @return bool
     */
    public function usernameExists(string $username, ?int $excludeId = null): bool;

    /**
     * Get users for export
     * 
     * @param array $filters
     * @return Collection
     */
    public function getForExport(array $filters = []): Collection;

    /**
     * Bulk delete users
     * 
     * @param array $ids
     * @return int Number of deleted users
     */
    public function bulkDelete(array $ids): int;

    /**
     * Bulk update users
     * 
     * @param array $ids
     * @param array $data
     * @return int Number of updated users
     */
    public function bulkUpdate(array $ids, array $data): int;

    /**
     * Get users with most bookings
     * 
     * @param int $limit
     * @return Collection
     */
    public function getTopAttendees(int $limit = 10): Collection;

    /**
     * Get creators with most events
     * 
     * @param int $limit
     * @return Collection
     */
    public function getTopCreators(int $limit = 10): Collection;

    /**
     * Get user with relationships loaded
     * 
     * @param int $id
     * @param array $relations
     * @return User|null
     */
    public function findWithRelations(int $id, array $relations = []): ?User;

    /**
     * Begin database transaction
     * 
     * @return void
     */
    public function beginTransaction(): void;

    /**
     * Commit database transaction
     * 
     * @return void
     */
    public function commitTransaction(): void;

    /**
     * Rollback database transaction
     * 
     * @return void
     */
    public function rollbackTransaction(): void;
}