<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatwootContactResource\Pages;
use App\Filament\Resources\ChatwootContactResource\RelationManagers;
use App\Models\ChatwootContact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Rinvex\Country\CountryLoader;
use Illuminate\Database\Eloquent\Model;

class ChatwootContactResource extends Resource
{
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return sprintf('%s | %s | %s', $record->name, $record->email, $record->phone_number);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['id', 'name', 'email', 'phone_number'];
    }

    protected static ?string $navigationGroup = 'Chatwoot';

    protected static ?string $model = ChatwootContact::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    public static function form(Form $form): Form
    {
        $countries = collect(CountryLoader::countries())->mapWithKeys(function ($country) {
            $label = sprintf(
                "%s",
                $country['emoji'],
                $country['iso_3166_1_alpha2'],
                $country['name'],
            );
            return [$country['iso_3166_1_alpha2'] => $label];
        });

        return $form->schema([
            Forms\Components\TextInput::make('id')->integer()->disabled(),
            Forms\Components\Select::make('chatwoot_account_id')
                ->relationship('account', 'name') // Assuming 'name' is the display field for accounts
                ->label('Account')->disabled(),
            Forms\Components\DateTimePicker::make('last_activity_at')->nullable()->disabled(),
            Forms\Components\TextInput::make('name')->nullable(),
            Forms\Components\TextInput::make('middle_name')->nullable(),
            Forms\Components\TextInput::make('last_name')->nullable(),
            Forms\Components\TextInput::make('email')->email()->unique(ignoreRecord: true)->nullable(),
            Forms\Components\TextInput::make('phone_number')->nullable(),
            Forms\Components\TextInput::make('location')->nullable(),
            Forms\Components\Select::make('country_code')->nullable()
                ->label('Country Code')
                ->options($countries)
                ->searchable()
                ->placeholder('Select a country')
                ->rules('exists:rinvex_countries,country_code'),
            Forms\Components\TextInput::make('identifier')->nullable(),
            Forms\Components\Checkbox::make('blocked'),
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
                Tables\Columns\CheckboxColumn::make('blocked'),
            ])
            ->filters([
            ])
            ->actions([
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PatientsRelationManager::class,
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
