<?php

namespace App\Filament\Resources\StripeCustomerResource\Pages;

use App\Filament\Resources\StripeCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListStripeCustomers extends ListRecords
{
    protected static string $resource = StripeCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
