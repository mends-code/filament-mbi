<?php

namespace App\Filament\Resources\ChatwootAccountResource\Pages;

use App\Filament\Resources\ChatwootAccountResource;
use Filament\Resources\Pages\ListRecords;

class ListChatwootAccounts extends ListRecords
{
    protected static string $resource = ChatwootAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
