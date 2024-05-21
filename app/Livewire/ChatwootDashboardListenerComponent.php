<?php

namespace App\Livewire;

use Livewire\Component;

class ChatwootDashboardListenerComponent extends Component
{
    protected $listeners = ['update-chatwoot-context', 'get-chatwoot-context'];

    public function render()
    {
        return view('livewire.chatwoot-dashboard-listener-component');
    }
}
