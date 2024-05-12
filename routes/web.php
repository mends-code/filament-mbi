<?php
use Illuminate\Support\Facades\Route;

Route::post('/filament/api/chatwoot-data-handler', [App\Http\Controllers\ChatwootDataHandlerController::class, 'handle']);