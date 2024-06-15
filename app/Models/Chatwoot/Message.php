<?php

namespace App\Models\Chatwoot;

class Message extends BaseModel
{
    protected $table = 'mbi_chatwoot.messages';

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function inbox()
    {
        return $this->belongsTo(Inbox::class, 'inbox_id');
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    // Scopes
    public function scopeSenderUser($query)
    {
        return $query->where('sender_type', 'User');
    }

    public function scopeSenderContact($query)
    {
        return $query->where('sender_type', 'Contact');
    }

    public function scopeByAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeByInbox($query, $inboxId)
    {
        return $query->where('inbox_id', $inboxId);
    }

    public function scopeByConversation($query, $conversationId)
    {
        return $query->where('conversation_id', $conversationId);
    }

    public function scopeByMessageType($query, $messageType)
    {
        return $query->where('message_type', $messageType);
    }

    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeLatestFirst($query)
    {
        return $query->orderBy('created_at', 'desc');
    }
}
