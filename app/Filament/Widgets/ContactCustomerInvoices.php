<?php

namespace App\Filament\Widgets;

use App\Models\ChatwootContact;
use App\Models\StripeInvoice;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ContactCustomerInvoices extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    use InteractsWithPageFilters;

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getInvoicesQuery())
            ->heading('Faktury Stripe')
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('Invoice ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data.amount_due')
                    ->label('Amount Due')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data.status')
                    ->label('Status')
                    ->sortable(),
                Tables\Columns\TextColumn::make('data.created')
                    ->label('Created At')
                    ->date()
                    ->sortable(),
            ]);
    }

    protected function getInvoicesQuery(): Builder
    {
        return StripeInvoice::query()
            ->whereIn('customer_id', function ($query) {
                $query->select('id')
                    ->from('mbi_stripe.customers')
                    ->where('chatwoot_contact_id', $this->filters['chatwootContactId']);
            });
    }
}
