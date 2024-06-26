<?php

namespace App\Services;

use App\Models\Chatwoot\AccessToken;
use App\Models\Filament\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatwootService
{
    private $endpoint;

    private $defaultPrivate = true;

    private $fallbackUserId;

    public function __construct()
    {
        $this->endpoint = config('services.chatwoot.endpoint');
        $this->fallbackUserId = config('services.chatwoot.fallback.user_id');
        Log::info('Chatwoot API Endpoint', ['endpoint' => $this->endpoint]);
    }

    /**
     * Get the API key for a given user ID.
     */
    private function getUserApiKey(?int $userId): ?string
    {
        if (! $userId && $this->fallbackUserId) {
            return AccessToken::forUser($this->fallbackUserId)->value('token');
        }

        $user = User::find($userId);

        if ($user) {
            Log::info('User found', ['user_id' => $user->id, 'email' => $user->email, 'chatwoot_user_id' => $user->chatwoot_user_id]);

            if ($user->chatwoot_user_id) {
                return AccessToken::forUser($user->chatwoot_user_id)->value('token');
            } else {
                Log::error('Chatwoot user ID not set for user', ['user_id' => $user->id]);
            }
        } else {
            Log::error('No user found with ID', ['user_id' => $userId]);
        }

        return null;
    }

    /**
     * Create a new message in a conversation.
     */
    public function createMessage(int $accountId, int $conversationId, string $content, ?bool $isPrivate, ?int $userId = null): array
    {
        $isPrivate = $isPrivate ?? $this->defaultPrivate;
        $userApiKey = $this->getUserApiKey($userId);

        if (! $userApiKey) {
            Log::error('API key not found for the user', ['user_id' => $userId]);

            return ['error' => 'API key not found'];
        }

        Log::info('Creating message', [
            'account_id' => $accountId,
            'conversation_id' => $conversationId,
            'content' => $content,
            'is_private' => $isPrivate,
        ]);

        $response = Http::withHeaders([
            'api_access_token' => $userApiKey,
        ])->post("{$this->endpoint}/api/v1/accounts/{$accountId}/conversations/{$conversationId}/messages", [
            'content' => $content,
            'private' => $isPrivate,
        ]);

        $responseData = $response->json();

        Log::info('Message creation response', [
            'response' => $responseData,
        ]);

        return $responseData ?? ['error' => 'No response data'];
    }

    /**
     * Send multiple messages in a conversation.
     */
    public function sendMessages(int $accountId, int $conversationId, array $messages, ?int $userId = null): array
    {
        $results = [];

        foreach ($messages as $message) {
            Log::info('Sending message', [
                'account_id' => $accountId,
                'conversation_id' => $conversationId,
                'message' => $message,
            ]);

            $results[] = $this->createMessage($accountId, $conversationId, $message, false, $userId);
        }

        Log::info('Messages sent', [
            'results' => $results,
        ]);

        return $results;
    }

    /**
     * Assign a conversation to an agent.
     */
    public function assignConversation(int $accountId, int $conversationId, ?int $assigneeId, ?int $userId = null): array
    {
        $userApiKey = $this->getUserApiKey($userId);

        if (! $userApiKey) {
            Log::error('API key not found for the user', ['user_id' => $userId]);

            return ['error' => 'API key not found'];
        }

        Log::info('Assigning conversation', [
            'account_id' => $accountId,
            'conversation_id' => $conversationId,
            'assignee_id' => $assigneeId,
        ]);

        $response = Http::withHeaders([
            'api_access_token' => $userApiKey,
        ])->post("{$this->endpoint}/api/v1/accounts/{$accountId}/conversations/{$conversationId}/assignments", [
            'assignee_id' => $assigneeId,
        ]);

        $responseData = $response->json();

        Log::info('Conversation assignment response', [
            'response' => $responseData,
        ]);

        return $responseData ?? ['error' => 'No response data'];
    }
}
