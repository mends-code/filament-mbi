<?php

namespace App\Filament\Resources\StripePriceResource\Pages;

use App\Filament\Resources\StripePriceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStripePrices extends ListRecords
{
    protected static string $resource = StripePriceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
