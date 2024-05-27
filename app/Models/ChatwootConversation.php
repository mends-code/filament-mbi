<?php

namespace App\Models;

use Carbon\Carbon;

class ChatwootConversation extends BaseModelChatwoot
{
    protected $table = 'mbi_chatwoot.conversations';

    protected $fillable = [];

    protected $casts = [
        'last_activity_at' => 'timestamp',
        'waiting_since' => 'timestamp',
        'contact_last_seen_at' => 'timestamp',
        'agent_last_seen_at' => 'timestamp',
        'assignee_last_seen_at' => 'timestamp',
        'first_reply_created_at' => 'timestamp',
    ];

    public function account()
    {
        return $this->belongsTo(ChatwootAccount::class, 'account_id');
    }

    public function contact()
    {
        return $this->belongsTo(ChatwootContact::class, 'contact_id');
    }

    public function inbox()
    {
        return $this->belongsTo(ChatwootInbox::class, 'inbox_id');
    }

    public function getLastActivityAtAttribute($value)
    {
        return $value ? Carbon::parse($value, 'UTC')->timezone('Europe/Warsaw') : null;
    }

    public function getWaitingSinceAttribute($value)
    {
        return $value ? Carbon::parse($value, 'UTC')->timezone('Europe/Warsaw') : null;
    }

    public function getContactLastSeenAtAttribute($value)
    {
        return $value ? Carbon::parse($value, 'UTC')->timezone('Europe/Warsaw') : null;
    }

    public function getAgentLastSeenAtAttribute($value)
    {
        return $value ? Carbon::parse($value, 'UTC')->timezone('Europe/Warsaw') : null;
    }

    public function getAssigneeLastSeenAtAttribute($value)
    {
        return $value ? Carbon::parse($value, 'UTC')->timezone('Europe/Warsaw') : null;
    }

    public function getFirstReplyCreatedAtAttribute($value)
    {
        return $value ? Carbon::parse($value, 'UTC')->timezone('Europe/Warsaw') : null;
    }
}
