<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Events\Http\Controllers\EventController;
use App\Modules\Registration\Http\Controllers\AuthController;
use App\Modules\Registration\Http\Controllers\ProfileController;
use App\Modules\Registration\Http\Controllers\AttendeeDashboardController;
use App\Modules\Registration\Http\Controllers\CreatorDashboardController;
use App\Modules\Registration\Http\Controllers\VerificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ==================== PUBLIC ROUTES ====================
// Home/Welcome page - NOW SHOWING WELCOME PAGE FIRST!
Route::get('/', [AuthController::class, 'showWelcome'])->name('home');

// Events Routes (Public - can be viewed without login)
Route::prefix('events')->name('events.')->group(function () {
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/{id}', [EventController::class, 'show'])->name('show');
    Route::get('/calendar', [EventController::class, 'calendar'])->name('calendar');
});

// Test route to see users
Route::get('/see-users', function() {
    $users = App\Models\User::all();
    $output = "<h2>Registered Users:</h2>";
    $output .= "<table border='1' cellpadding='10'>";
    $output .= "<tr><th>ID</th><th>Name</th><th>Email</th><th>Type</th><th>Registered</th></tr>";
    foreach($users as $user) {
        $output .= "<tr>";
        $output .= "<td>{$user->id}</td>";
        $output .= "<td>{$user->first_name} {$user->last_name}</td>";
        $output .= "<td>{$user->email}</td>";
        $output .= "<td>{$user->user_type}</td>";
        $output .= "<td>{$user->created_at}</td>";
        $output .= "</tr>";
    }
    $output .= "</table>";
    return $output;
})->name('see.users');

// Static pages
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login');

Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Password Reset Routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Sign-up redirect routes
Route::get('/sign-up', function () { return redirect('/register'); })->name('events.sign-up');
Route::get('/register-now', function () { return redirect('/register'); })->name('events.register-now');
Route::get('/join-event', function () {
    session()->flash('message', 'Please create an account to join this event');
    return redirect('/register');
})->name('events.join');
Route::get('/signup', function () { return redirect('/register'); })->name('events.signup');
Route::get('/create-account', function () { return redirect('/register'); })->name('events.create-account');

// Test route
Route::get('/test-routing', function () { return 'Routing is working! Current time: ' . now(); });

// ==================== AUTHENTICATED ROUTES ====================
Route::middleware(['auth'])->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Profile Routes
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'changePassword'])->name('password');
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar'])->name('avatar');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy')->middleware('password.confirm');
    });

    // Email Verification
    Route::prefix('email')->name('verification.')->group(function () {
        Route::get('/verify', [VerificationController::class, 'showNotice'])->name('notice');
        Route::get('/verify/{id}/{hash}', [VerificationController::class, 'verify'])->name('verify')->middleware('signed');
        Route::post('/resend', [VerificationController::class, 'resend'])->name('resend')->middleware('throttle:2,1');
    });

    // Attendee Dashboard Routes
    Route::prefix('attendee')->name('attendee.')->group(function () {
        Route::get('/dashboard', [AttendeeDashboardController::class, 'index'])->name('dashboard');
        Route::get('/bookings', [AttendeeDashboardController::class, 'bookings'])->name('bookings');
        Route::get('/wallet', [AttendeeDashboardController::class, 'wallet'])->name('wallet');
        Route::get('/bookings/{id}', [AttendeeDashboardController::class, 'showBooking'])->name('show-booking');
        Route::post('/bookings/{id}/cancel', [AttendeeDashboardController::class, 'cancelBooking'])->name('cancel-booking');
    });

    // Creator Routes
    Route::prefix('creator')->name('creator.')->group(function () {
        Route::get('/pending', [CreatorDashboardController::class, 'pending'])->name('pending');
        Route::middleware(['creator.approved'])->group(function () {
            Route::get('/dashboard', [CreatorDashboardController::class, 'index'])->name('dashboard');
            Route::get('/analytics', [CreatorDashboardController::class, 'analytics'])->name('analytics');
            Route::get('/events/{event}/bookings', [CreatorDashboardController::class, 'eventBookings'])->name('event.bookings');
        });
    });
});

// ==================== ADMIN ROUTES ====================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'permission:manage users'])->group(function () {
    Route::get('/creators/pending', [\App\Modules\Registration\Http\Controllers\Admin\CreatorApprovalController::class, 'pending'])->name('creators.pending');
    Route::post('/creators/{user}/approve', [\App\Modules\Registration\Http\Controllers\Admin\CreatorApprovalController::class, 'approve'])->name('creators.approve');
    Route::post('/creators/{user}/reject', [\App\Modules\Registration\Http\Controllers\Admin\CreatorApprovalController::class, 'reject'])->name('creators.reject');
    Route::post('/creators/bulk-approve', [\App\Modules\Registration\Http\Controllers\Admin\CreatorApprovalController::class, 'bulkApprove'])->name('creators.bulk-approve');
    Route::get('/creators/export', [\App\Modules\Registration\Http\Controllers\Admin\CreatorApprovalController::class, 'export'])->name('creators.export');
});
