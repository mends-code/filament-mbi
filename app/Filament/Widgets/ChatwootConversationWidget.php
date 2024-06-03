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
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Computed;

class ChatwootConversationWidget extends Widget implements HasActions, HasForms, HasInfolists
{
    use InteractsWithActions, InteractsWithForms, InteractsWithInfolists, InteractsWithPageFilters;

    protected static string $view = 'filament.widgets.chatwoot-conversation-widget';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    public static bool $isLazy = true;

    #[Computed]
    public function getConversationData()
    {
        $conversationDisplayId = $this->filters['chatwootConversationDisplayId'] ?? null;
        $accountId = $this->filters['chatwootAccountId'] ?? null;
        $inboxId = $this->filters['chatwootInboxId'] ?? null;

        Log::info('Fetching conversation data', [
            'conversationDisplayId' => $conversationDisplayId,
            'accountId' => $accountId,
            'inboxId' => $inboxId,
        ]);

        $account = ChatwootAccount::find($accountId);
        $conversation = ChatwootConversation::where('account_id', $accountId)
            ->where('display_id', $conversationDisplayId)
            ->first();
        $inbox = ChatwootInbox::find($inboxId);

        if ($account) {
            Log::info('Account found', ['account' => $account->toArray()]);
        } else {
            Log::warning('Account not found', ['accountId' => $accountId]);
        }

        if ($conversation) {
            Log::info('Conversation found', ['conversation' => $conversation->toArray()]);
        } else {
            Log::warning('Conversation not found', ['conversationDisplayId' => $conversationDisplayId]);
        }

        if ($inbox) {
            Log::info('Inbox found', ['inbox' => $inbox->toArray()]);
        } else {
            Log::warning('Inbox not found', ['inboxId' => $inboxId]);
        }

        return [
            'account' => $account ? $account->toArray() : [],
            'conversation' => $conversation ? $conversation->toArray() : [],
            'inbox' => $inbox ? $inbox->toArray() : [],
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        Log::info('Generating infolist for Chatwoot conversation widget');

        return $infolist
            ->state($this->getConversationData)
            ->schema([
                Section::make('serviceContextSection.conversation')
                    ->heading('Obsługiwana rozmowa Chatwoot')
                    ->headerActions([
                        Action::make('sendPrivateMessage')
                            ->label('Dodaj notatkę')
                            ->color('warning')
                            ->outlined()
                            ->button()
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
