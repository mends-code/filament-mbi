<?php

// app/Jobs/DeleteDiscardedInvoicesJob.php

namespace App\Jobs;

use App\Models\Stripe\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class DeleteDiscardedInvoicesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $discardedInvoices = Invoice::discarded()->get();

        $count = $discardedInvoices->count();
        if ($count > 0) {
            Invoice::discarded()->delete();
            Log::info("Deleted {$count} discarded invoices.");
        } else {
            Log::info('No discarded invoices found.');
        }
    }
}
