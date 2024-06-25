<?php

namespace App\Models\Stripe;

/**
 * 
 *
 * @property array $data
 * @property int $created
 * @property string $object
 * @property string|null $object_id
 * @property bool|null $livemode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $id
 * @method static \Illuminate\Database\Eloquent\Builder|Event newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Event query()
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreated($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereData($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereLivemode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereObject($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereObjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Event whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Event extends BaseModel
{
    protected $table = 'mbi_stripe.events';

    protected $casts = [
        'id' => 'string',
        'data' => 'json',
        'created' => 'integer',
        'object' => 'string',
        'object_id' => 'string',
        'livemode' => 'boolean',
    ];
}
