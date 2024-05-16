<?php

namespace App\Filament\Pages;

use App\Models\ChatwootContact;
use App\Models\StripePrice;
use App\Services\StripeService;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\App;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('chatwootContactId')
                            ->options(
                                ChatwootContact::query()
                                    ->get(['id', 'name', 'email', 'phone_number'])
                                    ->mapWithKeys(function ($item) {
                                        $displayName = $item->name ?: 'No Name';
                                        return [$item->id => "<span class=\"font-bold\">{$displayName}</span><br><span class=\"text-gray-400\">tel:</span> {$item->phone_number}<br><span class=\"text-gray-400\">email:</span> {$item->email}"];
                                    })
                                    ->toArray()
                            )
                            ->preload()
                            ->searchable()
                            ->reactive()
                            ->allowHtml()
                            ->native(false)
                            ->afterStateUpdated(fn (callable $set, $state) => $set('stripe_customer', $this->getStripeCustomerId($state))),
                    ]),
                Actions::make([
                    Action::make('createInvoiceUsingPrice')
                        ->label('Create Invoice')
                        ->modal('createInvoiceModal')
                        ->form([
                            Select::make('stripe_price')
                                ->label('Price')
                                ->options(
                                    StripePrice::all()->mapWithKeys(function ($item) {
                                        $data = $item->data;
                                        $productName = $item->product['data']['name'];
                                        return [$item->id => "{$productName} (" . ($data['unit_amount'] / 100) . " {$data['currency']})"];
                                    })
                                )
                                ->searchable(),
                        ])
                        ->action(function (array $data) {
                            $stripeService = App::make(StripeService::class);
                            $stripeService->createInvoiceFromPrice($this->filters['chatwootContactId'], $data['stripe_price']);
                        })
                ])
            ]);
    }

    public function getStripeCustomerId($contactId)
    {
        $contact = ChatwootContact::find($contactId);
        if ($contact && $contact->customer()->exists()) {
            return $contact->customer()->first()->id;
        }
        return null;
    }
}
