<?php

namespace App\Filament\Resources\ChatwootAccountResource\Pages;

use App\Filament\Resources\ChatwootAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditChatwootAccount extends EditRecord
{
    protected static string $resource = ChatwootAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
