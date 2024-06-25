<?php

namespace App\Models\Chatwoot;

use App\Models\Stripe\Invoice;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * 
 *
 * @property int $id
 * @property int $account_id
 * @property int $inbox_id
 * @property int $status
 * @property int|null $assignee_id
 * @property string $created_at
 * @property string $updated_at
 * @property int|null $contact_id
 * @property int $display_id
 * @property int|null $contact_last_seen_at
 * @property int|null $agent_last_seen_at
 * @property string|null $additional_attributes
 * @property int|null $contact_inbox_id
 * @property string $uuid
 * @property string|null $identifier
 * @property int $last_activity_at
 * @property int|null $team_id
 * @property int|null $campaign_id
 * @property string|null $snoozed_until
 * @property string|null $custom_attributes
 * @property int|null $assignee_last_seen_at
 * @property int|null $first_reply_created_at
 * @property int|null $priority
 * @property int|null $sla_policy_id
 * @property int|null $waiting_since
 * @property string|null $cached_label_list
 * @property-read \App\Models\Chatwoot\Account|null $account
 * @property-read \App\Models\Chatwoot\Contact|null $contact
 * @property-read \App\Models\Chatwoot\Inbox|null $inbox
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Invoice> $stripeInvoices
 * @property-read int|null $stripe_invoices_count
 * @method static Builder|Conversation closed()
 * @method static Builder|Conversation newModelQuery()
 * @method static Builder|Conversation newQuery()
 * @method static Builder|Conversation open()
 * @method static Builder|Conversation pending()
 * @method static Builder|Conversation query()
 * @method static Builder|Conversation snoozed()
 * @method static Builder|Conversation unanswered(int $timeoutMinutes)
 * @method static Builder|Conversation unassigned()
 * @method static Builder|Conversation whereAccountId($value)
 * @method static Builder|Conversation whereAdditionalAttributes($value)
 * @method static Builder|Conversation whereAgentLastSeenAt($value)
 * @method static Builder|Conversation whereAssigneeId($value)
 * @method static Builder|Conversation whereAssigneeLastSeenAt($value)
 * @method static Builder|Conversation whereCachedLabelList($value)
 * @method static Builder|Conversation whereCampaignId($value)
 * @method static Builder|Conversation whereContactId($value)
 * @method static Builder|Conversation whereContactInboxId($value)
 * @method static Builder|Conversation whereContactLastSeenAt($value)
 * @method static Builder|Conversation whereCreatedAt($value)
 * @method static Builder|Conversation whereCustomAttributes($value)
 * @method static Builder|Conversation whereDisplayId($value)
 * @method static Builder|Conversation whereFirstReplyCreatedAt($value)
 * @method static Builder|Conversation whereId($value)
 * @method static Builder|Conversation whereIdentifier($value)
 * @method static Builder|Conversation whereInboxId($value)
 * @method static Builder|Conversation whereLastActivityAt($value)
 * @method static Builder|Conversation wherePriority($value)
 * @method static Builder|Conversation whereSlaPolicyId($value)
 * @method static Builder|Conversation whereSnoozedUntil($value)
 * @method static Builder|Conversation whereStatus($value)
 * @method static Builder|Conversation whereTeamId($value)
 * @method static Builder|Conversation whereUpdatedAt($value)
 * @method static Builder|Conversation whereUuid($value)
 * @method static Builder|Conversation whereWaitingSince($value)
 * @mixin \Eloquent
 */
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
