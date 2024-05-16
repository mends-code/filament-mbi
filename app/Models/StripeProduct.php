<?php

namespace App\Models;

class StripeProduct extends BaseModelStripe
{
    protected $table = 'mbi_stripe.products';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
    ];

    public function prices()
    {
        return $this->hasMany(StripePrice::class, 'product_id', 'id');
    }
}
