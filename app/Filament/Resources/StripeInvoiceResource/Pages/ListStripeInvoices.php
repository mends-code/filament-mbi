<?php

namespace App\Filament\Resources\StripeInvoiceResource\Pages;

use App\Filament\Resources\StripeInvoiceResource;
use Filament\Resources\Pages\ListRecords;

class ListStripeInvoices extends ListRecords
{
    protected static string $resource = StripeInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
