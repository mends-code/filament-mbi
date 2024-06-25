<?php

namespace App\Models\Chatwoot;

/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property int|null $locale
 * @property string|null $domain
 * @property string|null $support_email
 * @property int $feature_flags
 * @property int|null $auto_resolve_duration
 * @property string|null $limits
 * @property string|null $custom_attributes
 * @property int|null $status
 * @method static \Illuminate\Database\Eloquent\Builder|Account newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Account query()
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereAutoResolveDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereCustomAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereFeatureFlags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereLimits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereSupportEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Account whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Account extends BaseModel
{
    protected $table = 'mbi_chatwoot.accounts';

    protected $casts = [];
}
