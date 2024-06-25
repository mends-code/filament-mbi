<?php

namespace App\Models\Chatwoot;

use App\Models\Stripe\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

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
        'status' => 'integer',
        'assignee_id' => 'integer',
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

    private function convertToTimezone($value, $timezone = 'Europe/Warsaw')
    {
        return $value ? Carbon::parse($value, 'UTC')->timezone($timezone) : null;
    }

    public function getLastActivityAtAttribute($value)
    {
        return $this->convertToTimezone($value);
    }

    public function getWaitingSinceAttribute($value)
    {
        return $this->convertToTimezone($value);
    }

    public function getContactLastSeenAtAttribute($value)
    {
        return $this->convertToTimezone($value);
    }

    public function getAgentLastSeenAtAttribute($value)
    {
        return $this->convertToTimezone($value);
    }

    public function getAssigneeLastSeenAtAttribute($value)
    {
        return $this->convertToTimezone($value);
    }

    public function getFirstReplyCreatedAtAttribute($value)
    {
        return $this->convertToTimezone($value);
    }

    public function scopeUnassigned(Builder $query): Builder
    {
        return $query->whereNull('assignee_id');
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->where('status', 0);
    }

    public function scopeClosed(Builder $query): Builder
    {
        return $query->where('status', 1);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 2);
    }

    public function scopeSnoozed(Builder $query): Builder
    {
        return $query->where('status', 3);
    }

    public function scopeUnanswered(Builder $query, int $timeoutMinutes): Builder
    {
        $threshold = Carbon::now()->subMinutes($timeoutMinutes);
        return $query->whereNotNull('waiting_since')
            ->where('waiting_since', '<=', $threshold)
            ->orderBy('waiting_since', 'asc');
    }

    public function resetAssignee()
    {
        $this->assignee_id = null;
        $this->save();
    }
}
