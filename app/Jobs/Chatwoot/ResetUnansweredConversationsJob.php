<?php

namespace App\Jobs\Chatwoot;

use App\Models\Chatwoot\Conversation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Config;

class ResetUnansweredConversationsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        $timeoutMinutes = Config::get('services.chatwoot.reset_assignee_timeout');

        $conversations = Conversation::open()
            ->unanswered($timeoutMinutes)
            ->get();

        foreach ($conversations as $conversation) {
            $conversation->resetAssignee();
            Log::info('Assignee ID set to null for conversation', ['conversation_id' => $conversation->id]);
        }
    }
}
