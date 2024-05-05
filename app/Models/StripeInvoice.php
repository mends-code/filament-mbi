<?php

namespace App\Models;

class StripeInvoice extends BaseModelStripe
{
    protected $table = 'mbi_stripe.objects'; // Use the same table as StripeObject

    protected static function booted()
    {
        static::addGlobalScope('object_type', function ($builder) {
            $builder->where('object_type', 'invoice');
        });
    }
    
    protected $appends = ['customer_id'];

    public function getCustomerIdAttribute()
    {
        return $this->data['customer'] ?? null;
    }
    
    public function customer()
    {
        return $this->belongsTo(StripeCustomer::class, 'customer_id', 'stripe_id');
    }
}
