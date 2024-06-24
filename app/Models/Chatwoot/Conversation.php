<?php

namespace App\Models\Chatwoot;

use Carbon\Carbon;

use App\Models\Stripe\Invoice;

class Conversation extends BaseModel
{
    protected $table = 'mbi_chatwoot.conversations';

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
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function inbox()
    {
        return $this->belongsTo(Inbox::class, 'inbox_id');
    }

    public function stripeInvoices()
    {
        return $this->hasMany(Invoice::class, 'chatwoot_conversation_id', 'id');
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
