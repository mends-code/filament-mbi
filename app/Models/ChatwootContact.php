<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatwootContact extends BaseModel
{
    use HasFactory;

    protected $table = 'chatwoot_contacts';

    protected $fillable = [
        'name', 'email', 'phone_number', 'additional_attributes',
        'identifier', 'custom_attributes', 'last_activity_at',
        'contact_type', 'middle_name', 'last_name', 'location', 
        'country_code', 'blocked'
    ];

    protected $casts = [
        'additional_attributes' => 'array',
        'custom_attributes' => 'array',
        'last_activity_at' => 'datetime',
        'blocked' => 'boolean',
    ];

    /**
     * Get the account that owns the contact.
     */
    public function account()
    {
        return $this->belongsTo(ChatwootAccount::class);
    }

    /**
     * Get the full name of the contact.
     */
    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Ensure emails are stored lowercase.
     */
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }
}
