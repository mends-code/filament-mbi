<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeInvoice extends BaseModelStripe
{
    protected $table = 'mbi_stripe.invoices';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
    ];

    protected $fillable = [
        'id', 'data', 'customer_id'
    ];

    public function customer()
    {
        return $this->belongsTo(StripeCustomer::class, 'customer_id', 'id');
    }
}
