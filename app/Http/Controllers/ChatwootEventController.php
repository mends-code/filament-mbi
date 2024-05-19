<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Events\ChatwootEvent;

class ChatwootEventController extends Controller
{
    public function handle(Request $request)
    {
        $data = $request->all();
        // Broadcast the event to WebSocket clients
        event(new ChatwootEvent($data));
        return response()->json(['status' => 'success']);
    }
}
