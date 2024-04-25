<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatwootAccount extends Model
{
    use HasFactory;

    protected $table = 'mbi_chatwoot.accounts';

    protected $fillable = [
        'id', 'name', 'domain', 'support_email', 'custom_attributes'
    ];
    protected $casts = [
        'custom_attributes' => 'array',  // This will auto-serialize and unserialize JSON to/from array
    ];

}
