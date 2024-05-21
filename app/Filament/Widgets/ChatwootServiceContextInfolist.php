<?php

namespace App\Filament\Widgets;

use App\Models\ChatwootAccount;
use App\Models\ChatwootInbox;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;

use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Split;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

use Livewire\Attributes\On;

class ChatwootServiceContextInfolist extends Widget implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithPageFilters, InteractsWithInfolists;

    protected static string $view = 'filament.widgets.chatwoot-service-context-infolist';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public array $chatwootContext = [];

    #[On('update-chatwoot-context')]
    public function updateChatwootContext($context)
    {
        $this->chatwootContext = json_decode($context, true); // Ensure json_decode returns an associative array

        // Fetch and update account_name
        if (isset($this->chatwootContext['data']['conversation']['account_id'])) {
            $account = ChatwootAccount::find($this->chatwootContext['data']['conversation']['account_id']);
            if ($account) {
                $this->chatwootContext['data']['conversation']['account_name'] = $account->name;
            }
        }

        // Fetch and update inbox_name
        if (isset($this->chatwootContext['data']['conversation']['inbox_id'])) {
            $inbox = ChatwootInbox::find($this->chatwootContext['data']['conversation']['inbox_id']);
            if ($inbox) {
                $this->chatwootContext['data']['conversation']['inbox_name'] = $inbox->name;
            }
        }
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state($this->chatwootContext) // Use the array directly
            ->schema([
                Split::make([
                    Section::make('serviceContextSection.contact')
                        ->heading('Obsługiwany kontakt')
                        ->description('Dane kontaktowe mogą być wspólne dla różnych rozmów')
                        ->schema([
                            TextEntry::make('data.contact.id')->badge()->placeholder('N/A')->label('Identyfikator kontaktu')->inlineLabel()->icon('heroicon-o-clipboard')->copyable(),
                            TextEntry::make('data.conversation.meta.sender.additional_attributes.country_code')->placeholder('brak kraju')->label('Kraj pobytu')->inlineLabel(),
                            TextEntry::make('data.contact.name')->placeholder('N/A')->label('Imię i nazwisko')->inlineLabel(),
                            TextEntry::make('data.contact.email')->placeholder('brak adresu email')->label('Adres email')->inlineLabel()->icon('heroicon-o-clipboard')->copyable()->badge()->color('gray'),
                            TextEntry::make('data.contact.phone_number')->placeholder('brak numeru telefonu')->label('Numer telefonu')->inlineLabel()->icon('heroicon-o-clipboard')->copyable()->badge()->color('gray'),
                        ])
                        ->columns(1),
                    Section::make('serviceContextSection.conversation')
                        ->heading('Obsługiwana rozmowa')
                        ->description('Aktualna rozmowa z poziomu której otwarto panel')
                        ->schema([
                            TextEntry::make('data.conversation.id')->badge()->placeholder('N/A')->copyable()->label('Identyfikator rozmowy')->inlineLabel()->icon('heroicon-o-clipboard'),
                            TextEntry::make('data.conversation.account_name')->placeholder('N/A')->label('Konto')->inlineLabel(),
                            TextEntry::make('data.conversation.inbox_name')->placeholder('N/A')->label('Skrzynka odbiorcza')->inlineLabel(),
                            TextEntry::make('data.contact.created_at')->since()->placeholder('czas pierwszego kontaktu')->label('Kontakt utworzono')->inlineLabel(),
                            TextEntry::make('data.conversation.last_activity_at')->since()->placeholder('czas ostatniej aktywności')->label('Ostatnia aktwyność')->inlineLabel() 
                        ])
                        ->columns(1),

                ])
            ]);
    }
}
