<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use App\Models\ChatwootContact;

class ChatwootContactBanner extends Component implements HasForms
{
    use InteractsWithForms;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('chatwoot_contact')
                            ->options(
                                ChatwootContact::query()
                                    ->get(['id', 'name', 'email', 'phone_number'])
                                    ->mapWithKeys(function ($item) {
                                        $displayName = $item->name ?: 'No Name';
                                        return [$item->id => "<span class=\"font-bold\">{$displayName}</span><span></span><br><span class=\"text-gray-400\">tel:</span> {$item->email}<br><span class=\"text-gray-400\">email:</span> {$item->phone_number}"];
                                    })
                                    ->toArray()
                            )
                            ->preload()
                            ->searchable()
                            ->reactive()
                            ->allowHtml()
                            ->native(false)
                            ->afterStateUpdated(fn (callable $set) => $set('stripe_customer', null)), // Reset Stripe customer when contact changes
                    ])
                    ->columns(1),
            ]);
    }

    public function render()
    {
        return view('livewire.chatwoot-contact-banner');
    }
}
