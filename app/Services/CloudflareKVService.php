<?php

namespace App\Services;

use App\Models\ShortenedLink;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CloudflareKVService
{
    protected $accountId;

    protected $namespaceId;

    protected $apiToken;

    public function __construct()
    {
        $this->accountId = config('services.shortener.cloudflare.account_id');
        $this->namespaceId = config('services.shortener.cloudflare.namespace_id');
        $this->apiToken = config('services.shortener.cloudflare.api_token');
    }

    public function createShortenedLink($targetUrl, $chatwootContactId = null, $chatwootAgentId = null, $chatwootConversationId = null, $chatwootAccountId = null)
    {
        // Encode the target URL in Base64
        $encodedTargetUrl = base64_encode($targetUrl);

        // Metadata for the shortened link
        $metadata = [
            'chatwoot_contact_id' => $chatwootContactId,
            'chatwoot_conversation_id' => $chatwootConversationId,
            'chatwoot_account_id' => $chatwootAccountId,
            'chatwoot_agent_id' => $chatwootAgentId,
        ];

        // Log metadata to ensure it's being set
        Log::info('Creating ShortenedLink with metadata: ', $metadata);

        // Step 1: Create entry in the model with encoded target URL and metadata
        $shortenedLink = ShortenedLink::create([
            'base64_target_url' => $encodedTargetUrl,
            'metadata' => $metadata, // Store metadata as an array
        ]);

        // Metadata for the KV store
        $kvMetadata = [
            'created_at' => $shortenedLink->created_at->toDateTimeString(),
            'chatwoot_contact_id' => $chatwootContactId,
            'chatwoot_conversation_id' => $chatwootConversationId,
            'chatwoot_account_id' => $chatwootAccountId,
            'chatwoot_agent_id' => $chatwootAgentId,
        ];

        // Step 2: Put all data to KV API
        $response = Http::withToken($this->apiToken)->asMultipart()->put(
            "https://api.cloudflare.com/client/v4/accounts/{$this->accountId}/storage/kv/namespaces/{$this->namespaceId}/values/{$shortenedLink->id}",
            [
                [
                    'name' => 'key',
                    'contents' => $shortenedLink->id,
                ],
                [
                    'name' => 'value',
                    'contents' => $encodedTargetUrl,
                ],
                [
                    'name' => 'metadata',
                    'contents' => json_encode($kvMetadata),
                ],
            ]
        );

        if ($response->successful()) {
            // Step 3: Update entry in the model with KV timestamps
            $shortenedLink->finalized_at = now();
            $shortenedLink->save();

            Log::info('Shortened link created and stored in Cloudflare KV', [
                'id' => $shortenedLink->id,
                'encoded_target_url' => $encodedTargetUrl,
                'finalized_at' => $shortenedLink->finalized_at,
            ]);

            return $shortenedLink;
        } else {
            Log::error('Failed to store shortened link in Cloudflare KV', [
                'id' => $shortenedLink->id,
                'encoded_target_url' => $encodedTargetUrl,
                'response' => $response->body(),
            ]);

            return null;
        }
    }
}
