<?php

namespace App\Jobs;

use App\Models\LinkEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
            $linkEntry = LinkEntry::create([
                'data' => $item,
            ]);

            // Log completion for each entry
            Log::info('LinkEntry processed successfully', ['entry' => $linkEntry]);
        }
    }
}
