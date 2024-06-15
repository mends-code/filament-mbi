<?php

namespace App\Filament\Widgets\Chatwoot;

use App\Models\Chatwoot\Contact;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Widgets\Concerns\CanPoll;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;

class ContactWidget extends Widget implements HasActions, HasForms, HasInfolists
{
    use CanPoll, InteractsWithActions, InteractsWithForms, InteractsWithInfolists, InteractsWithPageFilters;

    protected static string $view = 'filament.widgets.chatwoot.contact-widget';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 1;

    public static bool $isLazy = true;

    public array $chatwootContactPayload = [];

    #[Computed]
    public function getChatwootContactData()
    {
        $contactId = $this->filters['chatwootContactId'] ?? null;
        Log::info('Fetching contact data', ['contactId' => $contactId]);

        $contact = Contact::find($contactId);

        if ($contact) {
            Log::info('Contact found', ['contact' => $contact->toArray()]);
        } else {
            Log::warning('Contact not found', ['contactId' => $contactId]);

            return [];
        }

        return ['contact' => $contact ? $contact->toArray() : []];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        Log::info('Generating infolist for Chatwoot contact widget');

        return $infolist
            ->state($this->getChatwootContactData)
            ->schema([
                Section::make('serviceContextSection.contact')
                    ->heading('Obsługiwany kontakt Chatwoot')
                    ->headerActions([
                        Action::make('sendContactUpdateForm')
                            ->label('Wyślij formularz')
                            ->color('warning')
                            ->outlined()
                            ->button()
                            ->icon('heroicon-o-user-plus')
                            ->tooltip('wkrótce'),
                    ])
                    ->schema([
                        TextEntry::make('contact.id')
                            ->badge()
                            ->placeholder('N/A')
                            ->label('Identyfikator kontaktu')
                            ->inlineLabel(),
                        TextEntry::make('contact.created_at')
                            ->badge()
                            ->color('gray')
                            ->placeholder('czas utworzenia kontaktu')
                            ->label('Utworzono')
                            ->since()
                            ->inlineLabel(),
                        TextEntry::make('contact.additional_attributes.country_code')
                            ->placeholder('brak kraju')
                            ->label('Kraj pobytu')
                            ->inlineLabel(),
                        TextEntry::make('contact.name')
                            ->placeholder('N/A')
                            ->label('Imię i nazwisko')
                            ->inlineLabel(),
                        TextEntry::make('contact.email')
                            ->placeholder('brak adresu email')
                            ->label('Adres email')
                            ->inlineLabel(),
                        TextEntry::make('contact.phone_number')
                            ->placeholder('brak numeru telefonu')
                            ->label('Numer telefonu')
                            ->inlineLabel(),
                    ])
                    ->columns(1),
            ]);
    }
}
