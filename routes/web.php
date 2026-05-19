<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

use App\Http\Controllers\Admin\BlankController;

Route::get('/admin/blank', [BlankController::class, 'index'])->name('admin.blank');
