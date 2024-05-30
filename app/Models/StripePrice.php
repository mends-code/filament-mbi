<?php

namespace App\Models;

/**
 * @property array $data
 * @property int $created
 * @property string|null $product_id
 * @property int|null $active_since
 * @property string $id
 * @property-read \App\Models\StripeProduct|null $product
 *
 * @method static \Illuminate\Database\Eloquent\Builder|StripePrice active()
 * @method static \Illuminate\Database\Eloquent\Builder|StripePrice newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripePrice newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StripePrice query()
 * @method static \Illuminate\Database\Eloquent\Builder|StripePrice whereActiveSince($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripePrice whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripePrice whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripePrice whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StripePrice whereProductId($value)
 *
 * @mixin \Eloquent
 */
class StripePrice extends BaseModelStripe
{
    protected $table = 'mbi_stripe.prices';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'active_since' => 'timestamp',
    ];

    protected $fillable = [
        'id', 'data', 'product_id',
    ];

    public function product()
    {
        return $this->belongsTo(StripeProduct::class, 'product_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->whereNotNull('active_since');
    }
}
