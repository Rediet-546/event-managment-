<?php

namespace App\Modules\Registration\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Registration\Models\User;
use App\Modules\Registration\Http\Requests\LoginRequest;
use App\Modules\Registration\Http\Requests\RegisterRequest;
use App\Modules\Registration\Services\AuthService;
use App\Modules\Registration\Notifications\WelcomeEmail;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showWelcome()
    {
        return view('registration::welcome');
    }

    public function showLoginForm()
    {
        return view('registration::login');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');
        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            if ($user->hasRole('super-admin') || $user->hasRole('admin')) {
                if (Route::has('admin.dashboard')) {
                    return redirect()->intended(route('admin.dashboard', [], false))
                        ->with('success', 'Welcome back, ' . ($user->first_name ?? $user->name) . '!');
                }
                return redirect()->intended(route('events.index', [], false))
                    ->with('success', 'Welcome back, ' . ($user->first_name ?? $user->name) . '!');
            }

            if ($user->user_type === 'event_creator') {
                if (!$user->is_approved) {
                    Auth::logout();
                    return back()->withErrors([
                        'email' => 'Your creator account is pending approval.'
                    ])->onlyInput('email');
                }

                if (Route::has('creator.dashboard')) {
                    return redirect()->intended(route('creator.dashboard', [], false))
                        ->with('success', 'Welcome back, ' . ($user->first_name ?? $user->name) . '!');
                }
                return redirect()->intended(route('events.index', [], false))
                    ->with('success', 'Welcome back, ' . ($user->first_name ?? $user->name) . '!');
            }

            if (Route::has('attendee.dashboard')) {
                return redirect()->intended(route('attendee.dashboard', [], false))
                    ->with('success', 'Welcome back, ' . ($user->first_name ?? $user->name) . '!');
            }

            return redirect()->intended(route('events.index', [], false))
                ->with('success', 'Welcome back, ' . ($user->first_name ?? $user->name) . '!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        return view('registration::register');
    }

    public function register(RegisterRequest $request)
    {
        try {
            // Create user
            $user = User::create([
                'name' => trim($request->first_name . ' ' . $request->last_name),
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'age' => $request->age,
                'user_type' => $request->user_type ?? 'attendee',
            ]);

            if (method_exists($user, 'assignRole')) {
                $user->assignRole('user');
            }

            try {
                $user->notify(new WelcomeEmail($user));
            } catch (\Exception $e) {
                \Log::error('Welcome email failed: ' . $e->getMessage());
            }

            // ✅ FIXED: LOG THE USER IN!
            Auth::login($user);

            return redirect()->route('events.index')
                ->with('success', 'Registration successful! Welcome ' . ($user->first_name ?? $user->name) . '!');

        } catch (\Exception $e) {
            return back()->with('error', 'Registration failed: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();  // ✅ FIXED!

        return redirect('/')
            ->with('success', 'You have been logged out successfully.');
    }

    public function showVerificationNotice()
    {
        return view('registration::verify-email-notice');
    }

    public function verifyEmail($id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')
                ->with('error', 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('info', 'Email already verified.');
        }

        $user->markEmailAsVerified();

        return redirect()->route('login')
            ->with('success', 'Email verified successfully! You can now login.');
    }

    public function resendVerification(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to resend verification.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('events.index')
                ->with('info', 'Email already verified.');
        }

        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Verification email sent.');
    }

    public function showForgotPassword()
    {
        return view('registration::forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['success' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm(string $token)
    {
        return view('registration::reset-password', ['token' => $token]);
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
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new \Illuminate\Auth\Events\PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))  // ← FIXED!
            : back()->withErrors(['email' => [__($status)]]);
    }
}
