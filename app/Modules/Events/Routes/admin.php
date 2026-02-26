<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Events\Http\Controllers\Admin\VendorController;
use App\Modules\Events\Http\Controllers\Admin\BookingManagementController;

Route::group(['prefix' => 'admin/events', 'as' => 'admin.events.', 'middleware' => ['auth', 'role:admin|super-admin']], function () {
    
    // MAIN ADMIN EVENTS ROUTES
    Route::get('/', function () {
        $events = \App\Modules\Events\Models\Event::orderBy('start_date', 'desc')->paginate(15);
        return view('events::admin.events.index', compact('events'));
    })->name('index');
    
    Route::get('/create', function () {
        $categories = \App\Modules\Events\Models\EventCategory::active()->ordered()->get();
        return view('events::create', compact('categories'));
    })->name('create');
    
    // FIXED: Now using admin edit view
    Route::get('/{event}/edit', function (\App\Modules\Events\Models\Event $event) {
        $categories = \App\Modules\Events\Models\EventCategory::active()->ordered()->get();
        return view('events::admin.events.edit', compact('event', 'categories'));
    })->name('edit');
    
    // ADDED: Update route
    Route::put('/{event}', function (\App\Modules\Events\Models\Event $event) {
        $data = request()->all();
        $event->update($data);
        return redirect()->route('admin.events.index')->with('success', 'Event updated successfully!');
    })->name('update');
    
    // Publish route
    Route::post('/{event}/publish', function (\App\Modules\Events\Models\Event $event) {
        $event->update(['status' => 'published']);
        return redirect()->route('admin.events.index')->with('success', 'Event published successfully!');
    })->name('publish');
    
    // Delete route
    Route::delete('/{event}', function (\App\Modules\Events\Models\Event $event) {
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event deleted successfully!');
    })->name('destroy');
    
    // Vendor Management
    Route::group(['prefix' => 'vendors', 'as' => 'vendors.'], function () {
        Route::get('/', [VendorController::class, 'index'])->name('index');
        Route::get('/{vendor}', [VendorController::class, 'show'])->name('show');
        Route::post('/{vendor}/approve', [VendorController::class, 'approve'])->name('approve');
        Route::post('/{vendor}/suspend', [VendorController::class, 'suspend'])->name('suspend');
        Route::post('/{vendor}/reactivate', [VendorController::class, 'reactivate'])->name('reactivate');
        Route::get('/{vendor}/events', [VendorController::class, 'events'])->name('events');
        Route::get('/{vendor}/earnings', [VendorController::class, 'earnings'])->name('earnings');
    });

    // Booking Management
    Route::group(['prefix' => 'bookings', 'as' => 'bookings.'], function () {
        Route::get('/', [BookingManagementController::class, 'index'])->name('index');
        Route::get('/analytics', [BookingManagementController::class, 'analytics'])->name('analytics');
        Route::get('/export', [BookingManagementController::class, 'export'])->name('export');
        Route::get('/{booking}', [BookingManagementController::class, 'show'])->name('show');
        Route::put('/{booking}/status', [BookingManagementController::class, 'updateStatus'])->name('update-status');
        Route::post('/check-in/{booking}', [BookingManagementController::class, 'checkIn'])->name('check-in');
        Route::post('/bulk-check-in', [BookingManagementController::class, 'bulkCheckIn'])->name('bulk-check-in');
    });
});