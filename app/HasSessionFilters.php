<?php

namespace App;

use Livewire\Attributes\Session;

trait HasSessionFilters
{
    #[Session]
    public array $filters = [];
}
