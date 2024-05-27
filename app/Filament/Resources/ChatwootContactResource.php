<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatwootContactResource\Pages;
use App\Filament\Resources\ChatwootContactResource\RelationManagers;
use App\Models\ChatwootContact;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Rinvex\Country\CountryLoader;

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

    public static function countries()
    {
        $countries = collect(CountryLoader::countries())->mapWithKeys(function ($country) {
            $label = sprintf(
                '%s %s',
                $country['emoji'],
                $country['iso_3166_1_alpha2'],
            );

            return [$country['name'] => $label];
        });

        return $countries;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')->integer()->disabled(),
                Select::make('chatwoot_account_id')
                    ->relationship('account', 'name')
                    ->label('Account')->disabled(),
                DateTimePicker::make('last_activity_at')->nullable()->disabled(),
                TextInput::make('name')->nullable(),
                TextInput::make('email')->nullable()->email(),
                TextInput::make('phone_number')->nullable(),
                TextInput::make('location')->nullable(),
                Select::make('country_code')->nullable()
                    ->options(self::countries())
                    ->searchable()
                    ->placeholder('Select a country'),
                Checkbox::make('blocked')->disabled(),
                Select::make('customer')
                    ->relationship('customer', 'stripe_customer_id')
                    ->searchable(['stripe_customer_id']),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->badge()->color('gray')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('phone_number')->searchable(),
                Tables\Columns\TextColumn::make('customer.id')->badge()->color('gray')->searchable(),
                Tables\Columns\TextColumn::make('last_activity_at')->since()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('last_activity_at')
                    ->queries(
                        true: fn (Builder $query) => $query->whereNotNull('last_activity_at'),
                        false: fn (Builder $query) => $query->whereNull('last_activity_at'),
                        blank: fn (Builder $query) => $query,
                    )->default(true),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->recordAction(null)
            ->poll(env('FILAMENT_TABLE_POLL_INTERVAL', null))
            ->defaultSort('last_activity_at', 'desc');
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
        ];
    }
}
