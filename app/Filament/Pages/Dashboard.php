<?php

namespace App\Filament\Pages;

use App\Models\ChatwootContact;
use App\Models\StripeCustomer;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        $form->schema([
            Section::make()
                ->schema([
                    Select::make('chatwoot_contact')
                        ->options(
                            ChatwootContact::query()
                                ->get(['id', 'name', 'email', 'phone_number'])
                                ->mapWithKeys(function ($item) {
                                    $displayName = $item->name ?: 'No Name';
                                    return [$item->id => "{$displayName} {$item->email} {$item->phone_number}"];
                                })
                                ->toArray()
                        )
                        ->preload()
                        ->searchable()
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('stripe_customer', null)), // Reset Stripe customer when contact changes
                ])
                ->columns(2),
        ]);
        return $form;
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        $infolist->schema([
            Components\TextEntry::make('test')->label('test'),
        ]);
        return $infolist;
    }
}
