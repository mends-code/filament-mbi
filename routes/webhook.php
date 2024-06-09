<?php
// routes/webhook.php

use App\Http\Controllers\StripeWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('webhook')->group(function () {
    Route::prefix('stripe')->group(function () {
        Route::post('events', [StripeWebhookController::class, 'handleWebhook']);
    });

    // You can add more webhook routes here for different services in the future
});
