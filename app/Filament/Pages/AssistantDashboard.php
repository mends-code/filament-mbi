<?php


namespace App\Filament\Pages;

use App\Models\ChatwootContact;
use App\Models\ChatwootConversation;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Illuminate\Support\Facades\Blade;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use League\Csv\Serializer\CastToInt;
use Session;

class AssistantDashboard extends BaseDashboard
{
    use HasFiltersAction, HasFiltersForm;

    protected static ?string $navigationLabel = "Panel Asystenta";
    protected static ?string $title = "Panel Asystenta";
    protected static ?string $navigationIcon = "heroicon-o-hand-raised";

    protected static function contactPlaceholder()
    {
        return Blade::render('components.dashboard-contact-select-option', ['contact' => null]);
    }

    protected static function conversationPlaceholder()
    {
        return Blade::render('components.dashboard-conversation-select-option', ['conversation' => null]);
    }

    protected function getHeaderActions(): array
    {
        $isEmbeddedMode = false;

        return [
            Action::make('headerActionPrimary'),
            Action::make('headerActionSecondary')->color('gray'),
        ];
    }

    public function filtersForm(Form $form): Form
    {

        $chatwootData = Session::get('chatwoot_data', []);

        $isEmbeddedMode = false;
        $this->filters['chatwootContactId'] = intval($chatwootData['data']['contact']['id']) ?? null;

        return $form
            ->schema([
                Section::make('serviceSubjectContext')
                    ->heading('Kontekst Obsługi Pacjenta')
                    ->schema(function (Get $get, Set $set) use ($isEmbeddedMode) {
                        return [
                            Select::make('chatwootContactId')
                                ->disabled($isEmbeddedMode)
                                ->label('Kontakt')
                                ->searchable()
                                ->getSearchResultsUsing(fn(string $search): array => $this->getChatwootContactsSearchResults($search))
                                ->getOptionLabelUsing(fn($value): ?string => $this->getChatwootContactLabel($value))
                                ->live()
                                ->placeholder($this->contactPlaceholder())
                                ->allowHtml()
                                ->native(false)
                                ->required()
                                ->default($get('chatwootContactId'))
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
                                ->disabled($isEmbeddedMode)
                                ->label('Rozmowa')
                                ->options(fn() => $this->getChatwootConversationsOptions($get('chatwootContactId') ?? null))
                                ->allowHtml()
                                ->live()
                                ->placeholder($this->conversationPlaceholder())
                                ->native(false)
                                ->required()
                                ->default($get('chatwootConversationId')),
                        ];
                    })
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
