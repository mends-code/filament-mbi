<?php

namespace App\Models;

use Carbon\Carbon;

/**
 * 
 *
 * @property int $id
 * @property int $account_id
 * @property int $inbox_id
 * @property int $status
 * @property int|null $assignee_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
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
 * @property-read \App\Models\ChatwootAccount|null $account
 * @property-read \App\Models\ChatwootContact|null $contact
 * @property-read \App\Models\ChatwootInbox|null $inbox
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereAdditionalAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereAgentLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereAssigneeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereAssigneeLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereCachedLabelList($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereCampaignId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereContactId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereContactInboxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereContactLastSeenAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereCustomAttributes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereDisplayId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereFirstReplyCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereIdentifier($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereInboxId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereLastActivityAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation wherePriority($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereSlaPolicyId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereSnoozedUntil($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereTeamId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootConversation whereWaitingSince($value)
 * @mixin \Eloquent
 */
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
