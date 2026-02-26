<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Events\Http\Controllers\ApiEventController;

Route::group(['prefix' => 'v1/events', 'as' => 'api.events.', 'middleware' => ['auth:sanctum']], function () {
    // Public endpoints (with or without auth)
    Route::get('/', [ApiEventController::class, 'index'])->name('index')->withoutMiddleware(['auth:sanctum']);
    Route::get('/upcoming', [ApiEventController::class, 'upcoming'])->name('upcoming')->withoutMiddleware(['auth:sanctum']);
    Route::get('/featured', [ApiEventController::class, 'featured'])->name('featured')->withoutMiddleware(['auth:sanctum']);
    Route::get('/{event:slug}', [ApiEventController::class, 'show'])->name('show')->withoutMiddleware(['auth:sanctum']);
    Route::post('/{event:slug}/check-availability', [ApiEventController::class, 'checkAvailability'])->name('check-availability');
    
    // Protected endpoints
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::post('/', [ApiEventController::class, 'store'])->name('store');
        Route::put('/{event:slug}', [ApiEventController::class, 'update'])->name('update');
        Route::delete('/{event:slug}', [ApiEventController::class, 'destroy'])->name('destroy');
    });
});