<?php

namespace App\Models\Chatwoot;

use App\Models\Stripe\Invoice;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
