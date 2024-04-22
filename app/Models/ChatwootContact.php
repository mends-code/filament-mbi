<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatwootContact extends BaseModel
{
    use HasFactory;

    protected $table = 'chatwoot_contacts';  // Explicitly defining the table name

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
}
