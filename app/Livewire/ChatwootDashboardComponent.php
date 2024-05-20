<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;

class ChatwootDashboardComponent extends Component
{
    protected $listeners = ['triggerChatwootDashboardFetchInfo'];

    public function fetchInfo()
    {
        $payload = null;

        $this->dispatch('chatwoot-dashboard-fetch-info', $payload);
    }

    #[On('chatwoot-dashboard-fetch-info')]
    public function receiveInfo($payload)
    {
        // Handle received payload
        // For example, log it or process it further
        logger()->info('Payload received:', $payload);
    }

    public function render()
    {
        return view('livewire.chatwoot-dashboard-component');
    }
}
