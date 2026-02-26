<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendee\Http\Controllers\Admin;

Route::prefix('attendee')->name('admin.attendee.')->group(function () {
    
    Route::get('dashboard', [Admin\AttendeeDashboardController::class, 'index'])
        ->name('dashboard');
    
    Route::resource('bookings', Admin\BookingController::class)
        ->names('bookings');
    
    Route::get('bookings/export', [Admin\BookingController::class, 'export'])
        ->name('bookings.export');
    
    Route::resource('ticket-types', Admin\TicketTypeController::class)
        ->names('ticket-types');
    
    Route::resource('discounts', Admin\DiscountCodeController::class)
        ->names('discounts');
    
    Route::get('checkins/scan', [Admin\CheckInController::class, 'scan'])
        ->name('checkins.scan');
    
    Route::post('checkins/verify', [Admin\CheckInController::class, 'verify'])
        ->name('checkins.verify');
    
    Route::post('checkins/process', [Admin\CheckInController::class, 'process'])
        ->name('checkins.process');
    
    Route::get('checkins', [Admin\CheckInController::class, 'index'])
        ->name('checkins.index');
    
    Route::get('attendees', [Admin\AttendeeController::class, 'index'])
        ->name('attendees.index');
    
    Route::get('attendees/{user}', [Admin\AttendeeController::class, 'show'])
        ->name('attendees.show');
    
    Route::get('settings', [Admin\AttendeeSettingController::class, 'index'])
        ->name('settings');
    
    Route::post('settings', [Admin\AttendeeSettingController::class, 'update'])
        ->name('settings.update');
});