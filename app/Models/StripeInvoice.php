<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;

/**
 * @property array $data
 * @property int $created
 * @property string|null $customer_id
 * @property string $id
 * @property string|null $currency
 * @property string|null $status
 * @property bool $paid
 * @property int $total
 * @property bool $livemode
 * @property-read \App\Models\ChatwootContact|null $chatwootContact
 * @property-read \App\Models\StripeCustomer|null $customer
 *
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice forContact($contactId)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice latestForContact($contactId)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice query()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereCustomerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeInvoice whereId($value)
 *
 * @mixin \Eloquent
 */
class StripeInvoice extends BaseModelStripe
{
    protected $table = 'mbi_stripe.invoices';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'created' => 'timestamp',
        'currency' => 'string',
        'status' => 'string',
        'paid' => 'boolean',
        'total' => 'integer',
        'livemode' => 'boolean',
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

    public function scopeForContact($query, $contactId)
    {
        return $query->whereHas('chatwootContact', function ($query) use ($contactId) {
            $query->where('chatwoot_contact_id', $contactId);
        });
    }

    public function scopeLatestForContact($query, $contactId)
    {
        return $query->forContact($contactId)->orderBy('created', 'desc');
    }

    public function scopePaid($query)
    {
        return $query->where('paid', true);
    }

    public function scopeUnpaid($query)
    {
        return $query->where('paid', false);
    }
}
