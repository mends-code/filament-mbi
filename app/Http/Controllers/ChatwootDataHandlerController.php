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

        // Store data in session
        session(['chatwootData' => $eventData]);

        return response()->json(['status' => 'Data processed successfully']);
    }

    public function displayData()
    {
        $data = session('chatwootData', []);
        return response()->json(['status' => 'success', 'data' => $data]);
    }
}
