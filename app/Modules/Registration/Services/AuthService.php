<?php

namespace App\Modules\Registration\Services;

use App\Modules\Registration\Models\User;
use App\Modules\Registration\Repositories\UserRepositoryInterface;
use App\Modules\Registration\Notifications\WelcomeEmail;
use App\Modules\Registration\Notifications\CreatorPendingApproval;
use App\Modules\Registration\Notifications\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;

class AuthService
{
    /**
     * @var UserRepositoryInterface
     */
    protected $userRepository;

    /**
     * Maximum login attempts before lockout
     */
    const MAX_LOGIN_ATTEMPTS = 5;

    /**
     * Lockout duration in minutes
     */
    const LOCKOUT_DURATION = 15;

    /**
     * AuthService constructor.
     *
     * @param UserRepositoryInterface $userRepository
     */
    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Register a new user
     *
     * @param array $data
     * @return User
     */
    public function register(array $data): User
    {
        // Hash password
        $data['password'] = Hash::make($data['password']);
        
        // Generate username if not provided
        if (empty($data['username'])) {
            $data['username'] = $this->generateUniqueUsername($data['first_name'], $data['last_name']);
        }

        // Begin transaction
        $this->userRepository->beginTransaction();

        try {
            // Create user
            $user = $this->userRepository->create($data);
            
            // Create profile
            $user->profile()->create([]);
            
            // Assign role based on user type
            if ($user->user_type === 'attendee') {
                $user->assignRole('attendee');
            }
            
            // Send appropriate notification
            $this->sendRegistrationNotification($user);
            
            // Commit transaction
            $this->userRepository->commitTransaction();
            
            return $user;
            
        } catch (\Exception $e) {
            $this->userRepository->rollbackTransaction();
            throw $e;
        }
    }

    /**
     * Attempt to login user
     *
     * @param string $login
     * @param string $password
     * @param bool $remember
     * @return array
     */
    public function login(string $login, string $password, bool $remember = false): array
    {
        // Find user by email or username
        $user = $this->userRepository->findByEmailOrUsername($login);
        
        // Check if user exists
        if (!$user) {
            return [
                'success' => false,
                'message' => 'These credentials do not match our records.',
                'field' => 'login'
            ];
        }

        // Check if account is locked
        if ($this->isAccountLocked($user)) {
            $minutes = $this->getLockoutMinutes($user);
            return [
                'success' => false,
                'message' => "Too many login attempts. Please try again in {$minutes} minutes.",
                'field' => 'login'
            ];
        }

        // Verify password
        if (!Hash::check($password, $user->password)) {
            $this->incrementLoginAttempts($user);
            return [
                'success' => false,
                'message' => 'The provided password is incorrect.',
                'field' => 'password'
            ];
        }

        // Check if user is active
        if (!$user->is_active) {
            return [
                'success' => false,
                'message' => 'Your account has been deactivated. Please contact support.',
                'field' => 'login'
            ];
        }

        // Check email verification
        if (!$user->hasVerifiedEmail()) {
            return [
                'success' => false,
                'message' => 'Please verify your email address before logging in.',
                'field' => 'login',
                'resend_verification' => true,
                'email' => $user->email
            ];
        }

        // Check creator approval
        if ($user->isEventCreator() && !$user->is_approved) {
            return [
                'success' => false,
                'message' => 'Your creator account is pending approval. You will be notified once approved.',
                'field' => 'login',
                'pending_approval' => true
            ];
        }

        // Attempt login
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        
        if (Auth::attempt([$field => $login, 'password' => $password], $remember)) {
            // Update last login
            $this->userRepository->updateLastLogin($user->id, request()->ip());
            
            // Reset login attempts
            $this->resetLoginAttempts($user);
            
            // Regenerate session
            session()->regenerate();
            
            return [
                'success' => true,
                'user' => $user,
                'redirect' => $this->getDashboardRoute($user),
                'message' => $this->getWelcomeMessage($user)
            ];
        }

        return [
            'success' => false,
            'message' => 'Login failed. Please try again.',
            'field' => 'login'
        ];
    }

    /**
     * Logout user
     *
     * @return void
     */
    public function logout(): void
    {
        $user = Auth::user();
        
        if ($user) {
            activity()
                ->performedOn($user)
                ->log('User logged out');
        }
        
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
    }

