<?php

namespace App\Models\Filament;

use App\Models\Chatwoot\User as ChatwootUser;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'chatwoot_user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        $allowedDomains = explode(',', env('FILAMENT_EMAIL_DOMAINS', '')); // Get domains from .env, default to an empty string
        $userDomain = substr($this->email, strpos($this->email, '@') + 1); // Extract the domain from the email

        return in_array($userDomain, $allowedDomains); // Check if the user's domain is in the list of allowed domains
    }

    /**
     * Get the associated Chatwoot user.
     */
    public function chatwootUser()
    {
        return $this->belongsTo(ChatwootUser::class, 'chatwoot_user_id');
    }
}
