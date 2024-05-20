<?php

namespace App\Livewire;

use Livewire\Component;

class ChatwootListener extends Component
{
    public $chatwootData;

    protected $listeners = ['message' => 'handleChatwootEvent'];

    public function handleChatwootEvent()
    {
        $this->chatwootData = $this->dispatch('message');
    }

    public function render()
    {
        return view('livewire.chatwoot-listener');
    }
}
