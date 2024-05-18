<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\ChatwootContact;

class DashboardContactSelectOption extends Component
{
    public ChatwootContact $contact;

    public function __construct(ChatwootContact $contact)
    {
        $this->contact = $contact;
    }

    public function render()
    {
        return view('components.dashboard-contact-select-option');
    }

}
