<?php

namespace App\Livewire;

use Livewire\Component;

class ChatwootListener extends Component
{
    public $chatwootData;

    protected $listeners = ['chatwootEventReceived' => 'handleChatwootEvent'];

    public function handleChatwootEvent($eventData)
    {
        $this->chatwootData = $eventData;
    }

    public function render()
    {
        return view('livewire.chatwoot-listener');
    }
}
