<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatwootConversationResource\Pages;
use App\Filament\Resources\ChatwootConversationResource\RelationManagers;
use App\Models\ChatwootConversation;
use Filament\Forms;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChatwootConversationResource extends Resource
{

    protected static ?string $navigationGroup = 'Chatwoot';

    protected static ?string $model = ChatwootConversation::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->badge()->color('gray')->sortable(),
                Tables\Columns\TextColumn::make('account.name'),
                Tables\Columns\TextColumn::make('contact.name'),
                Tables\Columns\TextColumn::make('last_activity_at')->since()->sortable(),
                Tables\Columns\TextColumn::make('created_at')->since()->sortable(),
                Tables\Columns\TextColumn::make('contact_last_seen_at')->since()->sortable(),
                Tables\Columns\TextColumn::make('agent_last_seen_at')->since()->sortable(),
                Tables\Columns\TextColumn::make('assignee_last_seen_at')->since()->sortable(),
                Tables\Columns\TextColumn::make('first_reply_created_at')->since()->sortable(),
                Tables\Columns\TextColumn::make('waiting_since')->since()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
            ])
            ->bulkActions([])
            ->recordAction(null)
            ->poll(env('FILAMENT_TABLE_POLL_INTERVAL', null));
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('id')->badge()->color('gray'),
                Infolists\Components\TextEntry::make('account.name'),
                Infolists\Components\TextEntry::make('contact.name'),

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
            'index' => Pages\ListChatwootConversations::route('/'),
        ];
    }
}
