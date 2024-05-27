<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StripePriceResource\Pages;
use App\Models\StripePrice;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StripePriceResource extends Resource
{
    protected static ?string $model = StripePrice::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Stripe';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Invoice ID')->badge()->color('gray')->searchable()
                    ->icon('heroicon-o-clipboard')
                    ->copyable()
                    ->limit(15),
                Tables\Columns\TextColumn::make('product.data.name'),
                Tables\Columns\TextColumn::make('data.unit_amount')
                    ->label('Unit Amount')
                    ->money(fn ($record) => $record->data['currency'], divideBy: 100)
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->actions([
            ])
            ->bulkActions([
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStripePrices::route('/'),
            'create' => Pages\CreateStripePrice::route('/create'),
            'edit' => Pages\EditStripePrice::route('/{record}/edit'),
        ];
    }
}
