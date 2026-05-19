<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\BlankController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/blank', [BlankController::class, 'index'])->name('blank');
});
