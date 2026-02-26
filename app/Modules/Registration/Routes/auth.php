<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Registration\Http\Controllers\AuthController;
use App\Modules\Registration\Http\Controllers\VerificationController;

/*
|--------------------------------------------------------------------------
| Authentication Specific Routes
|--------------------------------------------------------------------------
| These routes handle authentication-specific flows
| Separated for better organization and maintenance
*/

// Socialite Routes (if using social login)
Route::prefix('auth')->name('auth.')->group(function () {
    
    // Social Login Routes (commented - enable when needed)
    // Route::get('/{provider}', [AuthController::class, 'redirectToProvider'])
    //     ->name('social.redirect');
    // Route::get('/{provider}/callback', [AuthController::class, 'handleProviderCallback'])
    //     ->name('social.callback');
});

// Email Verification Specific Routes
Route::prefix('email')->name('verification.')->middleware(['auth'])->group(function () {
    Route::get('/verify/notice', [VerificationController::class, 'showNotice'])
        ->name('notice')
        ->withoutMiddleware(['auth']); // Allow guests to see notice
    
    Route::get('/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->name('verify')
        ->middleware(['signed']);
    
    Route::post('/verification-notification', [VerificationController::class, 'resend'])
        ->name('send')
        ->middleware(['throttle:2,1']);
});

// Password Confirmation Routes
Route::prefix('confirm')->name('password.')->middleware(['auth'])->group(function () {
    Route::get('/password', [AuthController::class, 'showConfirmForm'])
        ->name('confirm');
    Route::post('/password', [AuthController::class, 'confirm'])
        ->name('confirm.submit');
});

// Two Factor Authentication Routes (if implementing)
Route::prefix('two-factor')->name('two-factor.')->middleware(['auth'])->group(function () {
    Route::get('/', [AuthController::class, 'showTwoFactorForm'])
        ->name('form');
    Route::post('/', [AuthController::class, 'verifyTwoFactor'])
        ->name('verify');
    Route::post('/enable', [AuthController::class, 'enableTwoFactor'])
        ->name('enable');
    Route::post('/disable', [AuthController::class, 'disableTwoFactor'])
        ->name('disable');
});

// Account Recovery Routes
Route::prefix('recovery')->name('recovery.')->middleware(['guest'])->group(function () {
    Route::get('/account', [AuthController::class, 'showRecoveryForm'])
        ->name('form');
    Route::post('/account', [AuthController::class, 'recoverAccount'])
        ->name('submit')
        ->middleware('throttle:3,1');
});

// Session Management
Route::middleware(['auth'])->group(function () {
    Route::delete('/sessions/{session}', [AuthController::class, 'destroySession'])
        ->name('sessions.destroy');
    Route::delete('/sessions', [AuthController::class, 'destroyAllSessions'])
        ->name('sessions.destroy-all');
});