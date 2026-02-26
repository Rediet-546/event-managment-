<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Registration\Http\Controllers\ApiAuthController;
use App\Modules\Registration\Http\Controllers\ApiProfileController;
use App\Modules\Registration\Http\Controllers\VerificationController;

/*
|--------------------------------------------------------------------------
| Registration Module API Routes
|--------------------------------------------------------------------------
| All routes are prefixed with 'api/v1' and return JSON responses
| Rate limiting applied for security
*/

Route::prefix('api/v1')->name('api.')->group(function () {

    // ==================== PUBLIC API ROUTES ====================
    Route::middleware('throttle:10,1')->group(function () {

        // Authentication
        Route::post('/login', [ApiAuthController::class, 'login'])
            ->name('login');

        Route::post('/register', [ApiAuthController::class, 'register'])
            ->name('register');

        // Password Reset - FIXED: These methods must exist
        Route::post('/forgot-password', [ApiAuthController::class, 'sendResetLink'])
            ->name('password.forgot');

        Route::post('/reset-password', [ApiAuthController::class, 'resetPassword'])
            ->name('password.reset');

        // Email Verification
        Route::get('/verify-email/{id}/{hash}', [VerificationController::class, 'apiVerify'])
            ->name('verification.verify')
            ->middleware('signed');

        Route::post('/resend-verification', [VerificationController::class, 'apiResend'])
            ->name('verification.resend');
    });

    // ==================== PROTECTED API ROUTES (Sanctum) ====================
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {

        // Auth
        Route::post('/logout', [ApiAuthController::class, 'logout'])
            ->name('logout');

        Route::post('/refresh-token', [ApiAuthController::class, 'refreshToken'])
            ->name('refresh');

        // User Profile
        Route::get('/user', [ApiAuthController::class, 'getUser'])
            ->name('user');

        Route::get('/profile', [ApiProfileController::class, 'show'])
            ->name('profile.show');

        Route::put('/profile', [ApiProfileController::class, 'update'])
            ->name('profile.update');

        Route::post('/profile/avatar', [ApiProfileController::class, 'uploadAvatar'])
            ->name('profile.avatar');

        Route::post('/change-password', [ApiAuthController::class, 'changePassword'])
            ->name('password.change');

        Route::delete('/account', [ApiAuthController::class, 'deleteAccount'])
            ->name('account.delete');

        // Dashboard Data
        Route::get('/dashboard/stats', [ApiAuthController::class, 'getDashboardStats'])
            ->name('dashboard.stats');

        // ==================== CREATOR ROUTES ====================
        Route::prefix('creator')->name('creator.')->group(function () {
            // Apply creator middleware only to these routes
            Route::middleware(['creator.approved'])->group(function () {
                Route::get('/stats', [ApiAuthController::class, 'getCreatorStats'])
                    ->name('stats');

                Route::get('/events', [ApiAuthController::class, 'getCreatorEvents'])
                    ->name('events');

                Route::get('/analytics', [ApiAuthController::class, 'getCreatorAnalytics'])
                    ->name('analytics');
            });
        });
    });
});
