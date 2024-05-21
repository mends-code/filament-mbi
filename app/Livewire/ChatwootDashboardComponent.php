<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;
use App\Events\ChatwootContextUpdated;

class ChatwootDashboardComponent extends Component
{
    protected $listeners = ['updateChatwootContext'];

    public function boot()
    {
    }

    public function updateChatwootContext($context)
    {

    }

    public function render()
    {
        return view('livewire.chatwoot-dashboard-component');
    }
}
