<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Session;

class ChatwootDashboardComponent extends Component
{
    public function boot()
    {
    }

    public function render()
    {
        return view('livewire.chatwoot-dashboard-component');
    }
}
