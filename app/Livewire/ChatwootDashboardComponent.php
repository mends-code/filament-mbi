<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class ChatwootDashboardComponent extends Component
{
    protected $listeners = [
        'update-chatwoot-context',
        'update-chatwoot-payload',
        'get-chatwoot-context',
        'set-chatwoot-session',
    ];

    public function boot()
    {
    }

    public function render()
    {
        return view('livewire.chatwoot-dashboard-component');
    }
}
