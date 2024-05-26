<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeProduct extends BaseModelStripe
{
    protected $table = 'mbi_stripe.products';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
    ];

    protected $fillable = [
        'id', 'data'
    ];

}
