<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatwootConversationResource\Pages;
use App\Filament\Resources\ChatwootConversationResource\RelationManagers;
use App\Models\ChatwootConversation;
use Filament\Forms;
use Filament\Forms\Form;
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
                //
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
            'index' => Pages\ListChatwootConversations::route('/'),
            'create' => Pages\CreateChatwootConversation::route('/create'),
            'edit' => Pages\EditChatwootConversation::route('/{record}/edit'),
        ];
    }
}
