<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Events\Http\Controllers\EventController;
use App\Modules\Events\Http\Controllers\CategoryController;
use App\Modules\Attendee\Http\Controllers\BookingController; // ← FIXED: Use Attendee module

/*
|--------------------------------------------------------------------------
| Events Module Web Routes
|--------------------------------------------------------------------------
*/

Route::prefix('events')->name('events.')->group(function () {
    // ========== PUBLIC ROUTES ==========
    Route::get('/', [EventController::class, 'index'])->name('index');
    Route::get('/calendar', [EventController::class, 'calendar'])->name('calendar');

    // Categories (static routes must come before the dynamic slug route)
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');

    // ========== AUTHENTICATED ROUTES ==========
    Route::middleware('auth')->group(function () {
        // Event CRUD (create is a reserved path, keep before slug route)
        Route::get('/create', [EventController::class, 'create'])
            ->name('create')
            ->middleware('can:create-events'); // Add permission check

        Route::post('/', [EventController::class, 'store'])
            ->name('store')
            ->middleware('can:create-events');

        // My bookings (from Attendee module)
        Route::get('/my-bookings', [BookingController::class, 'myBookings'])
            ->name('my-bookings');

        // Organizer tools - Only for event creators
        Route::middleware('can:manage-events')->group(function () {
            Route::get('/{event:slug}/bookings/manage', [BookingController::class, 'manageBookings'])
                ->name('manage-bookings');

            Route::post('/{event:slug}/bookings/{booking}/check-in', [BookingController::class, 'checkIn'])
                ->name('check-in');
        });

        // Event management (only for the event creator or admin)
        Route::get('/{event:slug}/edit', [EventController::class, 'edit'])
            ->name('edit')
            ->middleware('can:update,event');

        Route::put('/{event:slug}', [EventController::class, 'update'])
            ->name('update')
            ->middleware('can:update,event');

        Route::delete('/{event:slug}', [EventController::class, 'destroy'])
            ->name('destroy')
            ->middleware('can:delete,event');

        // Event actions
        Route::post('/{event:slug}/publish', [EventController::class, 'publish'])
            ->name('publish')
            ->middleware('can:update,event');

        Route::post('/{event:slug}/cancel', [EventController::class, 'cancel'])
            ->name('cancel')
            ->middleware('can:update,event');

        Route::post('/{event:slug}/duplicate', [EventController::class, 'duplicate'])
            ->name('duplicate')
            ->middleware('can:create-events');

        // Booking flows (from Attendee module)
        Route::get('/{event:slug}/book', [BookingController::class, 'create'])
            ->name('book');

        Route::post('/{event:slug}/book', [BookingController::class, 'store'])
            ->name('process-booking');

        Route::post('/{event:slug}/bookings/{booking}/cancel', [BookingController::class, 'cancelEventBooking'])
            ->name('cancel-booking')
            ->middleware('can:cancel,booking');
    });

    // Dynamic event slug registered after reserved paths
    Route::get('/{event:slug}', [EventController::class, 'show'])->name('show');
});

// ========== BOOKING ROUTES (Outside /events) ==========
Route::middleware('auth')->prefix('bookings')->name('events.booking.')->group(function () {
    Route::get('/{booking}/confirmation', [BookingController::class, 'confirmation'])
        ->name('confirmation');

    Route::post('/{booking}/cancel', [BookingController::class, 'cancel'])
        ->name('cancel')
        ->middleware('can:cancel,booking');
});

// ========== ADMIN ROUTES ==========
Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:view-admin-dashboard'])->group(function () {
    Route::get('/events', [EventController::class, 'adminIndex'])
        ->name('events.index');
});
