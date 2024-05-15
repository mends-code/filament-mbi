<?php

namespace App\Models;

class StripePrice extends BaseModelStripe
{
    protected $table = 'mbi_stripe.objects';

    protected static function booted()
    {
        static::addGlobalScope('object_type', function ($builder) {
            $builder->where('object_type', 'price');
        });
    }

}
