<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeInvoice extends BaseModelStripe
{
    protected $table = 'mbi_stripe.invoices';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'created' => 'timestamp',
    ];

    protected $fillable = [
        'id', 'data', 'customer_id'
    ];

    public function customer()
    {
        return $this->belongsTo(StripeCustomer::class, 'customer_id', 'id');
    }

    public function chatwootContact()
    {
        return $this->hasOneThrough(
            ChatwootContact::class,
            StripeCustomer::class,
            'id', // Foreign key on StripeCustomer table
            'id', // Foreign key on ChatwootContact table
            'customer_id', // Local key on StripeInvoice table
            'chatwoot_contact_id' // Local key on StripeCustomer table
        );
    }

}
