<?php

namespace App\Models;

class ChatwootInbox extends BaseModelChatwoot
{
    protected $table = 'mbi_chatwoot.inboxes';


    public function conversations()
    {
        return $this->hasMany(ChatwootConversation::class, 'inbox_id');
    }

}
