<?php

namespace App\Models;

/**
 * 
 *
 * @property array $data
 * @property int $created
 * @property int|null $chatwoot_contact_id
 * @property string $id
 * @property-read \App\Models\ChatwootContact|null $chatwootContact
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\StripeInvoice> $invoices
 * @property-read int|null $invoices_count
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer latestForContact($chatwootContactId)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer query()
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer whereChatwootContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripeCustomer whereId($value)
 * @mixin \Eloquent
 */
class StripeCustomer extends BaseModelStripe
{
    protected $table = 'mbi_stripe.customers';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'chatwoot_contact_id' => 'integer',
    ];

    protected $fillable = [
        'id',
        'data',
        'chatwoot_contact_id',
    ];

    public function chatwootContact()
    {
        return $this->belongsTo(ChatwootContact::class, 'chatwoot_contact_id');
    }

    public function invoices()
    {
        return $this
            ->hasMany(StripeInvoice::class, 'customer_id', 'id');

    }

    public function scopeLatestForContact($query, $chatwootContactId)
    {
        return $query
            ->where('chatwoot_contact_id', $chatwootContactId)
            ->orderBy('created', 'desc');
    }
}
