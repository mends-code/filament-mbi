<?php

namespace App\Filament\Pages;

use App\Models\ChatwootContact;
use App\Models\ChatwootConversation;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Forms\Components\Actions\Action;
use Illuminate\Support\Facades\Blade;

class AssistantDashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    protected static ?string $navigationLabel = "Panel Asystenta";

    protected static ?string $title = "Panel Asystenta";

    protected static ?string $navigationIcon = "heroicon-o-hand-raised";

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Kontekst obsługi')
                    ->headerActions([
                        Action::make('createInvoiceUsingPrice')
                            ->label('Szybka Faktura')
                            ->modal()
                            ->color('primary'),
                        Action::make('test')
                            ->modal()
                            ->color('gray'),
                        Action::make('test1')
                            ->modal()
                            ->color('gray'),
                    ])
                    ->schema([
                        Select::make('chatwootContactId')
                            ->label('Kontakt')
                            ->searchable()
                            ->getSearchResultsUsing(fn (string $search): array => $this->getChatwootContactsSearchResults($search))
                            ->getOptionLabelUsing(fn ($value): ?string => $this->getChatwootContactLabel($value))
                            ->reactive()
                            ->allowHtml()
                            ->native(false)
                            ->afterStateUpdated(function () {
                                $this->filters['chatwootConversationId'] = null;
                            }),

                        Select::make('chatwootConversationId')
                            ->label('Rozmowa')
                            ->options(fn () => $this->getChatwootConversationsOptions($this->filters['chatwootContactId']))
                            ->allowHtml()
                            ->native(false)
                            ->disabled(fn() => empty($this->filters['chatwootContactId'])),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getChatwootContactsSearchResults(string $search): array
    {
        $words = explode(' ', $search);
        $query = ChatwootContact::query();

        foreach ($words as $word) {
            $query->where(function ($q) use ($word) {
                $q->where('id', 'ILIKE', "%{$word}%")
                  ->orWhere('name', 'ILIKE', "%{$word}%")
                  ->orWhere('email', 'ILIKE', "%{$word}%")
                  ->orWhere('phone_number', 'ILIKE', "%{$word}%");
            });
        }

        return $query->limit(10)->get()
            ->mapWithKeys(function ($contact) {
                return [$contact->id => $this->getChatwootContactLabel($contact->id)];
            })
            ->toArray();
    }

    protected function getChatwootContactLabel($value): ?string
    {
        $contact = ChatwootContact::find($value);

        if ($contact) {
            return Blade::render('components.dashboard-contact-select-option', ['contact' => $contact]);
        }

        return null;
    }

    protected function getChatwootConversationsOptions($contactId): array
    {
        if (!$contactId) {
            return [];
        }

        return ChatwootConversation::where('contact_id', $contactId)->get()->mapWithKeys(function ($conversation) {
            $html = Blade::render('components.dashboard-conversation-select-option', ['conversation' => $conversation]);
            return [$conversation->id => $html];
        })->toArray();
    }
}
