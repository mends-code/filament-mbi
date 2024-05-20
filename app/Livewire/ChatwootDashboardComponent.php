<?php

namespace App\Livewire;

use Livewire\Component;

class ChatwootDashboardComponent extends Component
{
    public $conversationData = [];

    protected $listeners = [
        'handleConversationData'
    ];

    public function handleConversationData($data)
    {
        $this->conversationData = $data;
    }

    public function render()
    {
        return view('livewire.chatwoot-dashboard-component');
    }
}
