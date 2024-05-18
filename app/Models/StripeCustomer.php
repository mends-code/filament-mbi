<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StripeCustomer extends BaseModelStripe
{
    protected $table = 'mbi_stripe.customers';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'chatwoot_contact_id' => 'integer'
    ];

    protected $fillable = [
        'id', 'data', 'chatwoot_contact_id'
    ];

    public function chatwootContact()
    {
        return $this->belongsTo(ChatwootContact::class, 'chatwoot_contact_id');
    }

    public function invoices()
    {
        return $this->hasMany(StripeInvoice::class, 'customer_id', 'id');
    }
}
