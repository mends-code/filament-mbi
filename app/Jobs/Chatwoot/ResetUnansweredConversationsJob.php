<?php

namespace App\Jobs\Chatwoot;

use App\Models\Chatwoot\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class ResetUnansweredConversationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $timeoutMinutes;

    public function __construct()
    {
        $this->timeoutMinutes = config('services.chatwoot.reset_assignee_timeout');
    }

    public function handle()
    {
        if ($this->timeoutMinutes <= 0) {
            return;
        }

        $threshold = Carbon::now('Europe/Warsaw')->subMinutes($this->timeoutMinutes)->setTimezone('UTC');

        $conversations = Conversation::open()->assigned()->unanswered()->get()->filter(function ($conversation) use ($threshold) {
            return $conversation->waiting_since->lessThanOrEqualTo($threshold);
        });

        foreach ($conversations as $conversation) {
            UnassignConversationJob::dispatch($conversation);
        }
    }
}
