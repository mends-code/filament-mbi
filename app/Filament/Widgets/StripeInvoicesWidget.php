<?php

namespace App\Filament\Widgets;

use App\Models\StripeInvoice;
use App\Traits\HandlesInvoiceCreation;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Attributes\Computed;

class StripeInvoicesWidget extends Widget implements HasForms, HasInfolists, HasTable
{
    use HandlesInvoiceCreation, InteractsWithForms, InteractsWithInfolists, InteractsWithPageFilters, InteractsWithTable;

    protected static string $view = 'filament.widgets.stripe-invoices-widget';

    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 'full';

    public static bool $isLazy = true;

    protected function paginateTableQuery(Builder $query): CursorPaginator
    {
        return $query->cursorPaginate(($this->getTableRecordsPerPage() === 'all') ? $query->count() : $this->getTableRecordsPerPage());
    }

    #[Computed]
    public function getTableQuery()
    {
        return StripeInvoice::query()
            ->forContact($this->filters['chatwootContactId']);
    }

    public function table(Table $table): Table
    {
        $contactId = $this->filters['chatwootContactId'] ?? null;
        $agentId = $this->filters['chatwootAgentId'] ?? null;
        $conversationId = $this->filters['chatwootConversationId'] ?? null;
        $accountId = $this->filters['chatwootAccountId'] ?? null;

        return $table
            ->query($this->getTableQuery())
            ->paginated()
            ->extremePaginationLinks()
            ->paginationPageOptions([5])
            ->heading('Lista faktur Stripe')
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
                    ->money(fn ($record) => $record->data['currency'], divideBy: 100)
                    ->badge()
                    ->color(fn ($record) => $record->data['paid'] ? 'success' : 'danger'),
                Tables\Columns\TextColumn::make('created')
                    ->label('Utworzono')
                    ->since(),
                Tables\Columns\TextColumn::make('data.status')
                    ->label('Status')
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'open' => 'warning',
                        'paid' => 'success',
                        'uncollectible' => 'danger',
                        'void' => 'gray',
                        'deleted' => 'gray'
                    })
                    ->badge(),
            ])
            ->defaultSort('created', 'desc')
            ->actions([
                Action::make('cloneInvoice')
                    ->label('Skopiuj')
                    ->modalHeading('Skopiuj fakturę')
                    ->modalDescription('Wybierz walutę, konkretną usługę oraz jej cenę. W przypadku płatności za kilka takich samych usług możesz ustawić żądaną ilość.')
                    ->icon('heroicon-o-clipboard-document')
                    ->form(fn ($record) => $this->getInvoiceFormSchema(
                        productId: $record->data['lines']['data'][0]['price']['product'],
                        currency: $record->data['lines']['data'][0]['price']['currency'],
                        priceId: $record->data['lines']['data'][0]['price']['id'],
                        quantity: $record->data['lines']['data'][0]['quantity'],
                    ))
                    ->action(function ($data) use ($contactId, $agentId, $conversationId, $accountId) {
                        $this->chatwootConversationId = $conversationId;
                        $this->chatwootAccountId = $accountId;
                        $this->chatwootAgentId = $agentId;
                        $this->createInvoice($contactId, [$data]);
                    })
                    ->button()
                    ->outlined(),
                ViewAction::make('hostedInvoiceUrlView')
                    ->label('Pokaż link')
                    ->icon('heroicon-o-link')
                    ->form([
                        Textarea::make('data.hosted_invoice_url')
                            ->label('Link do faktury')
                            ->hint('kliknij dwukrotnie lub zaznacz tekst; następnie skopiuj go do schowka'),
                    ])
                    ->closeModalByClickingAway()
                    ->modalCancelAction(false)
                    ->modalHeading('Link do faktury')
                    ->modalCloseButton()
                    ->button()
                    ->outlined(),
            ], position: ActionsPosition::AfterColumns);
    }
}
