<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatwootAccount extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name', 'domain', 'support_email', 'custom_attributes'
    ];
    protected $casts = [
        'custom_attributes' => 'array',  // This will auto-serialize and unserialize JSON to/from array
    ];

    public function contacts()
    {
        return $this->hasMany(ChatwootContact::class);
    }
}
