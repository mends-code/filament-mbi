<?php

namespace App\Models\Stripe;

class Event extends BaseModel
{
    protected $table = 'mbi_stripe.events';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'created' => 'integer',
        'object' => 'string',
        'object_id' => 'string',
        'livemode' => 'boolean',
    ];
}
