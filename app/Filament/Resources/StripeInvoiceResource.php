<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StripeInvoiceResource\Pages;
use App\Models\StripeInvoice;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Infolist;

class StripeInvoiceResource extends Resource
{
    protected static ?string $model = StripeInvoice::class;

    protected static ?string $navigationGroup = 'Stripe';

    protected static ?string $navigationIcon = 'heroicon-o-document-plus';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('data.id'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('data.id')->label('Invoice ID')->sortable()->badge()->color('gray'),
                Tables\Columns\TextColumn::make('customer.data.name')
                    ->label('Customer Name')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('data.total')
                    ->label('Total')
                    ->money(fn ($record) => $record->data['currency'], divideBy: 100)
                    ->badge()
                    ->color(fn ($record) => $record->data['paid'] ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('data.created')->label('Created At')->sortable()->since(),
                Tables\Columns\TextColumn::make('data.status')
                    ->label('Status')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'open' => 'info',
                        'paid' => 'success',
                        'uncollectible' => 'danger',
                        'void' => 'gray'
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                // Define bulk actions here
            ])
            ->poll(env('FILAMENT_TABLE_POLL_INTERVAL', 'null'));
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Fieldset::make('invoice-data')
                    ->schema([
                        TextEntry::make('customer.data.id')->badge()->color('gray')->label('ID'),
                        TextEntry::make('customer.data.name')->label('Name'),
                        TextEntry::make('customer.data.email')->label('Email'),
                        TextEntry::make('customer.data.phone')->label('Phone'),
                    ])
                    ->columns([
                        'sm' => 1,
                        'xl' => 2,
                    ]),
                Fieldset::make('invoice-data')
                    ->schema([
                        TextEntry::make('data.id')->badge()->color('gray')->label('ID'),
                        TextEntry::make('data.total')
                            ->label('Total')
                            ->money(fn ($record) => $record->data['currency'], divideBy: 100)
                            ->badge()
                            ->color(fn ($record) => $record->data['paid'] ? 'success' : 'danger'),
                        TextEntry::make('data.created')->since()->label('Created At'),
                        TextEntry::make('data.status')
                            ->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'draft' => 'gray',
                                'open' => 'info',
                                'paid' => 'success',
                                'uncollectible' => 'danger',
                                'void' => 'gray'
                            }),
                    ])
                    ->columns([
                        'sm' => 1,
                        'xl' => 2,
                    ]),
                Fieldset::make('invoice-items')
                    ->schema([
                        RepeatableEntry::make('data.lines.data')
                            ->schema([
                                TextEntry::make('description'),
                                TextEntry::make('price.unit_amount')
                                    ->money(fn ($record) => $record->data['currency'], divideBy: 100)
                                    ->badge()
                                    ->color('gray'),
                                TextEntry::make('quantity'),
                                TextEntry::make('amount')
                                    ->money(fn ($record) => $record->data['currency'], divideBy: 100)
                                    ->badge()
                                    ->color('gray'),
                            ])
                            ->label('')
                            ->columns([
                                'sm' => 1,
                                'xl' => 4,
                            ]),

                    ])
                    ->columns(1),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // Define any relations here if necessary
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStripeInvoices::route('/'),
        ];
    }
}
