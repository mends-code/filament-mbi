<?php

namespace App\Models\Chatwoot;

use App\Models\Stripe\Customer;
use App\Models\Stripe\Invoice;
use Carbon\Carbon;

class Contact extends BaseModel
{
    protected $table = 'mbi_chatwoot.contacts';

    protected $casts = [
        'name' => 'string',
        'additional_attributes' => 'json',
        'custom_attributes' => 'json',
        'last_activity_at' => 'timestamp',
        'blocked' => 'boolean',
        'identifier' => 'string',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function stripeCustomers()
    {
        return $this->hasMany(Customer::class, 'chatwoot_contact_id');
    }

    public function stripeInvoices()
    {
        return $this->hasManyThrough(
            Invoice::class,
            Customer::class,
            'chatwoot_contact_id', // Foreign key on Customer table
            'customer_id', // Foreign key on Invoice table
            'id', // Local key on ChatwootContact table
            'id' // Local key on Customer table
        );
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'contact_id');
    }

    public function getLastActivityAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimezone('Europe/Warsaw') : null;
    }

    public function getCreatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value)->setTimezone('Europe/Warsaw') : null;
    }
}
