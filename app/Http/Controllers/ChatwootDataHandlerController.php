<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ChatwootDataHandlerController extends Controller
{
    public function handle(Request $request)
    {
        $eventData = $request->json()->all();
        Log::info('Received Chatwoot data:', $eventData);

        return response()->json(['status' => 'Data processed successfully']);
    }
}
