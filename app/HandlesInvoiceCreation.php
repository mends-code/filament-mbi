<?php

namespace App;

use App\Jobs\CreateStripeInvoiceJob;
use App\Models\StripeCustomer;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;

trait HandlesInvoiceCreation
{

    public function createInvoice(int $contactId, int $currentAgentId, array $items)
    {
        if ($contactId) {
            $customer = StripeCustomer::latestForContact($contactId)->first();

            Log::info('Dispatching CreateStripeInvoiceJob', [
                'contactId' => $contactId,
                'items' => $items,
                'customerId' => $customer->id ?? null,
                'agentId' => $currentAgentId,
            ]);

            CreateStripeInvoiceJob::dispatch($contactId, $items, $customer->id ?? null, $currentAgentId);
        } else {
            Log::warning('No contact ID found.');
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

            if (!isset($options[$productId])) {
                $options[$productId] = [];
            }

            if (!isset($options[$productId][$currency])) {
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

        return $customer->data['currency'];
    }

    protected function getGlobalPriceOptionsForProduct($currency, $productId)
    {
        $allPrices = $this->getGlobalPriceOptions();

        return $allPrices[$productId][$currency] ?? [];
    }
}
