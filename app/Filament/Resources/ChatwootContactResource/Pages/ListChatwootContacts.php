<?php

namespace App\Filament\Resources\ChatwootContactResource\Pages;

use App\Filament\Resources\ChatwootContactResource;
use Filament\Resources\Pages\ListRecords;

class ListChatwootContacts extends ListRecords
{
    protected static string $resource = ChatwootContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