    /**
     * Send password reset link
     *
     * @param string $email
     * @return string
     */
    public function sendResetLink(string $email): string
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user) {
            return Password::INVALID_USER;
        }

        // Check if account is active
        if (!$user->is_active) {
            return 'account_inactive';
        }

        // Create token
        $token = Password::createToken($user);
        
        // Send notification
        $user->notify(new PasswordReset($token));
        
        return Password::RESET_LINK_SENT;
    }

    /**
     * Reset password
     *
     * @param array $data
     * @return string
     */
    public function resetPassword(array $data): string
    {
        $response = Password::reset($data, function ($user, $password) {
            $user->password = Hash::make($password);
            $user->setRememberToken(Str::random(60));
            $user->save();
            
            activity()
                ->performedOn($user)
                ->log('Password reset');
        });

        return $response;
    }

    /**
     * Verify email
     *
     * @param int $userId
     * @param string $hash
     * @return bool
     */
    public function verifyEmail(int $userId, string $hash): bool
    {
        $user = $this->userRepository->findById($userId);
        
        if (!$user || $user->hasVerifiedEmail()) {
            return false;
        }
        
        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return false;
        }
        
        $user->markEmailAsVerified();
        
        activity()
            ->performedOn($user)
            ->log('Email verified');
        
        return true;
    }

    /**
     * Resend verification email
     *
     * @param string $email
     * @return bool
     */
    public function resendVerification(string $email): bool
    {
        $user = $this->userRepository->findByEmail($email);
        
        if (!$user || $user->hasVerifiedEmail()) {
            return false;
        }
        
        $user->sendEmailVerificationNotification();
        
        return true;
    }

    /**
     * Check if user can login
     *
     * @param User $user
     * @return array
     */
    public function checkLoginAbility(User $user): array
    {
        $errors = [];
        
        if (!$user->is_active) {
            $errors[] = 'Account is deactivated';
        }
        
        if (!$user->hasVerifiedEmail()) {
            $errors[] = 'Email not verified';
        }
        
        if ($user->isEventCreator() && !$user->is_approved) {
            $errors[] = 'Creator account pending approval';
        }
        
        return $errors;
    }

    /**
     * Get dashboard route based on user type
     *
     * @param User $user
     * @return string
     */
    public function getDashboardRoute(User $user): string
    {
        if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
            return route('admin.dashboard');
        }
        
        if ($user->isEventCreator()) {
            return route('creator.dashboard');
        }
        
        return route('attendee.dashboard');
    }

    /**
     * Get welcome message based on user type
     *
     * @param User $user
     * @return string
     */
    protected function getWelcomeMessage(User $user): string
    {
        if ($user->hasRole(['super-admin', 'admin'])) {
            return "Welcome back, {$user->first_name}! You have administrator access.";
        }
        
        if ($user->isEventCreator()) {
            return "Welcome back, {$user->first_name}! Ready to manage your events?";
        }
        
        return "Welcome back, {$user->first_name}! Ready for your next event?";
    }

    /**
     * Generate unique username
     *
     * @param string $firstName
     * @param string $lastName
     * @return string
     */
    protected function generateUniqueUsername(string $firstName, string $lastName): string
    {
        $base = Str::slug($firstName . '.' . $lastName);
        $username = $base;
        $counter = 1;
        
        while ($this->userRepository->usernameExists($username)) {
            $username = $base . $counter;
            $counter++;
        }
        
        return $username;
    }

    /**
     * Send registration notification based on user type
     *
     * @param User $user
     * @return void
     */
    protected function sendRegistrationNotification(User $user): void
    {
        if ($user->user_type === 'attendee') {
            $user->notify(new WelcomeEmail($user));
        } else {
            $user->notify(new CreatorPendingApproval($user));
            
            // Notify admins about pending creator
            $this->notifyAdminsAboutPendingCreator($user);
        }
    }

    /**
     * Notify admins about pending creator
     *
     * @param User $creator
     * @return void
     */
    protected function notifyAdminsAboutPendingCreator(User $creator): void
    {
        $admins = User::role(['super-admin', 'admin'])->get();
        
        foreach ($admins as $admin) {
            $admin->notify(new \App\Modules\Registration\Notifications\PendingCreatorApproval($creator));
        }
    }

    /**
     * Check if account is locked
     *
     * @param User $user
     * @return bool
     */
    protected function isAccountLocked(User $user): bool
    {
        $attempts = cache()->get("login_attempts_{$user->id}", 0);
        return $attempts >= self::MAX_LOGIN_ATTEMPTS;
    }

    /**
     * Get lockout minutes remaining
     *
     * @param User $user
     * @return int
     */
    protected function getLockoutMinutes(User $user): int
    {
        $lockoutTime = cache()->get("login_lockout_{$user->id}");
        return $lockoutTime ? now()->diffInMinutes($lockoutTime) : 0;
    }

    /**
     * Increment login attempts
     *
     * @param User $user
     * @return void
     */
    protected function incrementLoginAttempts(User $user): void
    {
        $attempts = cache()->get("login_attempts_{$user->id}", 0) + 1;
        cache()->put("login_attempts_{$user->id}", $attempts, now()->addMinutes(self::LOCKOUT_DURATION));
        
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            cache()->put("login_lockout_{$user->id}", now()->addMinutes(self::LOCKOUT_DURATION), now()->addMinutes(self::LOCKOUT_DURATION));
        }
    }

    /**
     * Reset login attempts
     *
     * @param User $user
     * @return void
     */
    protected function resetLoginAttempts(User $user): void
    {
        cache()->forget("login_attempts_{$user->id}");
        cache()->forget("login_lockout_{$user->id}");
    }

    /**
     * Validate login input
     *
     * @param Request $request
     * @throws ValidationException
     */
    public function validateLogin(Request $request): void
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);
    }

    /**
     * Validate registration input
     *
     * @param Request $request
     * @throws ValidationException
     */
    public function validateRegistration(Request $request): void
    {
        $rules = [
            'first_name' => 'required|string|max:100',
            'last_name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'user_type' => 'required|in:attendee,event_creator',
            'terms' => 'required|accepted'
        ];

        if ($request->user_type === 'attendee') {
            $rules['age'] = 'required|integer|min:18|max:120';
        } else {
            $rules['phone'] = 'required|string|max:20';
            $rules['organization_name'] = 'required|string|max:255';
        }

        $request->validate($rules);
    }
}