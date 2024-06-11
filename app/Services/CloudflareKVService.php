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

    public function createShortenedLink($targetUrl)
    {
        // Step 1: Create entry in the model with ID and target URL
        $shortenedLink = ShortenedLink::create(['target_url' => $targetUrl]);

        // Encode the target URL in Base64
        $encodedUrl = base64_encode($targetUrl);

        // Calculate expiration time
        $expirationTtl = (int) config('services.shortener.expiration_ttl');
        $expiration = now()->addSeconds($expirationTtl);
        $expirationTimestamp = now()->addSeconds($expirationTtl)->timestamp;

        // Metadata for the KV store
        $metadata = [
            'created_at' => $shortenedLink->created_at->toDateTimeString(),
        ];

        // Step 2: Put all data to KV API
        $response = Http::withToken($this->apiToken)->asMultipart()->put(
            "https://api.cloudflare.com/client/v4/accounts/{$this->accountId}/storage/kv/namespaces/{$this->namespaceId}/values/{$shortenedLink->id}",
            [
                [
                    [
                        'name' => 'key',
                        'contents' => $shortenedLink->id,
                    ],
                    [
                        'name' => 'value',
                        'contents' => $targetUrl,
                    ],
                    [
                        'name' => 'metadata',
                        'contents' => json_encode($metadata),
                    ],
                    [
                        'name' => 'expiration',
                        'contents' => $expirationTimestamp,
                    ],
                    [
                        'name' => 'base64',
                        'contents' => true,
                    ],
                ],
            ]
        );

        if ($response->successful()) {
            // Step 3: Update entry in the model with KV timestamps
            $shortenedLink->finalized_at = now();
            $shortenedLink->kv_expires_at = $expiration;
            $shortenedLink->save();

            Log::info('Shortened link created and stored in Cloudflare KV', [
                'id' => $shortenedLink->id,
                'target_url' => $targetUrl,
                'finalized_at' => $shortenedLink->finalized_at,
                'kv_expires_at' => $shortenedLink->kv_expires_at,
            ]);

            return $shortenedLink;
        } else {
            Log::error('Failed to store shortened link in Cloudflare KV', [
                'id' => $shortenedLink->id,
                'target_url' => $targetUrl,
                'response' => $response->body(),
            ]);

            return null;
        }
    }
}
