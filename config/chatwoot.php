<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Chatwoot Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for Chatwoot integration.
    | These settings can be controlled through the environment variables
    | prefixed with CHATWOOT_.
    |
    */

    'endpoint' => env('CHATWOOT_ENDPOINT', 'https://app.chatwoot.com'),
    'platform_app_api_key' => env('CHATWOOT_PLATFORM_APP_API_KEY', ''),
    'user_api_key' => env('CHATWOOT_USER_API_KEY', ''),
];
