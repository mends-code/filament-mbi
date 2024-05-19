<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatwootContextController;

// Define a resource route for Chatwoot context
Route::apiResource('chatwoot-context', ChatwootContextController::class);
