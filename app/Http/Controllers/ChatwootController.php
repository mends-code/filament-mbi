<?php

// app/Http/Controllers/ChatwootController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ChatwootController extends Controller
{
    /**
     * Store Chatwoot context in session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'conversation' => 'required|array',
            'contact' => 'required|array',
            'currentAgent' => 'required|array',
        ]);

        // Store validated context data in session
        Session::put('chatwoot.conversation', $validated['conversation']);
        Session::put('chatwoot.contact', $validated['contact']);
        Session::put('chatwoot.currentAgent', $validated['currentAgent']);

        return response()->json(['message' => 'Context data received and stored in session successfully'], 200);
    }
}

