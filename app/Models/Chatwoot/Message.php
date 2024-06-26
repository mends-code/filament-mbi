<?php

namespace App\Models\Chatwoot;

use App\Traits\HasTimestampScopes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

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
