<?php

namespace App\Jobs\Chatwoot;

use App\Models\Chatwoot\Conversation;
use App\Services\ChatwootService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UnassignConversationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $conversation;

    public function __construct(Conversation $conversation)
    {
        $this->conversation = $conversation;
    }

    public function handle(ChatwootService $chatwootService)
    {
        $chatwootService->assignConversation($this->conversation->account_id, $this->conversation->display_id, null);
    }
}
