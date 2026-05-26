<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlankController;
use App\Http\Controllers\Admin\AdminPreviewController;
use App\Http\Controllers\Public\BookingSupportController;
use App\Http\Controllers\Public\LandingPageController;

Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/booking', [LandingPageController::class, 'booking'])->name('booking.page');
Route::get('/booking/success', [BookingSupportController::class, 'success'])->name('booking.success');
Route::get('/booking/status', [BookingSupportController::class, 'status'])->name('booking.status');
Route::get('/booking/payment/dp', [BookingSupportController::class, 'dpPayment'])->name('booking.payment.dp');
Route::get('/booking/payment/final', [BookingSupportController::class, 'finalPayment'])->name('booking.payment.final');
Route::get('/booking/reschedule', [BookingSupportController::class, 'reschedule'])->name('booking.reschedule');
Route::get('/booking/cancellation-policy', [BookingSupportController::class, 'cancellationPolicy'])->name('booking.cancellation.policy');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/dashboard')->name('home');
    Route::get('/dashboard', [AdminPreviewController::class, 'dashboard'])->name('dashboard');
    Route::get('/bookings/requests', [AdminPreviewController::class, 'bookingRequests'])->name('bookings.requests');
    Route::get('/bookings/active', [AdminPreviewController::class, 'bookingsActive'])->name('bookings.active');
    Route::get('/bookings/{booking}', [AdminPreviewController::class, 'bookingDetail'])->name('bookings.detail');
    Route::get('/calendar', [AdminPreviewController::class, 'calendar'])->name('calendar');
    Route::get('/payments/dp', [AdminPreviewController::class, 'dpVerification'])->name('payments.dp');
    Route::get('/payments/final', [AdminPreviewController::class, 'finalPayment'])->name('payments.final');
    Route::get('/pricing/reviews', [AdminPreviewController::class, 'pricingReviews'])->name('pricing.reviews');
    Route::get('/packages', [AdminPreviewController::class, 'packages'])->name('packages');
    Route::get('/location-rules', [AdminPreviewController::class, 'locationRules'])->name('location.rules');
    Route::get('/reschedules', [AdminPreviewController::class, 'reschedules'])->name('reschedules');
    Route::get('/cancellations', [AdminPreviewController::class, 'cancellations'])->name('cancellations');
    Route::get('/force-majeure', [AdminPreviewController::class, 'forceMajeure'])->name('force.majeure');
    Route::get('/customers', [AdminPreviewController::class, 'customers'])->name('customers');
    Route::get('/settings', [AdminPreviewController::class, 'settings'])->name('settings');
    Route::get('/blank', [BlankController::class, 'index'])->name('blank');
});
