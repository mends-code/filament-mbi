<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatwootEventController;

Route::post('/chatwoot-event', [ChatwootEventController::class, 'handle']);
