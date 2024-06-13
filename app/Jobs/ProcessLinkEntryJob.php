<?php

namespace App\Jobs;

use App\Models\LinkEntry;
use App\Models\ShortenedLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Exception;

class ProcessLinkEntryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payload;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $payload)
    {
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Iterate over each item in the payload and create a LinkEntry
        foreach ($this->payload as $item) {
            // Extract shortened link ID from the data
            $shortenedLinkId = $this->extractShortenedLinkId($item);

            // Check if the ShortenedLink with this ID exists
            if (!ShortenedLink::where('id', $shortenedLinkId)->exists()) {
                throw new Exception("ShortenedLink with ID {$shortenedLinkId} does not exist.");
            }

            // Create the LinkEntry with the shortened_link_id
            $linkEntry = LinkEntry::create([
                'data' => $item,
                'shortened_link_id' => $shortenedLinkId,
            ]);

            // Log completion for each entry
            Log::info('LinkEntry processed successfully', ['entry' => $linkEntry]);
        }
    }

    /**
     * Extract the shortened link ID from the given data.
     *
     * @param array $data
     * @return string|null
     */
    protected function extractShortenedLinkId(array $data)
    {
        // Assume the shortened link ID is derived from the URL in the 'event'->'request'->'url' field
        if (isset($data['event']['request']['url'])) {
            return Str::afterLast(parse_url($data['event']['request']['url'], PHP_URL_PATH), '/');
        }

        return null;
    }
}
