<?php

namespace App\Models;

/**
 * @property int $id
 * @property int $channel_id
 * @property int $account_id
 * @property string $name
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChatwootConversation> $conversations
 * @property-read int|null $conversations_count
 *
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox query()
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereAllowMessagesAfterResolved($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereAutoAssignmentConfig($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereBusinessName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereChannelId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereChannelType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereCsatSurveyEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereEmailAddress($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereEnableAutoAssignment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereEnableEmailCollect($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereGreetingEnabled($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereGreetingMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereLockToSingleConversation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereOutOfOfficeMessage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox wherePortalId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereSenderNameType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereTimezone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ChatwootInbox whereWorkingHoursEnabled($value)
 *
 * @mixin \Eloquent
 */
class ChatwootInbox extends BaseModelChatwoot
{
    protected $table = 'mbi_chatwoot.inboxes';

    public function conversations()
    {
        return $this->hasMany(ChatwootConversation::class, 'inbox_id');
    }
}
