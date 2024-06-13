<?php

namespace App\Models;

class StripeEvent extends BaseModelStripe
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
