<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripePrice extends BaseModelStripe
{
    protected $table = 'mbi_stripe.prices';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
    ];

    protected $fillable = [
        'id', 'data', 'product_id'
    ];

    public function product()
    {
        return $this->belongsTo(StripeProduct::class, 'product_id', 'id');
    }
}
