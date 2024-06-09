<?php

namespace App\Traits;

use App\Jobs\CreateStripeInvoiceJob;
use App\Models\StripeCustomer;
use App\Models\StripePrice;
use App\Models\StripeProduct;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Livewire\Attributes\Computed;

trait HandlesInvoiceCreation
{
    use HasChatwootProperties;

    public function createInvoice(array $items)
    {
        CreateStripeInvoiceJob::dispatch(
            $items,
            $this->chatwootContactId,
            $this->chatwootAgentId,
            $this->chatwootConversationId,
            $this->chatwootAccountId
        );
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