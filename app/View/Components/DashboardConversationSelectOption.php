<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\ChatwootConversation;

class DashboardConversationSelectOption extends Component
{
    public ChatwootConversation $conversation;

    public function __construct(ChatwootConversation $conversation)
    {
        $this->conversation = $conversation;
    }

    public function render()
    {
        return view('components.dashboard-conversation-select-option');
    }
}
