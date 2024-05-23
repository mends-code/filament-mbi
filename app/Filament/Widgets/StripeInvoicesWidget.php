<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\ChatwootContact;
use App\Models\StripeInvoice;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class StripeInvoicesWidget extends BaseWidget
{
    use InteractsWithPageFilters;

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getTableQuery(): Builder|null
    {
        $chatwootContactId = $this->filters['chatwootContactId'] ?? null;

        return StripeInvoice::whereHas(
            'chatwootContact',
            function ($query) use ($chatwootContactId) {
                $query->where('chatwoot_contact_id', $chatwootContactId);
            }
        );
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Invoice ID')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->icon('heroicon-o-clipboard')
                    ->copyable()
                    ->limit(15),
                Tables\Columns\TextColumn::make('data.hosted_invoice_url')
                    ->label('Hosted Invoice Url')
                    ->icon('heroicon-o-clipboard')
                    ->badge()
                    ->color('warning')
                    ->copyable()
                    ->limit(15),
                Tables\Columns\TextColumn::make('customer.data.id')
                    ->label('Customer ID')
                    ->badge()
                    ->color('gray')
                    ->searchable(),
                Tables\Columns\TextColumn::make('data.total')
                    ->label('Total')
                    ->money(fn($record) => $record->data['currency'], divideBy: 100)
                    ->badge()
                    ->color(fn($record) => $record->data['paid'] ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('created')
                    ->label('Created At')
                    ->since(),
                Tables\Columns\TextColumn::make('data.status')
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
            ->filters([
                Tables\Filters\SelectFilter::make('data.status')
                    ->options([
                        'draft' => 'draft',
                        'open' => 'open',
                        'paid' => 'paid',
                        'uncollectible' => 'uncollectible',
                        'void' => 'void'
                    ])
            ])
            ->recordAction(null)
            ->poll(env('FILAMENT_TABLE_POLL_INTERVAL', null));
    }
}
