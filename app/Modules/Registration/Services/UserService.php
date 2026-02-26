<?php

namespace App\Modules\Registration\Services;

use App\Modules\Registration\Models\User;
use App\Modules\Registration\Repositories\UserRepositoryInterface;
use App\Modules\Registration\Notifications\CreatorApproved;
use App\Modules\Registration\Notifications\CreatorRejected;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserService
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * UserService constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Update user profile
     *
     * @param int $userId
     * @param array $data
     * @return bool
     */
    public function updateProfile(int $userId, array $data): bool
    {
        $user = $this->userRepository->findOrFail($userId);
        
        // Separate user and profile data
        $userData = array_intersect_key($data, array_flip([
            'first_name', 'last_name', 'email', 'username'
        ]));
        
        $profileData = array_intersect_key($data, array_flip([
            'phone', 'address_line1', 'address_line2', 'city', 
            'state', 'postal_code', 'country', 'bio'
        ]));

        // Begin transaction
        $this->userRepository->beginTransaction();

        try {
            // Check email uniqueness if changed
            if (isset($userData['email']) && $userData['email'] !== $user->email) {
                if ($this->userRepository->emailExists($userData['email'], $userId)) {
                    throw new \Exception('Email already taken.');
                }
            }

            // Check username uniqueness if changed
            if (isset($userData['username']) && $userData['username'] !== $user->username) {
                if ($this->userRepository->usernameExists($userData['username'], $userId)) {
                    throw new \Exception('Username already taken.');
                }
            }

            // Update user
            if (!empty($userData)) {
                $user->update($userData);
            }

            // Update profile
            if (!empty($profileData)) {
                $user->profile()->update($profileData);
            }

            $this->userRepository->commitTransaction();
            
            activity()
                ->performedOn($user)
                ->withProperties(['updated_fields' => array_keys($data)])
                ->log('Profile updated');

            return true;

        } catch (\Exception $e) {
            $this->userRepository->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Upload avatar
     *
     * @param int $userId
     * @param UploadedFile $avatar
     * @return string
     */
    public function uploadAvatar(int $userId, UploadedFile $avatar): string
    {
        $user = $this->userRepository->findOrFail($userId);
        
        // Delete old avatar
        if ($user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
        }

        // Store new avatar
        $path = $avatar->store('avatars/' . $userId, 'public');
        
        // Update profile
        $user->profile()->update(['avatar' => $path]);
        
        activity()
            ->performedOn($user)
            ->log('Avatar updated');

        return $path;
    }

    /**
     * Change password
     *
     * @param int $userId
     * @param string $currentPassword
     * @param string $newPassword
     * @return bool
     */
    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->userRepository->findOrFail($userId);
        
        if (!Hash::check($currentPassword, $user->password)) {
            return false;
        }

        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        activity()
            ->performedOn($user)
            ->log('Password changed');

        return true;
    }

    /**
     * Approve creator account
     *
     * @param int $userId
     * @param int $approvedBy
     * @return bool
     */
    public function approveCreator(int $userId, int $approvedBy): bool
    {
        $user = $this->userRepository->findOrFail($userId);
        
        if ($user->user_type !== 'event_creator') {
            throw new \Exception('User is not an event creator.');
        }

        if ($user->is_approved) {
            return true; // Already approved
        }

        $result = $this->userRepository->approveCreator($userId, $approvedBy);
        
        if ($result) {
            $user->notify(new CreatorApproved($user));
            
            activity()
                ->performedOn($user)
                ->withProperties(['approved_by' => $approvedBy])
                ->log('Creator approved');
        }

        return $result;
    }

    /**
     * Reject creator account
     *
     * @param int $userId
     * @param string $reason
     * @return bool
     */
    public function rejectCreator(int $userId, string $reason): bool
    {
        $user = $this->userRepository->findOrFail($userId);
        
        if ($user->user_type !== 'event_creator') {
            throw new \Exception('User is not an event creator.');
        }

        $result = $this->userRepository->rejectCreator($userId);
        
        if ($result) {
            $user->notify(new CreatorRejected($user, $reason));
            
            activity()
                ->performedOn($user)
                ->withProperties(['reason' => $reason])
                ->log('Creator rejected');
        }

        return $result;
    }

    /**
     * Bulk approve creators
     *
     * @param array $userIds
     * @param int $approvedBy
     * @return int
     */
    public function bulkApproveCreators(array $userIds, int $approvedBy): int
    {
        $count = $this->userRepository->bulkApproveCreators($userIds, $approvedBy);
        
        activity()
            ->withProperties(['approved_count' => $count, 'approved_by' => $approvedBy])
            ->log('Bulk creator approval');

        return $count;
    }

    /**
     * Get dashboard data for user
     *
     * @param User $user
     * @return array
     */
    public function getDashboardData(User $user): array
    {
        $data = [
            'user' => $user,
            'statistics' => [],
            'recent_activity' => []
        ];

        if ($user->isAttendee()) {
            $data['statistics'] = [
                'total_bookings' => $user->bookings()->count(),
                'upcoming_events' => $user->bookings()
                    ->whereHas('event', fn($q) => $q->where('start_date', '>', now()))
                    ->count(),
                'past_events' => $user->bookings()
                    ->whereHas('event', fn($q) => $q->where('end_date', '<', now()))
                    ->count(),
                'total_spent' => $user->payments()->sum('amount')
            ];

            $data['upcoming_bookings'] = $user->bookings()
                ->with('event')
                ->whereHas('event', fn($q) => $q->where('start_date', '>', now()))
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        }

        if ($user->isEventCreator()) {
            $data['statistics'] = [
                'total_events' => $user->createdEvents()->count(),
                'active_events' => $user->createdEvents()
                    ->where('status', 'published')
                    ->where('end_date', '>', now())
                    ->count(),
                'total_bookings' => $user->createdEvents()
                    ->withCount('bookings')
                    ->get()
                    ->sum('bookings_count'),
                'total_revenue' => $user->createdEvents()
                    ->withSum('bookings', 'total_amount')
                    ->get()
                    ->sum('bookings_sum_total_amount')
            ];

            $data['recent_events'] = $user->createdEvents()
                ->withCount('bookings')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            $data['recent_bookings'] = \App\Modules\Attendee\Models\Booking::whereIn('event_id', 
                $user->createdEvents()->pluck('id')
            )->with('event', 'user')
             ->orderBy('created_at', 'desc')
             ->limit(10)
             ->get();
        }

        // Add recent activity
        $data['recent_activity'] = activity()
            ->causedBy($user)
            ->latest()
            ->limit(10)
            ->get();

        return $data;
    }

    /**
     * Get user statistics for analytics
     *
     * @param string $period
     * @return array
     */
    public function getUserStatistics(string $period = 'month'): array
    {
        return [
            'overview' => [
                'total' => $this->userRepository->count(),
                'attendees' => $this->userRepository->countByType('attendee'),
                'creators' => $this->userRepository->countByType('event_creator'),
                'admins' => $this->userRepository->countByRole('admin'),
                'super_admins' => $this->userRepository->countByRole('super-admin'),
                'pending_creators' => $this->userRepository->getPendingCreators()->count()
            ],
            'registration_stats' => $this->userRepository->getRegistrationStats($period),
            'age_groups' => $this->userRepository->getAgeGroupStats(),
            'growth_data' => $this->userRepository->getUserGrowthData(30),
            'top_attendees' => $this->userRepository->getTopAttendees(5),
            'top_creators' => $this->userRepository->getTopCreators(5)
        ];
    }

    /**
     * Search users
     *
     * @param string $query
     * @param array $filters
     * @return array
     */
    public function searchUsers(string $query, array $filters = []): array
    {
        $users = $this->userRepository->search($query);
        
        $results = [];
        foreach ($users as $user) {
            $results[] = [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'username' => $user->username,
                'type' => $user->user_type,
                'avatar' => $user->profile->avatar_url,
                'url' => route('admin.users.show', $user)
            ];
        }

        return $results;
    }

    /**
     * Export users data
     *
     * @param array $filters
     * @return array
     */
    public function exportUsers(array $filters = []): array
    {
        $users = $this->userRepository->getForExport($filters);
        
        $exportData = [];
        foreach ($users as $user) {
            $exportData[] = [
                'ID' => $user->id,
                'First Name' => $user->first_name,
                'Last Name' => $user->last_name,
                'Email' => $user->email,
                'Username' => $user->username,
                'Type' => $user->user_type,
                'Status' => $user->is_active ? 'Active' : 'Inactive',
                'Verified' => $user->hasVerifiedEmail() ? 'Yes' : 'No',
                'Registered' => $user->created_at->format('Y-m-d H:i:s'),
                'Last Login' => $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never',
                'Organization' => $user->organization_name ?? 'N/A',
                'Phone' => $user->phone ?? 'N/A',
                'Country' => $user->profile->country ?? 'N/A'
            ];
        }

        return $exportData;
    }

    /**
     * Delete user account
     *
     * @param int $userId
     * @param string $password
     * @return bool
     */
    public function deleteAccount(int $userId, string $password): bool
    {
        $user = $this->userRepository->findOrFail($userId);
        
        if (!Hash::check($password, $user->password)) {
            return false;
        }

        // Delete avatar
        if ($user->profile->avatar) {
            Storage::disk('public')->delete($user->profile->avatar);
        }

        // Delete user (soft delete)
        $result = $this->userRepository->delete($userId);
        
        if ($result) {
            activity()
                ->performedOn($user)
                ->log('Account deleted');
        }

        return $result;
    }

    /**
     * Toggle user active status
     *
     * @param int $userId
     * @return bool
     */
    public function toggleUserStatus(int $userId): bool
    {
        $result = $this->userRepository->toggleActive($userId);
        
        $user = $this->userRepository->findById($userId);
        
        activity()
            ->performedOn($user)
            ->log($user->is_active ? 'Account activated' : 'Account deactivated');

        return $result;
    }

    /**
     * Get pending creators summary
     *
     * @return array
     */
    public function getPendingCreatorsSummary(): array
    {
        $pending = $this->userRepository->getPendingCreators();
        
        return [
            'count' => $pending->count(),
            'oldest' => $pending->isNotEmpty() ? $pending->first()->created_at->diffForHumans() : null,
            'recent' => $pending->take(5)->map(fn($user) => [
                'id' => $user->id,
                'name' => $user->full_name,
                'email' => $user->email,
                'organization' => $user->organization_name,
                'registered' => $user->created_at->diffForHumans()
            ])
        ];
    }

    /**
     * Update user role
     *
     * @param int $userId
     * @param string $role
     * @return bool
     */
    public function updateUserRole(int $userId, string $role): bool
    {
        $user = $this->userRepository->findOrFail($userId);
        
        // Remove all current roles
        $user->syncRoles([]);
        
        // Assign new role
        $user->assignRole($role);
        
        activity()
            ->performedOn($user)
            ->withProperties(['new_role' => $role])
            ->log('User role updated');

        return true;
    }
}