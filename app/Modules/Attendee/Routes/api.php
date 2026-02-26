<?php

use Illuminate\Support\Facades\Route;
use Modules\Attendee\Http\Controllers\Api;

Route::prefix('attendee')->name('api.attendee.')->group(function () {
    
    // Public routes
    Route::get('events/{eventId}/availability', [Api\BookingApiController::class, 'checkAvailability'])
        ->name('events.availability');
    
    Route::post('checkin/scan', [Api\CheckInApiController::class, 'scan'])
        ->name('checkin.scan');
    
    Route::post('checkin/process', [Api\CheckInApiController::class, 'process'])
        ->name('checkin.process');
    
    Route::get('checkin/verify/{ticketNumber}', [Api\CheckInApiController::class, 'verify'])
        ->name('checkin.verify');
    
    Route::get('tickets/verify/{ticketNumber}', [Api\TicketApiController::class, 'verify'])
        ->name('tickets.verify');
    
    // Protected routes (require authentication)
    Route::middleware('auth:sanctum')->group(function () {
        
        // Bookings
        Route::get('bookings', [Api\BookingApiController::class, 'index'])
            ->name('bookings.index');
        
        Route::post('bookings', [Api\BookingApiController::class, 'store'])
            ->name('bookings.store');
        
        Route::get('bookings/{booking}', [Api\BookingApiController::class, 'show'])
            ->name('bookings.show');
        
        Route::post('bookings/{booking}/cancel', [Api\BookingApiController::class, 'cancel'])
            ->name('bookings.cancel');
        
        // Tickets
        Route::get('tickets', [Api\TicketApiController::class, 'index'])
            ->name('tickets.index');
        
        Route::get('tickets/{ticketNumber}', [Api\TicketApiController::class, 'show'])
            ->name('tickets.show');
        
        Route::get('tickets/{ticketNumber}/qr', [Api\TicketApiController::class, 'qrCode'])
            ->name('tickets.qr');
        
        Route::get('tickets/{ticketNumber}/download', [Api\TicketApiController::class, 'download'])
            ->name('tickets.download');
    });
});