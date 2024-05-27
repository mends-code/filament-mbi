<?php

namespace App\Filament\Widgets;

use App\Models\ChatwootAccount;
use App\Models\ChatwootConversation;
use App\Models\ChatwootInbox;
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

class ChatwootConversationWidget extends Widget implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions, InteractsWithForms, InteractsWithInfolists;

    protected static string $view = 'filament.widgets.chatwoot-conversation-widget';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    public static bool $isLazy = true;

    #[Reactive]
    public ?array $filters = null;

    public function getConversationPayload()
    {
        $conversationDisplayId = $this->filters['chatwootConversationDisplayId'] ?? null;
        $accountId = $this->filters['chatwootAccountId'] ?? null;
        $inboxId = $this->filters['chatwootInboxId'] ?? null;

        $account = ChatwootAccount::find($accountId);
        $conversation = ChatwootConversation::where('account_id', $accountId)
            ->where('display_id', $conversationDisplayId)
            ->first();
        $inbox = ChatwootInbox::find($inboxId);

        return [
            'account' => $account ? $account->toArray() : [],
            'conversation' => $conversation ? $conversation->toArray() : [],
            'inbox' => $inbox ? $inbox->toArray() : [],
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->state($this->getConversationPayload())
            ->schema([
                Section::make('serviceContextSection.conversation')
                    ->heading('Obsługiwana rozmowa Chatwoot')
                    ->headerActions([
                        Action::make('sendPrivateMessage')
                            ->label('Dodaj notatkę')
                            ->color('warning')
                            ->link()
                            ->icon('heroicon-o-information-circle')
                            ->tooltip('wkrótce'),
                    ])
                    ->schema([
                        TextEntry::make('conversation.display_id')
                            ->badge()
                            ->placeholder('N/A')
                            ->label('Identyfikator rozmowy')
                            ->inlineLabel(),
                        TextEntry::make('conversation.created_at')
                            ->since()
                            ->badge()
                            ->color('gray')
                            ->placeholder('czas utworzenia konwersacji')
                            ->label('Utworzono')
                            ->inlineLabel(),
                        TextEntry::make('account.name')
                            ->placeholder('N/A')
                            ->label('Konto')
                            ->inlineLabel(),
                        TextEntry::make('inbox.name')
                            ->placeholder('N/A')
                            ->label('Skrzynka odbiorcza')
                            ->inlineLabel(),
                        TextEntry::make('conversation.last_activity_at')
                            ->since()
                            ->placeholder('czas ostatniej aktywności')
                            ->label('Ostatnia aktwyność')
                            ->inlineLabel()
                            ->badge()
                            ->color('gray'),
                        TextEntry::make('conversation.waiting_since')
                            ->since()
                            ->placeholder('nie oczekuje na odpowiedź')
                            ->label('Ostatnia wiadomość')
                            ->inlineLabel()
                            ->badge()
                            ->color('warning'),
                    ])
                    ->columns(1),
            ]);
    }
}
