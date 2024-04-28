<?php

namespace App\Filament\Resources\ChatwootConversationResource\Pages;

use App\Filament\Resources\ChatwootConversationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChatwootConversation extends EditRecord
{
    protected static string $resource = ChatwootConversationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
