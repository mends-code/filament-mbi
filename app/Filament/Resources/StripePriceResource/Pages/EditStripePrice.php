<?php

namespace App\Filament\Resources\StripePriceResource\Pages;

use App\Filament\Resources\StripePriceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditStripePrice extends EditRecord
{
    protected static string $resource = StripePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
