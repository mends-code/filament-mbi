<?php
// Add these to routes/web.php
use App\Http\Controllers\ChatwootDataHandlerController;

Route::post('/filament/api/chatwoot-data-handler', [ChatwootDataHandlerController::class, 'handle']);
Route::get('/display-chatwoot-data', [ChatwootDataHandlerController::class, 'displayData']);
