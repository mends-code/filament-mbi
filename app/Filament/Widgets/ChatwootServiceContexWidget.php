<?php

namespace App\Filament\Widgets;

use App\Models\ChatwootAccount;
use App\Models\ChatwootContact;
use App\Models\ChatwootConversation;
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
use Filament\Infolists\Components\TextEntry\TextEntrySize;

use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;

use Livewire\Attributes\Modelable;
use Livewire\Attributes\Reactive;

class ChatwootServiceContexWidget extends Widget implements HasForms, HasInfolists
{
    use InteractsWithForms, InteractsWithInfolists;

    protected static string $view = 'filament.widgets.chatwoot-service-context-infolist';

    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    public static bool $isLazy = true;

    #[Reactive] 
    public ?array $filters = null;


    public function getChatwootPayload()
    {

        $contactId = $this->filters['chatwootContactId'] ?? null;
        $conversationDisplayId = $this->filters['chatwootConversationDisplayId'] ?? null;
        $accountId = $this->filters['chatwootAccountId'] ?? null;
        $inboxId = $this->filters['chatwootInboxId'] ?? null;

        $contact = ChatwootContact::find($contactId);
        $account = ChatwootAccount::find($accountId);
        $conversation = ChatwootConversation::where('account_id', $accountId)
            ->where('display_id', $conversationDisplayId)
            ->where('contact_id', $contactId)
            ->first();
        $inbox = ChatwootInbox::find($inboxId);

        return [
            'contact' => $contact ? $contact->toArray() : [],
            'account' => $account ? $account->toArray() : [],
            'conversation' => $conversation ? $conversation->toArray() : [],
            'inbox' => $inbox ? $inbox->toArray() : [],
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state($this->getChatwootPayload())
            ->schema([
                Split::make([
                    Section::make('serviceContextSection.contact')
                        ->heading('Obsługiwany kontakt')
                        ->schema([
                            TextEntry::make('contact.id')
                                ->badge()
                                ->placeholder('N/A')
                                ->label('Identyfikator kontaktu')
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
                                ->inlineLabel()
                                ->icon('heroicon-o-clipboard')
                                ->badge()
                                ->color('gray'),
                            TextEntry::make('contact.phone_number')
                                ->placeholder('brak numeru telefonu')
                                ->label('Numer telefonu')
                                ->inlineLabel()
                                ->icon('heroicon-o-clipboard')
                                ->badge()
                                ->color('gray'),
                        ])
                        ->columns(1),
                    Section::make('serviceContextSection.conversation')
                        ->heading('Obsługiwana rozmowa')
                            ->schema([
                            TextEntry::make('conversation.display_id')
                                ->badge()->placeholder('N/A')->label('Identyfikator rozmowy')->inlineLabel(),
                            TextEntry::make('account.name')->placeholder('N/A')->label('Konto')->inlineLabel(),
                            TextEntry::make('inbox.name')->placeholder('N/A')->label('Skrzynka odbiorcza')->inlineLabel(),
                            TextEntry::make('contact.created_at')->since()->placeholder('czas pierwszego kontaktu')->label('Kontakt utworzono')->inlineLabel(),
                            TextEntry::make('conversation.last_activity_at')->since()->placeholder('czas ostatniej aktywności')->label('Ostatnia aktwyność')->inlineLabel()
                        ])
                        ->columns(1),
                ])
                    ->from('lg'),
            ]);
    }
}
