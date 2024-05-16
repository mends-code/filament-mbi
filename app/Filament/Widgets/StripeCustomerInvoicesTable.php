<?php

namespace App\Filament\Widgets;

use App\Models\StripeInvoice;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StripeCustomerInvoicesTable extends BaseWidget
{
    use InteractsWithPageFilters;

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQuery())
            ->columns([
                Tables\Columns\TextColumn::make('data.id')
                    ->label('Invoice ID')
                    ->badge()
                    ->color('gray')
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
                Tables\Columns\TextColumn::make('data.total')
                    ->label('Total')
                    ->money(fn ($record) => $record->data['currency'], divideBy: 100)
                    ->badge()
                    ->color(fn ($record) => $record->data['paid'] ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('data.created')
                    ->label('Created At')
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('data.status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'open' => 'info',
                        'paid' => 'success',
                        'uncollectible' => 'danger',
                        'void' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                ])
            ])
            ->bulkActions([
                // Define bulk actions here
            ])
            ->defaultSort('created_at', 'desc')
            ->poll(env('FILAMENT_TABLE_POLL_INTERVAL', null));
    }

    protected function getQuery(): Builder
    {
        $query = StripeInvoice::query();

        if (!empty($this->filters['chatwootContactId'])) {
            $query->whereHas('customer.contact', function (Builder $query) {
                $query->where('chatwoot_contact_id', $this->filters['chatwootContactId']);
            });
        }

        return $query;
    }
}
