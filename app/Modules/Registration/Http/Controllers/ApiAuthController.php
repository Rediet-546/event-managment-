<?php

namespace App\Modules\Registration\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Registration\Models\User;
use App\Modules\Registration\Http\Requests\RegisterRequest;
use App\Modules\Registration\Http\Requests\LoginRequest;
use App\Modules\Registration\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ApiAuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * API Login
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Account is deactivated'
            ], 403);
        }

        // Check creator approval
        if ($user->isEventCreator() && !$user->is_approved) {
            return response()->json([
                'success' => false,
                'message' => 'Creator account pending approval'
            ], 403);
        }

        // Create token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Update last login
        $user->updateLastLogin();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
                'dashboard_url' => $user->dashboard_url
            ]
        ]);
    }
    // Add these methods to your ApiAuthController class

public function sendResetLink(Request $request)
{
    $request->validate(['email' => 'required|email']);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    return $status === Password::RESET_LINK_SENT
        ? response()->json(['success' => true, 'message' => __($status)])
        : response()->json(['success' => false, 'message' => __($status)], 400);
}

public function resetPassword(Request $request)
{
    $request->validate([
        'token' => 'required',
        'email' => 'required|email',
        'password' => 'required|min:8|confirmed',
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function ($user, $password) {
            $user->password = Hash::make($password);
            $user->save();
        }
    );

    return $status === Password::PASSWORD_RESET
        ? response()->json(['success' => true, 'message' => __($status)])
        : response()->json(['success' => false, 'message' => __($status)], 400);
}

public function refreshToken(Request $request)
{
    $user = $request->user();
    $user->tokens()->delete();
    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'success' => true,
        'token' => $token,
        'token_type' => 'Bearer'
    ]);
}

public function getUser(Request $request)
{
    return response()->json([
        'success' => true,
        'data' => $request->user()->load('profile', 'roles')
    ]);
}

public function changePassword(Request $request)
{
    $request->validate([
        'current_password' => 'required|current_password',
        'new_password' => 'required|min:8|confirmed'
    ]);

    $user = $request->user();
    $user->password = Hash::make($request->new_password);
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Password changed successfully'
    ]);
}

public function deleteAccount(Request $request)
{
    $request->validate([
        'password' => 'required|current_password'
    ]);

    $user = $request->user();
    $user->tokens()->delete();
    $user->delete();

    return response()->json([
        'success' => true,
        'message' => 'Account deleted successfully'
    ]);
}

public function getDashboardStats(Request $request)
{
    $user = $request->user();

    $stats = [
        'total_bookings' => $user->bookings()->count(),
        'upcoming_events' => $user->bookings()
            ->whereHas('event', fn($q) => $q->where('start_date', '>', now()))
            ->count(),
        'total_spent' => $user->payments()->sum('amount'),
    ];

    return response()->json([
        'success' => true,
        'data' => $stats
    ]);
}

public function getCreatorStats(Request $request)
{
    $user = $request->user();

    $stats = [
        'total_events' => $user->createdEvents()->count(),
        'total_bookings' => $user->createdEvents()
            ->withCount('bookings')
            ->get()
            ->sum('bookings_count'),
        'total_revenue' => $user->createdEvents()
            ->withSum('bookings', 'total_amount')
            ->get()
            ->sum('bookings_sum_total_amount'),
    ];

    return response()->json([
        'success' => true,
        'data' => $stats
    ]);
}

public function getCreatorEvents(Request $request)
{
    $events = $request->user()->createdEvents()
        ->withCount('bookings')
        ->orderBy('created_at', 'desc')
        ->paginate(15);

    return response()->json([
        'success' => true,
        'data' => $events
    ]);
}

public function getCreatorAnalytics(Request $request)
{
    // Simplified analytics
    return response()->json([
        'success' => true,
        'data' => [
            'message' => 'Analytics endpoint - implement as needed'
        ]
    ]);
}
    /**
     * API Register
     */
    public function register(RegisterRequest $request)
    {
        try {
            $userData = $request->validated();
            $userData['password'] = Hash::make($userData['password']);

            $user = User::create($userData);

            // Create profile
            $user->profile()->create([]);

            // Assign role based on user type
            if ($user->user_type === 'attendee') {
                $user->assignRole('attendee');
            }

            // Create token
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => $user->user_type === 'attendee'
                    ? 'Registration successful'
                    : 'Registration submitted for approval',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'requires_approval' => $user->user_type === 'event_creator'
                ]
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API Logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ]);
    }

    /**
     * Get authenticated user
     */
    public function user(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()->load('profile', 'roles')
        ]);
    }

    /**
     * Refresh token
     */
    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'token' => $token
        ]);
    }
}
