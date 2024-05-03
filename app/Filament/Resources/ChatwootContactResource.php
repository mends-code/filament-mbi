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
                "%s %s",
                $country['emoji'],
                $country['iso_3166_1_alpha2'],
            );
            return [$country['name'] => $label];
        });

        return $form
            ->schema([
                Forms\Components\TextInput::make('id')->integer()->disabled(),
                Forms\Components\Select::make('chatwoot_account_id')
                    ->relationship('account', 'name') // Assuming 'name' is the display field for accounts
                    ->label('Account')->disabled(),
                Forms\Components\DateTimePicker::make('last_activity_at')->nullable()->disabled(),
                Forms\Components\TextInput::make('name')->nullable(),
                Forms\Components\TextInput::make('email')->nullable()->email(),
                Forms\Components\TextInput::make('phone_number')->nullable(),
                Forms\Components\TextInput::make('location')->nullable(),
                Forms\Components\Select::make('country_code')->nullable()
                    ->options($countries)
                    ->searchable()
                    ->placeholder('Select a country'),
                Forms\Components\TextInput::make('identifier')->nullable(),
                Forms\Components\Checkbox::make('blocked'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('email')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('phone_number')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('last_activity_at')->dateTime()->sortable()->searchable(),
                Tables\Columns\TextColumn::make('country_code')->sortable()->searchable(),
                Tables\Columns\CheckboxColumn::make('blocked')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('last_activity_at')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('last_activity_at'),
                        false: fn (Builder $query) => $query->whereNull('last_activity_at'),
                        blank: fn (Builder $query) => $query, // In this example, we do not want to filter the query when it is blank.
                    )
            ])
            ->actions([])
            ->bulkActions([])
            ->persistFiltersInSession();
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
