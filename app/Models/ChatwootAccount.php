<?php

namespace App\Models;

/**
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property int|null $locale
 * @property string|null $domain
 * @property string|null $support_email
 * @property int $feature_flags
 * @property int|null $auto_resolve_duration
 * @property string|null $limits
 * @property string|null $custom_attributes
 * @property int|null $status
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount whereAutoResolveDuration($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount whereCustomAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount whereDomain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount whereFeatureFlags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount whereLimits($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount whereSupportEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootAccount whereUpdatedAt($value)
 *
 * @mixin \Eloquent
 */
class ChatwootAccount extends BaseModelChatwoot
{
    protected $table = 'mbi_chatwoot.accounts';

    protected $fillable = [];

    protected $casts = [];
}
