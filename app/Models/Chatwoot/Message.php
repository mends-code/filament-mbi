<?php

namespace App\Models\Chatwoot;

use App\Traits\HasTimestampScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * 
 *
 * @property int $id
 * @property string|null $content
 * @property int $account_id
 * @property int $inbox_id
 * @property int $conversation_id
 * @property int $message_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property bool $private
 * @property int|null $status
 * @property string|null $source_id
 * @property int $content_type
 * @property array|null $content_attributes
 * @property string|null $sender_type
 * @property int|null $sender_id
 * @property array|null $external_source_ids
 * @property array|null $additional_attributes
 * @property string|null $processed_message_content
 * @property array|null $sentiment
 * @property-read \App\Models\Chatwoot\Account|null $account
 * @property-read \App\Models\Chatwoot\Conversation|null $conversation
 * @property-read \App\Models\Chatwoot\Inbox|null $inbox
 * @method static Builder|Message byAccount(int $accountId)
 * @method static Builder|Message byConversation(int $conversationId)
 * @method static Builder|Message byInbox(int $inboxId)
 * @method static Builder|Message byMessageType(int $messageType)
 * @method static Builder|Message forYearAndMonth(int $year, int $month)
 * @method static Builder|Message latestFirst()
 * @method static Builder|Message newModelQuery()
 * @method static Builder|Message newQuery()
 * @method static Builder|Message private()
 * @method static Builder|Message public()
 * @method static Builder|Message query()
 * @method static Builder|Message senderAgentBot(int $senderId)
 * @method static Builder|Message senderContact(int $senderId)
 * @method static Builder|Message senderUser(int $senderId)
 * @method static Builder|Message senderUserOrContact(int $userId)
 * @method static Builder|Message whereAccountId($value)
 * @method static Builder|Message whereAdditionalAttributes($value)
 * @method static Builder|Message whereContent($value)
 * @method static Builder|Message whereContentAttributes($value)
 * @method static Builder|Message whereContentType($value)
 * @method static Builder|Message whereConversationId($value)
 * @method static Builder|Message whereCreatedAt($value)
 * @method static Builder|Message whereExternalSourceIds($value)
 * @method static Builder|Message whereId($value)
 * @method static Builder|Message whereInboxId($value)
 * @method static Builder|Message whereMessageType($value)
 * @method static Builder|Message wherePrivate($value)
 * @method static Builder|Message whereProcessedMessageContent($value)
 * @method static Builder|Message whereSenderId($value)
 * @method static Builder|Message whereSenderType($value)
 * @method static Builder|Message whereSentiment($value)
 * @method static Builder|Message whereSourceId($value)
 * @method static Builder|Message whereStatus($value)
 * @method static Builder|Message whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Message extends BaseModel
{
    use HasTimestampScopes;

    protected $table = 'mbi_chatwoot.messages';

    protected $casts = [
        'account_id' => 'integer',
        'inbox_id' => 'integer',
        'conversation_id' => 'integer',
        'message_type' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'private' => 'boolean',
        'status' => 'integer',
        'content_attributes' => 'json',
        'external_source_ids' => 'array',
        'additional_attributes' => 'json',
        'sentiment' => 'array',
    ];

    protected static function booted()
    {
        static::addGlobalScope('excludeDeleted', function (Builder $builder) {
            $builder->where(function ($query) {
                $query->whereNull('content_attributes->deleted')
                    ->orWhere('content_attributes->deleted', '!=', true);
            });
        });
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function inbox(): BelongsTo
    {
        return $this->belongsTo(Inbox::class, 'inbox_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    // Scopes
    public function scopeSenderUser(Builder $query, int $senderId): Builder
    {
        return $query->where('sender_type', 'User')->where('sender_id', $senderId);
    }

    public function scopeSenderContact(Builder $query, int $senderId): Builder
    {
        return $query->where('sender_type', 'Contact')->where('sender_id', $senderId);
    }

    public function scopeSenderUserOrContact(Builder $query, int $userId): Builder
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('sender_type', 'User')->where('sender_id', $userId)
                ->orWhere('sender_type', 'Contact');
        });
    }

    public function scopeSenderAgentBot(Builder $query, int $senderId): Builder
    {
        return $query->where('sender_type', 'AgentBot')->where('sender_id', $senderId);
    }

    public function scopeByAccount(Builder $query, int $accountId): Builder
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeByInbox(Builder $query, int $inboxId): Builder
    {
        return $query->where('inbox_id', $inboxId);
    }

    public function scopeByConversation(Builder $query, int $conversationId): Builder
    {
        return $query->where('conversation_id', $conversationId);
    }

    public function scopeByMessageType(Builder $query, int $messageType): Builder
    {
        return $query->where('message_type', $messageType);
    }

    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopePrivate(Builder $query): Builder
    {
        return $query->where('private', true);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('private', false);
    }
}
