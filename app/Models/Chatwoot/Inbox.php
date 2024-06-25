<?php

namespace App\Models\Chatwoot;

use Illuminate\Database\Eloquent\Relations\HasMany;

class Inbox extends BaseModel
{
    protected $table = 'mbi_chatwoot.inboxes';

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'inbox_id');
    }
}
