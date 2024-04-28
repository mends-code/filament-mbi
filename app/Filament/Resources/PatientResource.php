<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PatientResource\Pages;
use App\Filament\Resources\PatientResource\RelationManagers;
use App\Models\Patient;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Database\Eloquent\Model;

class PatientResource extends Resource
{
    public static function getGlobalSearchResultTitle(Model $record): string
    {
        return sprintf('%s %s | %s', $record->first_name, $record->last_name, $record->birthdate);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['first_name', 'last_name', 'birthdate'];
    }

    protected static ?string $navigationGroup = 'Medical Documentation';

    protected static ?string $model = Patient::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('first_name')
                ->required()
                ->label('First Name'),
            Forms\Components\TextInput::make('last_name')
                ->required()
                ->label('Last Name'),
            Forms\Components\DatePicker::make('birthdate')
                ->required()
                ->label('Birthdate')
                ->native(false)
                ->displayFormat('d mm Y'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('id')
                ->label('ID'),
            Tables\Columns\TextColumn::make('first_name')
                ->label('First Name'),
            Tables\Columns\TextColumn::make('last_name')
                ->label('Last Name'),
            Tables\Columns\TextColumn::make('birthdate')
                ->label('Birthdate')
                ->date(),
        ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ViewAction::make()
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
            'index' => Pages\ListPatients::route('/'),
            'create' => Pages\CreatePatient::route('/create'),
            'edit' => Pages\EditPatient::route('/{record}/edit'),
        ];
    }
}
