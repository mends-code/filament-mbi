<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatwootService
{
    private $endpoint;

    private $platformAppApiKey;

    private $userApiKey;

    private $defaultPrivate = true;

    public function __construct()
    {
        $this->endpoint = config('chatwoot.endpoint');
        $this->platformAppApiKey = config('chatwoot.platform_app_api_key');
        $this->userApiKey = config('chatwoot.user_api_key');
    }

    /**
     * Create a new message in a conversation.
     *
     * @return array
     */
    public function createMessage(int $accountId, int $conversationId, string $content, ?bool $isPrivate = null)
    {
        $isPrivate = $isPrivate ?? $this->defaultPrivate;

        Log::info('Creating message', [
            'account_id' => $accountId,
            'conversation_id' => $conversationId,
            'content' => $content,
            'is_private' => $isPrivate,
        ]);

        $response = Http::withHeaders([
            'api_access_token' => $this->userApiKey,
        ])
            ->post("{$this->endpoint}/api/v1/accounts/{$accountId}/conversations/{$conversationId}/messages", [
                'content' => $content,
                'private' => $isPrivate,
            ]);

        $responseData = $response->json();

        Log::info('Message creation response', [
            'response' => $responseData,
        ]);

        return $responseData;
    }

    /**
     * Send multiple messages in a conversation.
     *
     * @return array
     */
    public function sendMessages(int $accountId, int $conversationId, array $messages)
    {
        $results = [];

        foreach ($messages as $message) {
            Log::info('Sending message', [
                'account_id' => $accountId,
                'conversation_id' => $conversationId,
                'message' => $message,
            ]);

            $results[] = $this->createMessage($accountId, $conversationId, $message, false);
        }

        Log::info('Messages sent', [
            'results' => $results,
        ]);

        return $results;
    }
}
