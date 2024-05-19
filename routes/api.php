<?php

// routes/api.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatwootController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/chatwoot/context', [ChatwootController::class, 'store']);
});
