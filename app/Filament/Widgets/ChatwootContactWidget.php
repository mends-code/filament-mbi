<?php

namespace App\Filament\Widgets;

use App\Models\ChatwootContact;
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
use Filament\Widgets\Widget;
use Livewire\Attributes\Reactive;

class ChatwootContactWidget extends Widget implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions, InteractsWithForms, InteractsWithInfolists;

    protected static string $view = 'filament.widgets.chatwoot-contact-widget';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 1;

    public static bool $isLazy = true;

    #[Reactive]
    public ?array $filters = null;

    public function getContactPayload()
    {
        $contactId = $this->filters['chatwootContactId'] ?? null;
        $contact = ChatwootContact::find($contactId);

        return ['contact' => $contact ? $contact->toArray() : []];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state($this->getContactPayload())
            ->schema([
                Section::make('serviceContextSection.contact')
                    ->heading('Obsługiwany kontakt Chatwoot')
                    ->headerActions([
                        Action::make('sendContactUpdateForm')
                            ->label('Wyślij formularz')
                            ->link()
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
