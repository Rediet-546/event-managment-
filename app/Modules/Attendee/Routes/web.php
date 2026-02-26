<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendee\Http\Controllers\Front\BookingController;
use Modules\Attendee\Http\Controllers\Front\TicketController;
use Modules\Attendee\Http\Controllers\Front\AccountController;
use Modules\Attendee\Http\Controllers\Admin\AttendeeDashboardController;
use Modules\Attendee\Http\Controllers\Admin\BookingController as AdminBookingController;
use Modules\Attendee\Http\Controllers\Admin\TicketTypeController;
use Modules\Attendee\Http\Controllers\Admin\DiscountCodeController;
use Modules\Attendee\Http\Controllers\Admin\CheckInController;
use Modules\Attendee\Http\Controllers\Admin\AttendeeController;
use Modules\Attendee\Http\Controllers\Admin\EmailTemplateController;
use Modules\Attendee\Http\Controllers\Admin\AttendeeSettingController;

// ==================== FRONTEND ROUTES ====================
Route::prefix('attendee')->name('attendee.front.')->group(function () {
    
    // Test route
    Route::get('/test', function() {
        return view('attendee::test');
    })->name('test');
    
    // Booking routes
    Route::get('/events/{event}/book', [BookingController::class, 'create'])
        ->name('bookings.create');
    Route::post('/events/{event}/book', [BookingController::class, 'store'])
        ->name('bookings.store');
    Route::get('/bookings/{booking}/success', [BookingController::class, 'success'])
        ->name('bookings.success');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])
        ->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])
        ->name('bookings.cancel');
    
    // Ticket routes
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/{ticketNumber}', [TicketController::class, 'show'])
            ->name('show');
        Route::get('/{ticketNumber}/download', [TicketController::class, 'download'])
            ->name('download');
        Route::get('/{ticketNumber}/qr', [TicketController::class, 'qrCode'])
            ->name('qr');
    });
    
    // Account routes (require authentication)
    Route::prefix('account')->name('account.')->middleware('auth')->group(function () {
        Route::get('/dashboard', [AccountController::class, 'dashboard'])
            ->name('dashboard');
        Route::get('/bookings', [AccountController::class, 'bookings'])
            ->name('bookings');
        Route::get('/profile', [AccountController::class, 'profile'])
            ->name('profile');
        Route::post('/profile', [AccountController::class, 'updateProfile'])
            ->name('profile.update');
    });
});

// ==================== ADMIN ROUTES ====================
Route::prefix('admin/attendee')->name('admin.attendee.')->middleware(['web', 'auth'])->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AttendeeDashboardController::class, 'index'])
        ->name('dashboard');
    
    // Bookings
    Route::resource('bookings', AdminBookingController::class)
        ->names('bookings');
    Route::get('/bookings/export', [AdminBookingController::class, 'export'])
        ->name('bookings.export');
    
    // Ticket Types
    Route::resource('ticket-types', TicketTypeController::class)
        ->names('ticket-types');
    
    // Discount Codes
    Route::resource('discounts', DiscountCodeController::class)
        ->names('discounts');
    
    // Check-ins
    Route::get('/checkins/scan', [CheckInController::class, 'scan'])
        ->name('checkins.scan');
    Route::post('/checkins/verify', [CheckInController::class, 'verify'])
        ->name('checkins.verify');
    Route::post('/checkins/process', [CheckInController::class, 'process'])
        ->name('checkins.process');
    Route::get('/checkins', [CheckInController::class, 'index'])
        ->name('checkins.index');
    
    // Attendees
    Route::get('/attendees', [AttendeeController::class, 'index'])
        ->name('attendees.index');
    Route::get('/attendees/{user}', [AttendeeController::class, 'show'])
        ->name('attendees.show');
    
    // Email Templates
    Route::resource('email-templates', EmailTemplateController::class)
        ->names('email-templates');
    
    // Settings
    Route::get('/settings', [AttendeeSettingController::class, 'index'])
        ->name('settings');
    Route::post('/settings', [AttendeeSettingController::class, 'update'])
        ->name('settings.update');
});
