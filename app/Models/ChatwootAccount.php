<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class ChatwootAccount extends BaseModelChatwoot
{
    protected $table = 'mbi_chatwoot.accounts';
    protected $fillable = [];
    protected $casts = [];
}
