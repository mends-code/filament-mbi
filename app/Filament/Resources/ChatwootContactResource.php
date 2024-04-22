<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatwootContactResource\Pages;
use App\Filament\Resources\ChatwootContactResource\RelationManagers;
use App\Models\ChatwootContact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChatwootContactResource extends Resource
{
    protected static ?string $model = ChatwootContact::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('id'),
            Forms\Components\TextInput::make('name')->nullable(),
            Forms\Components\TextInput::make('email')->email()->unique()->nullable(),
            Forms\Components\TextInput::make('phone_number')->nullable(),
            Forms\Components\KeyValue::make('additional_attributes')->nullable(),
            Forms\Components\TextInput::make('identifier')->nullable(),
            Forms\Components\KeyValue::make('custom_attributes')->nullable(),
            Forms\Components\DateTimePicker::make('last_activity_at')->nullable(),
            Forms\Components\Select::make('contact_type')->options([
                0 => 'Type 0',
                1 => 'Type 1', // Add more types as needed
            ])->nullable(),
            Forms\Components\TextInput::make('middle_name')->nullable(),
            Forms\Components\TextInput::make('last_name')->nullable(),
            Forms\Components\TextInput::make('location')->nullable(),
            Forms\Components\TextInput::make('country_code')->nullable(),
            Forms\Components\Toggle::make('blocked'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone_number'),
                Tables\Columns\TextColumn::make('last_activity_at')->dateTime(),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\BooleanColumn::make('blocked'),
                ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListChatwootContacts::route('/'),
            'create' => Pages\CreateChatwootContact::route('/create'),
            'edit' => Pages\EditChatwootContact::route('/{record}/edit'),
        ];
    }
}
