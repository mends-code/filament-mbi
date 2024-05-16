<?php

namespace App\Filament\Resources\StripePriceResource\Pages;

use App\Filament\Resources\StripePriceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateStripePrice extends CreateRecord
{
    protected static string $resource = StripePriceResource::class;
}
