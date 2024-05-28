<?php

return [
    'secret' => env('STRIPE_SECRET'),
    'customer' => [
        'default' => [
            'email' => env('STRIPE_CUSTOMER_DEFAULT_EMAIL', 'change-me@example.local'),
        ],
    ],
];
