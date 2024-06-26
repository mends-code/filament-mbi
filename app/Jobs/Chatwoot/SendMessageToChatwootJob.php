<?php

namespace App\Jobs\Chatwoot;

use App\Services\ChatwootService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMessageToChatwootJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected int $accountId;
    protected int $conversationId;
    protected array $messages;
    protected ?int $userId;

    public function __construct(int $accountId, int $conversationId, array $messages, ?int $userId = null)
    {
        $this->accountId = $accountId;
        $this->conversationId = $conversationId;
        $this->messages = $messages;
        $this->userId = $userId;
    }

    public function handle(ChatwootService $chatwootService): void
    {
        Log::info('Sending messages to Chatwoot', ['messages' => $this->messages]);

        $responses = $chatwootService->sendMessages($this->accountId, $this->conversationId, $this->messages, $this->userId);

        foreach ($responses as $response) {
            if (isset($response['error'])) {
                Log::error('Error sending message to Chatwoot', ['response' => $response]);
                return;
            }
        }

        Log::info('Messages sent successfully');
    }
}
