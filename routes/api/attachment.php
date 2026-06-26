<?php

use App\Http\Controllers\Api\Internal\AttachmentController;
use Illuminate\Support\Facades\Route;

Route::prefix('internal')
    ->name('api.internal.')
    ->middleware(['web', 'auth', 'signed'])
    ->group(function (): void {
        Route::get('/attachments/{attachmentUuid}/preview', [AttachmentController::class, 'show'])
            ->name('attachments.preview');
    });

Route::prefix('public')
    ->name('api.public.')
    ->middleware(['web', 'signed'])
    ->group(function (): void {
        Route::get('/attachments/{attachmentUuid}/payment-receipt', [AttachmentController::class, 'showPaymentReceipt'])
            ->name('attachments.payment-receipt');
        Route::get('/attachments/{attachmentUuid}/package-thumbnail', [AttachmentController::class, 'showPackageThumbnail'])
            ->name('attachments.package-thumbnail');
    });
