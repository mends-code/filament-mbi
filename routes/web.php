<?php
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redis;

Route::get('/redis-check', function () {
    // Set a value in Redis
    Redis::set('key', 'This is a test value');

    // Retrieve the value from Redis
    $value = Redis::get('key');

    return $value; // This should return 'This is a test value'
});
