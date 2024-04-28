<?php

namespace App\Filament\Resources\ChatwootConversationResource\Pages;

use App\Filament\Resources\ChatwootConversationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListChatwootConversations extends ListRecords
{
    protected static string $resource = ChatwootConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
