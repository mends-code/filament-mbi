<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatwootAccountResource\Pages;
use App\Filament\Resources\ChatwootAccountResource\RelationManagers;
use App\Models\ChatwootAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\FormsComponent;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
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

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')->badge()->color('gray')->sortable(),
            Tables\Columns\TextColumn::make('name'),
            Tables\Columns\TextColumn::make('support_email'),
        ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->filters([])
            ->poll(env('FILAMENT_TABLE_POLL_INTERVAL', 'null'));
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\TextEntry::make('id')->badge()->color('gray'),
                Components\TextEntry::make('name'),
                Components\TextEntry::make('support_email'),
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
