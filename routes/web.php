<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatwootDataController;

Route::post('/api/chatwoot/app-context/store', [ChatwootDataController::class, 'store']);
Route::get('/api/chatwoot/app-context/fetch', [ChatwootDataController::class, 'fetch']);
