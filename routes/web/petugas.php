<?php

use App\Http\Controllers\Web\Admin\AdminPreviewController;
use App\Http\Controllers\Web\Admin\LocationPricingRuleController;
use App\Http\Controllers\Web\Admin\ProfileController;
use Illuminate\Support\Facades\Route;

Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'role:Petugas'])->group(function () {
    Route::redirect('/', '/petugas/dashboard')->name('home');
    Route::get('/dashboard', [AdminPreviewController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings', [AdminPreviewController::class, 'bookingsList'])->name('bookings.list');
    Route::get('/bookings/requests', [AdminPreviewController::class, 'bookingRequests'])->name('bookings.requests');
    Route::get('/bookings/active', [AdminPreviewController::class, 'bookingsActive'])->name('bookings.active');
    Route::get('/bookings/{booking}', [AdminPreviewController::class, 'bookingDetail'])->name('bookings.detail');
    Route::get('/calendar', [AdminPreviewController::class, 'calendar'])->name('calendar');
    Route::get('/calendar/events', [AdminPreviewController::class, 'calendarEvents'])->name('calendar.events');
    Route::get('/payments/dp', [AdminPreviewController::class, 'dpVerification'])->name('payments.dp');
    Route::get('/payments/final', [AdminPreviewController::class, 'finalPayment'])->name('payments.final');
    Route::get('/pricing/reviews', [AdminPreviewController::class, 'pricingReviews'])->name('pricing.reviews');
    Route::get('/reschedules', [AdminPreviewController::class, 'reschedules'])->name('reschedules');
    Route::get('/cancellations', [AdminPreviewController::class, 'cancellations'])->name('cancellations');
    Route::get('/force-majeure', [AdminPreviewController::class, 'forceMajeure'])->name('force.majeure');
    Route::get('/customers', [AdminPreviewController::class, 'customers'])->name('customers');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
});

Route::prefix('petugas')->name('petugas.')->middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/packages', [AdminPreviewController::class, 'packages'])->name('packages');
    Route::get('/location-rules', [LocationPricingRuleController::class, 'index'])->name('location.rules');
    Route::get('/settings', [AdminPreviewController::class, 'settings'])->name('settings');
});
