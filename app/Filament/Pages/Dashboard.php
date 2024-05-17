<?php

namespace App\Filament\Pages;

use App\Models\ChatwootContact;
use App\Models\StripeCustomer;
use App\Models\StripePrice;
use App\Services\StripeService;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
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
                            ->label('Chatwoot Contact')
                            ->preload()
                            ->searchable()
                            ->reactive()
                            ->allowHtml()
                            ->native(false)
                            ->afterStateUpdated(fn(callable $set, $state) => $this->setStripeCustomerIdOptions($set, $state)),

                        Select::make('stripeCustomerId')
                    ])
                    ->columns(2),

                Actions::make([
                    Action::make('createInvoiceUsingPrice')
                        ->modal()
                ]),
            ]);
    }
}
