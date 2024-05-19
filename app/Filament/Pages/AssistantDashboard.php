<?php

namespace App\Filament\Pages;

use App\Models\ChatwootContact;
use App\Models\ChatwootConversation;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Forms\Form;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Get;
use Filament\Forms\Set;

class AssistantDashboard extends BaseDashboard
{
    use HasFiltersAction;

    protected static ?string $navigationLabel = "Panel Asystenta";

    protected static ?string $title = "Panel Asystenta";

    protected static ?string $navigationIcon = "heroicon-o-hand-raised";

    protected function getHeaderActions(): array
    {
        return [
            Action::make('CreateInvoice'),
            FilterAction::make('changeServiceScope')
                ->label(fn() => session('isEmbeddedMode', false) ? 'Sprawdź kontekst obsługi' : 'Zmień kontekst obsługi')
                ->modalHeading()
                ->slideOver(false)
                ->icon('heroicon-o-arrow-path')
                ->modalWidth('3xl')
                ->form([
                    Grid::make(1)
                        ->schema(function (Get $get, Set $set) {
                            $contactId = $get('chatwootContactId');

                            return [
                                Select::make('chatwootContactId')
                                    ->disabled(session('isEmbeddedMode', false))
                                    ->label('Kontakt')
                                    ->searchable()
                                    ->getSearchResultsUsing(fn(string $search): array => $this->getChatwootContactsSearchResults($search))
                                    ->getOptionLabelUsing(fn($value): ?string => $this->getChatwootContactLabel($value))
                                    ->live()
                                    ->placeholder(fn() => Blade::render('components.dashboard-contact-select-option', ['contact' => null]))
                                    ->allowHtml()
                                    ->native(false)
                                    ->required()
                                    ->afterStateUpdated(function (Set $set, $state) {
                                        if ($state == null) {
                                            $set('chatwootConversationId', null);
                                        } else {
                                            // Fetch the most recent conversation for the selected contact
                                            $conversation = ChatwootConversation::where('contact_id', $state)
                                                ->orderBy('last_activity_at', 'desc')
                                                ->first();
                                            if ($conversation) {
                                                $set('chatwootConversationId', $conversation->id);
                                            }
                                        }
                                    }),

                                Select::make('chatwootConversationId')
                                    ->disabled(session('isEmbeddedMode', false))
                                    ->label('Rozmowa')
                                    ->options(fn() => $this->getChatwootConversationsOptions($contactId ?? null))
                                    ->allowHtml()
                                    ->live()
                                    ->placeholder(fn() => Blade::render('components.dashboard-conversation-select-option', ['conversation' => null]))
                                    ->native(false)
                                    ->required(),
                            ];
                        })
                ])
                ->color('gray'),
        ];
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
