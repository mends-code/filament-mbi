<?php

namespace App\Filament\Resources\ChatwootContactResource\Pages;

use App\Filament\Resources\ChatwootContactResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChatwootContact extends EditRecord
{
    protected static string $resource = ChatwootContactResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
