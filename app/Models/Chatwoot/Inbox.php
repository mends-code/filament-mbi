<?php

namespace App\Models\Chatwoot;

use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * 
 *
 * @property int $id
 * @property int $channel_id
 * @property int $account_id
 * @property string $name
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $channel_type
 * @property bool|null $enable_auto_assignment
 * @property bool|null $greeting_enabled
 * @property string|null $greeting_message
 * @property string|null $email_address
 * @property bool|null $working_hours_enabled
 * @property string|null $out_of_office_message
 * @property string|null $timezone
 * @property bool|null $enable_email_collect
 * @property bool|null $csat_survey_enabled
 * @property bool|null $allow_messages_after_resolved
 * @property string|null $auto_assignment_config
 * @property bool $lock_to_single_conversation
 * @property int|null $portal_id
 * @property int $sender_name_type
 * @property string|null $business_name
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Chatwoot\Conversation> $conversations
 * @property-read int|null $conversations_count
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox query()
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereAllowMessagesAfterResolved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereAutoAssignmentConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereChannelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereCsatSurveyEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereEmailAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereEnableAutoAssignment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereEnableEmailCollect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereGreetingEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereGreetingMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereLockToSingleConversation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereOutOfOfficeMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox wherePortalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereSenderNameType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Inbox whereWorkingHoursEnabled($value)
 * @mixin \Eloquent
 */
class Inbox extends BaseModel
{
    protected $table = 'mbi_chatwoot.inboxes';

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class, 'inbox_id');
    }
}
