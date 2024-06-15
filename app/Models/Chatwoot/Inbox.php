<?php

namespace App\Models\Chatwoot;

class Inbox extends BaseModel
{
    protected $table = 'mbi_chatwoot.inboxes';

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'inbox_id');
    }
}
