<?php

namespace App\Models;

class StripeCustomer extends BaseModelStripe
{
    protected $table = 'mbi_stripe.objects'; // Use the same table as StripeObject

    protected static function booted()
    {
        static::addGlobalScope('object_type', function ($builder) {
            $builder->where('object_type', 'customer');
        });
    }

    public function contact()
    {
        return $this->belongsToMany(ChatwootContact::class, 'mbi_filament.chatwoot_contact_stripe_customer', 'stripe_customer_id', 'chatwoot_contact_id');
    }

}
