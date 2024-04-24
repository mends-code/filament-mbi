<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatwootAccountResource\Pages;
use App\Filament\Resources\ChatwootAccountResource\RelationManagers;
use App\Models\ChatwootAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChatwootAccountResource extends Resource

{

    protected static ?string $navigationGroup = 'Chatwoot';

    protected static ?string $model = ChatwootAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-queue-list';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('id')->integer()->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('domain'),
            Forms\Components\TextInput::make('support_email')->email(),
            Forms\Components\KeyValue::make('custom_attributes'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('domain'),
            Tables\Columns\TextColumn::make('support_email'),
        ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ])
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
            'index' => Pages\ListChatwootAccounts::route('/'),
            'create' => Pages\CreateChatwootAccount::route('/create'),
            'edit' => Pages\EditChatwootAccount::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
