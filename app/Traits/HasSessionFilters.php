<?php

namespace App\Traits;

use Livewire\Attributes\Session;

trait HasSessionFilters
{
    #[Session]
    public array $filters = [];
}
