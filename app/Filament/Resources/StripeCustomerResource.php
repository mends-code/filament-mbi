<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StripeCustomerResource\Pages;
use App\Filament\Resources\StripeCustomerResource\RelationManagers;
use App\Models\StripeCustomer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class StripeCustomerResource extends Resource
{
    protected static ?string $model = StripeCustomer::class;

    protected static ?string $navigationGroup = 'Stripe';

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

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
                Tables\Columns\TextColumn::make('stripe_id')->badge()->color('gray')->label('Stripe ID')->searchable(),
                Tables\Columns\TextColumn::make('data.name')->label('Name'),
                Tables\Columns\TextColumn::make('contact.id'),
                Tables\Columns\TextColumn::make('data.email')->label('Email'),
                Tables\Columns\TextColumn::make('data.currency')->badge()->color('info')->label('Currency'),
                Tables\Columns\TextColumn::make('data.created')->label('Created At')->sortable()->since(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])
            ->defaultSort('data.created', 'desc')
            ->poll(env('FILAMENT_TABLE_POLL_INTERVAL', 'null'));
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
            'index' => Pages\ListStripeCustomers::route('/'),
        ];
    }
}
