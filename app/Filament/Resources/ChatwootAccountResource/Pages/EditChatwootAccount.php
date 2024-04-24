<?php

namespace App\Filament\Resources\ChatwootAccountResource\Pages;

use App\Filament\Resources\ChatwootAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChatwootAccount extends EditRecord
{
    protected static string $resource = ChatwootAccountResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
