<?php

// app/Traits/HandlesStripeInvoice.php

namespace App\Traits;

use App\Jobs\CreateStripeInvoiceJob;
use App\Jobs\SendStripeInvoiceLinkJob;
use App\Models\StripeCustomer;
use App\Models\StripeInvoice;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;

trait HandlesStripeInvoice
{
    use ManagesChatwootMetadata;

    public StripeInvoice $invoice;

    public function initializeHandlesStripeInvoice()
    {
        $this->setChatwootMetadataFromFilters();
    }

    public function getInvoiceStatusColor($status): ?string
    {
        return match ($status) {
            'draft' => 'gray',
            'open' => 'warning',
            'paid' => 'success',
            'uncollectible' => 'danger',
            'void' => 'gray',
            'deleted' => 'gray',
            default => 'gray',
            null => 'gray',
        };
    }

    public function getInvoiceStatusLabel($status): ?string
    {
        return match ($status) {
            'draft' => 'Szkic',
            'open' => 'W trakcie',
            'paid' => 'Zapłacona',
            'uncollectible' => 'Nieściągalna',
            'void' => 'Unieważniona',
            'deleted' => 'Usunięta',
            default => 'Nieznany',
            null => null,
        };
    }

    public function fetchLatestInvoiceData()
    {
        $contactId = $this->chatwootContactId ?? null;

        Log::info('Fetching latest invoice data', ['contactId' => $contactId]);

        if (! $contactId) {
            Log::warning('Contact ID is not provided.');
            $this->invoice = new StripeInvoice();

            return [];
        }

        $invoice = StripeInvoice::latestForContact($contactId)->active()->first();

        if ($invoice) {
            Log::info('Latest invoice found', ['invoiceId' => $invoice->id]);
            $this->invoice = $invoice;
        } else {
            Log::warning('No invoice found for contact', ['contactId' => $contactId]);
            $this->invoice = new StripeInvoice();
        }
    }

    public function setInvoiceById($invoiceId)
    {
        $invoice = StripeInvoice::find($invoiceId);

        if ($invoice) {
            Log::info('Invoice set manually', ['invoiceId' => $invoice->id]);
            $this->invoice = $invoice;
        } else {
            Log::warning('No invoice found with ID', ['invoiceId' => $invoiceId]);
            $this->invoice = new StripeInvoice();
        }
    }

    public function sendStripeInvoiceLink()
    {
        $this->fetchLatestInvoiceData();

        if (! $this->invoice->exists || ! $this->chatwootAccountId || ! $this->chatwootContactId || ! $this->chatwootConversationId) {
            return;
        }

        SendStripeInvoiceLinkJob::dispatch($this->invoice->id, $this->chatwootAccountId, $this->chatwootContactId, $this->chatwootConversationId);

        Log::info('Job dispatched for sending invoice link');
    }

    public function createInvoice(array $items)
    {
        try {
            CreateStripeInvoiceJob::dispatch(
                $items,
                $this->chatwootContactId,
                $this->chatwootAgentId,
                $this->chatwootConversationId,
                $this->chatwootAccountId
            );

            Notification::make()
                ->body('Faktura została pomyślnie utworzona.')
                ->color('success')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->body('Wystąpił błąd podczas tworzenia faktury: '.$e->getMessage())
                ->color('danger')
                ->send();
        }
    }

    public function getInvoiceFormSchema($productId = null, $currency = null, $priceId = null, $quantity = 1): array
    {
        return [
            Grid::make(['default' => 6])
                ->schema([
                    Select::make('productId')
                        ->default($productId)
                        ->label('Usługa')
                        ->placeholder('Wybierz')
                        ->searchPrompt('Wyszukaj')
                        ->loadingMessage('Wczytywanie')
                        ->selectablePlaceholder(false)
                        ->options(fn () => $this->getGlobalProductOptions())
                        ->required()
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->native(false)
                        ->afterStateUpdated(fn (callable $set) => $set('currency', $this->getCustomerCurrency()))
                        ->columnSpan(['default' => 3]),
                    Select::make('currency')
                        ->default($currency)
                        ->label('Waluta')
                        ->placeholder('Wybierz')
                        ->searchPrompt('Wyszukaj')
                        ->loadingMessage('Wczytywanie')
                        ->selectablePlaceholder(false)
                        ->native(false)
                        ->options($this->getGlobalCurrencyOptions())
                        ->required()
                        ->searchable()
                        ->preload()
                        ->reactive()
                        ->hidden(fn (callable $get) => ! $get('productId'))
                        ->afterStateUpdated(fn (callable $set) => $set('priceId', null))
                        ->columnSpan(['default' => 1]),
                    Select::make('priceId')
                        ->default($priceId)
                        ->native(false)
                        ->placeholder('Wybierz')
                        ->searchPrompt('Wyszukaj')
                        ->loadingMessage('Wczytywanie')
                        ->selectablePlaceholder(false)
                        ->reactive()
                        ->searchable()
                        ->preload()
                        ->hidden(fn (callable $get) => ! $get('currency'))
                        ->label('Cena')
                        ->options(fn (callable $get) => $this->getGlobalPriceOptionsForProduct($get('currency'), $get('productId')))
                        ->required()
                        ->columnSpan(['default' => 1]),
                    TextInput::make('quantity')
                        ->default($quantity)
                        ->label('Ilość')
                        ->reactive()
                        ->hidden(fn (callable $get) => ! $get('priceId'))
                        ->numeric()
                        ->required()
                        ->regex('/^[1-9]\d*$/i')
                        ->columnSpan(['default' => 1]),
                ]),
        ];
    }

    #[Computed(persist: true, cache: true)]
    protected function getGlobalCurrencyOptions()
    {
        return StripePrice::select('currency')
            ->distinct()
            ->pluck('currency')
            ->mapWithKeys(fn ($currency) => [$currency => strtoupper($currency)])
            ->toArray();
    }

    #[Computed(persist: true, cache: true)]
    protected function getGlobalProductOptions()
    {
        return StripeProduct::active()->pluck('name', 'id')->toArray();
    }

    #[Computed(persist: true, cache: true)]
    protected function getGlobalPriceOptions()
    {
        $prices = StripePrice::active()->oneTime()->get();

        $options = [];

        foreach ($prices as $price) {
            $productId = $price->product_id;
            $currency = $price->currency;

            if (! isset($options[$productId])) {
                $options[$productId] = [];
            }

            if (! isset($options[$productId][$currency])) {
                $options[$productId][$currency] = [];
            }

            $options[$productId][$currency][$price->id] = ($price->unit_amount / 100);
        }

        return $options;
    }

    protected function getCustomerCurrency()
    {
        $contactId = $this->contactId ?? null;

        $customer = StripeCustomer::latestForContact($contactId)->first();

        return $customer->data['currency'] ?? null;
    }

    protected function getGlobalPriceOptionsForProduct($currency, $productId)
    {
        $allPrices = $this->getGlobalPriceOptions();

        return $allPrices[$productId][$currency] ?? [];
    }
}
