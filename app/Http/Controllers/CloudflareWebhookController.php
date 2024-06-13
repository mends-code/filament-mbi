<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessLinkEntryJob;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class CloudflareWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Log the incoming request for debugging purposes
        Log::info('Cloudflare Webhook Received:', $request->all());

        // Dispatch a job to handle the webhook
        ProcessLinkEntryJob::dispatch($request->all());

        return response()->json(['status' => 'success']);
    }
}
