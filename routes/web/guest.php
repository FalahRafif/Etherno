<?php

use App\Http\Controllers\Public\BookingSupportController;
use App\Http\Controllers\Public\LandingPageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('home');
Route::get('/booking', [LandingPageController::class, 'booking'])->name('booking.page');
Route::get('/booking/success', [BookingSupportController::class, 'success'])->name('booking.success');
Route::get('/booking/status', [BookingSupportController::class, 'status'])->name('booking.status');
Route::get('/booking/payment/dp', [BookingSupportController::class, 'dpPayment'])->name('booking.payment.dp');
Route::get('/booking/payment/final', [BookingSupportController::class, 'finalPayment'])->name('booking.payment.final');
Route::get('/booking/reschedule', [BookingSupportController::class, 'reschedule'])->name('booking.reschedule');
Route::get('/booking/cancellation-policy', [BookingSupportController::class, 'cancellationPolicy'])->name('booking.cancellation.policy');
