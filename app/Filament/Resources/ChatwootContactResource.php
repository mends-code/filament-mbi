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
use Rinvex\Country\CountryLoader;

class ChatwootContactResource extends Resource
{
    protected static ?string $model = ChatwootContact::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $countries = collect(CountryLoader::countries())->mapWithKeys(function ($country) {
            $label = sprintf(
                "%s %s - %s",
                $country['emoji'],
                $country['iso_3166_1_alpha2'],
                $country['name'],
            );
            return [$country['iso_3166_1_alpha2'] => $label];
        });

        return $form->schema([
            Forms\Components\Select::make('chatwoot_account_id')
                ->relationship('account', 'name') // Assuming 'name' is the display field for accounts
                ->searchable()
                ->label('Account'),
            Forms\Components\Toggle::make('blocked'),
            Forms\Components\DateTimePicker::make('last_activity_at')->nullable()->disabled(),
            Forms\Components\TextInput::make('name')->nullable(),
            Forms\Components\TextInput::make('middle_name')->nullable(),
            Forms\Components\TextInput::make('last_name')->nullable(),
            Forms\Components\TextInput::make('email')->email()->unique(ignoreRecord: true)->nullable(),
            Forms\Components\TextInput::make('phone_number')->nullable(),
            Forms\Components\TextInput::make('location')->nullable(),
            Forms\Components\Select::make('country_code')
                ->label('Country Code')
                ->options($countries)
                ->searchable()
                ->placeholder('Select a country')
                ->rules('required', 'exists:rinvex_countries,country_code'),
            Forms\Components\TextInput::make('identifier')->nullable()->disabled(),
            Forms\Components\KeyValue::make('additional_attributes')->nullable()->disabled(),
            Forms\Components\KeyValue::make('custom_attributes')->nullable()->disabled(),
            Forms\Components\Select::make('contact_type')->options([
                0 => 'Type 0',
                1 => 'Type 1', // Add more types as needed
            ])->nullable()->disabled(),
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
                Tables\Columns\IconColumn::make('blocked')->icons([
                    'heroicon-o-lock-closed' => true,
                    'heroicon-o-lock-open' => false,
                ])
                    ->colors([
                        'danger' => true,
                        'info' => false,
                    ]),
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
