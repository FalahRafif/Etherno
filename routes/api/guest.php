<?php

use App\Http\Controllers\Api\Public\BookingController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function (): void {
    Route::get('/booking/availability', [BookingController::class, 'availability'])->name('booking.availability');
    Route::get('/booking/location-options', [BookingController::class, 'locationOptions'])->name('booking.location.options');
    Route::get('/booking/estimate', [BookingController::class, 'estimate'])->name('booking.estimate');
    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
});
