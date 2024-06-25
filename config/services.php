<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),

    ],
    'stripe' => [
        'secret' => env('STRIPE_SECRET'),
        'customer' => [
            'default' => [
                'email' => env('STRIPE_CUSTOMER_DEFAULT_EMAIL', 'change-me@example.local'),
            ],
        ],
    ],
    'chatwoot' => [
        'endpoint' => env('CHATWOOT_ENDPOINT', 'https://app.chatwoot.com'),
        'reset_assignee_timeout' => env('CHATWOOT_RESET_ASSIGNEE_TIMEOUT', 30),
        'reset_assignee_enabled' => env('CHATWOOT_RESET_ASSIGNEE_ENABLED', false),
    ],

    'cloudflare' => [
        'account_id' => env('CLOUDFLARE_ACCOUNT_ID'),
        'namespace_id' => env('CLOUDFLARE_NAMESPACE_ID'),
        'api_token' => env('CLOUDFLARE_API_TOKEN'),
        'link_shortener' => [
            'domain' => env('CLOUDFLARE_LINK_SHORTENER_DOMAIN', 'link.mends.eu'),
            'id_length' => env('CLOUDFLARE_LINK_SHORTENER_ID_LENGTH', 8),
        ],
    ],

];
