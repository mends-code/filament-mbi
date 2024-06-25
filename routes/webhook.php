<?php

use App\Http\Controllers\CloudflareWebhookController;
use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhook')->group(function () {
    Route::prefix('stripe')->group(function () {
        Route::post('events', [StripeWebhookController::class, 'handleWebhook']);
    });

    Route::prefix('cloudflare')->group(function () {
        Route::post('link-entry', [CloudflareWebhookController::class, 'handleWebhook']);
    });
});
