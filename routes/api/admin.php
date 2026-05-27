<?php

use App\Http\Controllers\Api\Admin\LocationPricingRuleController;
use App\Http\Controllers\Api\Admin\PackageController;
use App\Http\Controllers\Api\Admin\ProfileController;
use App\Http\Controllers\Api\Admin\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')
    ->middleware(['web', 'auth', 'role:Admin'])
    ->name('api.admin.')
    ->group(function (): void {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        Route::post('/packages', [PackageController::class, 'store'])->name('packages.store');
        Route::put('/packages/{package}', [PackageController::class, 'update'])->name('packages.update');
        Route::delete('/packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');

        Route::post('/location-rules', [LocationPricingRuleController::class, 'store'])->name('location-rules.store');
        Route::put('/location-rules/{locationPricingRule}', [LocationPricingRuleController::class, 'update'])->name('location-rules.update');
        Route::delete('/location-rules/{locationPricingRule}', [LocationPricingRuleController::class, 'destroy'])->name('location-rules.destroy');
        Route::get('/location-rules/options', [LocationPricingRuleController::class, 'locationOptions'])->name('location-rules.options');
    });

Route::prefix('admin')
    ->middleware(['web', 'auth'])
    ->name('api.admin.')
    ->group(function (): void {
        Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    });
