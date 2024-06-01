<?php

namespace App\Filament\Pages;

use App\HasSessionFilters;
use App\Jobs\CreateStripeInvoiceJob;
use App\Models\StripeCustomer;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class Dashboard extends BaseDashboard
{
    use HasSessionFilters;

    protected static ?string $navigationLabel = 'Panel';

    protected static ?string $title = 'Panel';

    protected static ?string $navigationIcon = 'heroicon-o-hand-raised';

    public function mount()
    {
        $this->js('window.addEventListener("message", event => $wire.dispatch("set-dashboard-filters", { context: event.data })); console.log("Filters set")');
    }

    #[On('set-dashboard-filters')]
    public function setDashboardFilters($context)
    {
        $contextData = json_decode($context)->data;

        Arr::set($this->filters, 'chatwootContactId', $contextData->contact->id ?? null);
        Arr::set($this->filters, 'chatwootConversationDisplayId', $contextData->conversation->id ?? null);
        Arr::set($this->filters, 'chatwootInboxId', $contextData->conversation->inbox_id ?? null);
        Arr::set($this->filters, 'chatwootAccountId', $contextData->conversation->account_id ?? null);
        Arr::set($this->filters, 'chatwootCurrentAgentId', $contextData->currentAgent->id ?? null);

        Log::info('Filters set', [
            'contactId' => $this->filters['chatwootContactId'],
            'conversationId' => $this->filters['chatwootConversationDisplayId'],
            'inboxId' => $this->filters['chatwootInboxId'],
            'accountId' => $this->filters['chatwootAccountId'],
            'currentAgentId' => $this->filters['chatwootCurrentAgentId'],
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createInvoice')
                ->modal()
                ->label('Wystaw fakturę')
                ->icon('heroicon-s-document-plus')
                ->form([
                    Select::make('currency')
                        ->label('Wybierz walutę')
                        ->native(false)
                        ->searchable()
                        ->preload()
                        ->live(debounce: '500ms')
                        ->options(fn () => $this->getPriceCurrencies)
                        ->required()
                        ->afterStateUpdated(fn (callable $set) => $set('items.productId', null))
                        ->reactive(),
                    Repeater::make('items')
                        ->disabled(fn (callable $get) => ! $get('currency'))
                        ->label('Dodaj usługi')
                        ->schema([
                            Select::make('productId')
                                ->label('Wybierz usługę')
                                ->options(fn () => $this->getProductOptions)
                                ->required()
                                ->searchable()
                                ->preload()
                                ->live(debounce: '500ms')
                                ->native(false)
                                ->afterStateUpdated(fn (callable $set) => $set('priceId', null)), // Clear price on product change
                            Select::make('priceId')
                                ->native(false)
                                ->live(debounce: '500ms')
                                ->disabled(fn (callable $get) => ! $get('productId'))
                                ->label('Cena')
                                ->options(fn (callable $get) => collect($this->getPriceOptions) //livewire converts calculated method to cached
                                    ->filter(fn ($price) => ($price['product_id'] === $get('productId')) && ($price['currency'] === $get('../../currency'))) // it seems that this filtering does not work -
                                    ->mapWithKeys(fn ($price) => [$price['id'] => ($price['unit_amount'] / 100).' '.strtoupper($price['currency'])])
                                )
                                ->required(),
                            TextInput::make('quantity')
                                ->label('Ilość')
                                ->live(debounce: '500ms')
                                ->disabled(fn (callable $get) => ! $get('priceId'))
                                ->numeric()
                                ->default(1)
                                ->required(),
                        ])
                        // current solution works flawlessly; howver
                        ->required(),
                ])
                ->action(fn (array $data) => $this->handleCreateInvoice($data)),

            Action::make('makeAppointment')->color('gray')->label('Umów wizytę')->icon('heroicon-o-calendar')->tooltip('wkrótce'),
            Action::make('sendEmail')->color('gray')->label('Wyślij email')->icon('heroicon-o-envelope')->tooltip('wkrótce'),
            Action::make('sendSMS')->color('gray')->label('Wyślij sms')->icon('heroicon-o-chat-bubble-bottom-center-text')->tooltip('wkrótce'),
        ];
    }

    #[Computed(persist: true, cache: true)] // this provides caching to lower server load
    protected function getProductOptions()
    {
        $products = StripeProduct::active()->get();

        return $products->pluck('name', 'id')->toArray();
    }

    #[Computed(persist: true, cache: true)] // this provides caching to lower server load
    protected function getPriceOptions()
    {
        $prices = StripePrice::active()->oneTime()->get();

        return $prices->map(function ($price) {
            return [
                'id' => $price->id,
                'product_id' => $price->product_id,
                'unit_amount' => $price->unit_amount,
                'currency' => $price->currency,
            ];
        })->toArray();
    }

    #[Computed(persist: true, cache: true)] // this provides caching to lower server load
    protected function getPriceCurrencies()
    {
        $products = StripePrice::active()->get()->unique('currency');

        return $products->pluck('currency', 'currency')->toArray();
    }

    public function handleCreateInvoice(array $data)
    {
        $contactId = $this->filters['chatwootContactId'] ?? null;
        $currentAgentId = $this->filters['chatwootCurrentAgentId'] ?? null;

        if ($contactId) {
            $customer = StripeCustomer::latestForContact($contactId)->first();
            $items = $data['items'];

            Log::info('Dispatching CreateStripeInvoiceJob', [
                'contactId' => $contactId,
                'items' => $items,
                'customerId' => $customer->id ?? null,
                'agentId' => $currentAgentId,
            ]);

            CreateStripeInvoiceJob::dispatch($contactId, $items, $customer->id ?? null, $currentAgentId);
        } else {
            Log::warning('No contact ID found in filters.');
        }
    }
}
