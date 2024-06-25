<?php

namespace App\Models\Chatwoot;

use App\Models\Stripe\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property string $provider
 * @property string $uid
 * @property string $encrypted_password
 * @property string|null $reset_password_token
 * @property \Illuminate\Support\Carbon|null $reset_password_sent_at
 * @property \Illuminate\Support\Carbon|null $remember_created_at
 * @property int $sign_in_count
 * @property \Illuminate\Support\Carbon|null $current_sign_in_at
 * @property \Illuminate\Support\Carbon|null $last_sign_in_at
 * @property string|null $current_sign_in_ip
 * @property string|null $last_sign_in_ip
 * @property string|null $confirmation_token
 * @property \Illuminate\Support\Carbon|null $confirmed_at
 * @property \Illuminate\Support\Carbon|null $confirmation_sent_at
 * @property string|null $unconfirmed_email
 * @property string $name
 * @property string|null $display_name
 * @property string|null $email
 * @property array|null $tokens
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property string|null $pubsub_token
 * @property int|null $availability
 * @property array|null $ui_settings
 * @property array|null $custom_attributes
 * @property string|null $type
 * @property string|null $message_signature
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Invoice> $stripeInvoices
 * @property-read int|null $stripe_invoices_count
 * @method static Builder|User byEmail(string $email)
 * @method static Builder|User confirmed()
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User notSuperAdmin()
 * @method static Builder|User query()
 * @method static Builder|User superAdmin()
 * @method static Builder|User whereAvailability($value)
 * @method static Builder|User whereConfirmationSentAt($value)
 * @method static Builder|User whereConfirmationToken($value)
 * @method static Builder|User whereConfirmedAt($value)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereCurrentSignInAt($value)
 * @method static Builder|User whereCurrentSignInIp($value)
 * @method static Builder|User whereCustomAttributes($value)
 * @method static Builder|User whereDisplayName($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereEncryptedPassword($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastSignInAt($value)
 * @method static Builder|User whereLastSignInIp($value)
 * @method static Builder|User whereMessageSignature($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User whereProvider($value)
 * @method static Builder|User wherePubsubToken($value)
 * @method static Builder|User whereRememberCreatedAt($value)
 * @method static Builder|User whereResetPasswordSentAt($value)
 * @method static Builder|User whereResetPasswordToken($value)
 * @method static Builder|User whereSignInCount($value)
 * @method static Builder|User whereTokens($value)
 * @method static Builder|User whereType($value)
 * @method static Builder|User whereUiSettings($value)
 * @method static Builder|User whereUid($value)
 * @method static Builder|User whereUnconfirmedEmail($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends BaseModel
{
    protected $table = 'mbi_chatwoot.users';

    protected $casts = [
        'reset_password_sent_at' => 'datetime',
        'remember_created_at' => 'datetime',
        'current_sign_in_at' => 'datetime',
        'last_sign_in_at' => 'datetime',
        'confirmed_at' => 'datetime',
        'confirmation_sent_at' => 'datetime',
        'tokens' => 'array',
        'ui_settings' => 'json',
        'custom_attributes' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'type' => 'string',
    ];

    // Scopes
    public function scopeByEmail(Builder $query, string $email): Builder
    {
        return $query->where('email', $email);
    }

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->whereNotNull('confirmed_at');
    }

    public function scopeSuperAdmin(Builder $query): Builder
    {
        return $query->where('type', 'SuperAdmin');
    }

    public function scopeNotSuperAdmin(Builder $query): Builder
    {
        return $query->whereNot('type', 'SuperAdmin');
    }

    public function stripeInvoices(): HasMany
    {
        return $this->hasMany(Invoice::class, 'chatwoot_agent_id', 'id');
    }

}
