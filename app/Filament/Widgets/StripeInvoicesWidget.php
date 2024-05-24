<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Grouping\Group;
use Filament\Forms\Components\Textarea;
use App\Models\StripeInvoice;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\On;
use Livewire\Attributes\Session;
use Livewire\Attributes\Reactive;

class StripeInvoicesWidget extends BaseWidget
{
    
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public static bool $isLazy = true;
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                StripeInvoice::whereHas(
                    'chatwootContact',
                    function ($query) {
                        $query->where('chatwoot_contact_id', $this->filters['chatwootContactId'] ?? null);
                    }
                )
            )
            ->deferLoading()
            ->heading('Lista faktur')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID faktury')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('customer.data.id')
                    ->label('ID klienta')
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('data.total')
                    ->label('Suma')
                    ->money(fn($record) => $record->data['currency'], divideBy: 100)
                    ->badge()
                    ->color(fn($record) => $record->data['paid'] ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('created')
                    ->label('Utworzono')
                    ->since(),
                Tables\Columns\IconColumn::make('data.status')
                    ->label('Status')
                    ->color(fn(string $state): string => match ($state) {
                        'draft' => 'gray',
                        'open' => 'info',
                        'paid' => 'success',
                        'uncollectible' => 'danger',
                        'void' => 'gray'
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'draft' => 'heroicon-o-pencil',
                        'open' => 'heroicon-o-lock-open',
                        'paid' => 'heroicon-o-check',
                        'uncollectible' => 'heroicon-o-face-frown',
                        'void' => 'heroicon-o-archive-box'
                    })
            ])
            ->actions([
                ViewAction::make('hostedInvoiceUrlView')
                    ->label('Pokaż link')
                    ->icon('heroicon-o-link')
                    ->form([
                        Textarea::make('data.hosted_invoice_url')
                            ->label('Link do faktury')
                            ->hint('kliknij dwukrotnie lub zaznacz tekst; następnie skopiuj go do schowka')
                    ])
                    ->closeModalByClickingAway()
                    ->modalCancelAction(false)
                    ->modalHeading('Link do faktury')
                    ->modalCloseButton(),
                ViewAction::make('recordView')
                    ->label('Zobacz fakturę')
                    ->icon('heroicon-o-eye')
                    ->infolist([
                        Fieldset::make('invoice-data')
                            ->schema([
                                TextEntry::make('customer.data.id')->badge()->color('gray')->label('Identyfikator klienta'),
                                TextEntry::make('customer.data.name')->label('Name'),
                                TextEntry::make('customer.data.email')->label('Email'),
                                TextEntry::make('customer.data.phone')->label('Phone'),
                            ])
                            ->columns([
                                'sm' => 1,
                                'xl' => 2,
                            ]),
                        Fieldset::make('invoice-data')
                            ->schema([
                                TextEntry::make('data.id')->badge()->color('gray')->label('ID'),
                                TextEntry::make('data.total')
                                    ->label('Total')
                                    ->money(fn($record) => $record->data['currency'], divideBy: 100)
                                    ->badge()
                                    ->color(fn($record) => $record->data['paid'] ? 'success' : 'danger'),
                                TextEntry::make('data.created')->since()->label('Created At'),
                                TextEntry::make('data.status')
                                    ->label('Status')
                                    ->badge()
                                    ->color(fn(string $state): string => match ($state) {
                                        'draft' => 'gray',
                                        'open' => 'info',
                                        'paid' => 'success',
                                        'uncollectible' => 'danger',
                                        'void' => 'gray'
                                    }),
                            ])
                            ->columns([
                                'sm' => 1,
                                'xl' => 2,
                            ]),
                        Fieldset::make('invoice-items')
                            ->schema([
                                RepeatableEntry::make('data.lines.data')
                                    ->schema([
                                        TextEntry::make('description'),
                                        TextEntry::make('price.unit_amount')
                                            ->money(fn($record) => $record->data['currency'], divideBy: 100)
                                            ->badge()
                                            ->color('gray'),
                                        TextEntry::make('quantity'),
                                        TextEntry::make('amount')
                                            ->money(fn($record) => $record->data['currency'], divideBy: 100)
                                            ->badge()
                                            ->color('gray'),
                                    ])
                                    ->label('')
                                    ->columns([
                                        'sm' => 2,
                                        'xl' => 4,
                                    ]),
                            ])
                            ->columns(1),
                    ])
            ], position: ActionsPosition::BeforeColumns)
            ->paginated(false);
    }
}
