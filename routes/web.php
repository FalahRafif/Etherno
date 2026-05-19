<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlankController;
use App\Http\Controllers\Public\LandingPageController;

Route::get('/', [LandingPageController::class, 'index'])->name('home');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/blank', [BlankController::class, 'index'])->name('blank');
});
