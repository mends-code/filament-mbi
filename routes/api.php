<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatwootDataController;

Route::post('/store-chatwoot-data', [ChatwootDataController::class, 'store'])->name('store-chatwoot-data');