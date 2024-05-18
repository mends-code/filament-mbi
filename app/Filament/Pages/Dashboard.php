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
use Illuminate\Support\HtmlString;

class Dashboard extends BaseDashboard
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
                            ->options($this->getChatwootContactsOptions())
                            ->reactive()
                            ->allowHtml()
                            ->native(false)
                            ->afterStateUpdated(function () {
                                $this->filters['chatwootConversationId'] = null;
                            }),

                        Select::make('chatwootConversationId')
                            ->label('Rozmowa')
                            ->searchable()
                            ->options(function () {
                                return $this->getChatwootConversationsOptions($this->filters['chatwootContactId']);
                            })
                            ->reactive()
                            ->allowHtml()
                            ->native(false)
                            ->disabled(fn() => empty ($this->filters['chatwootContactId'])),
                    ])
                    ->columns(2),
            ]);
    }

    protected function getChatwootContactsOptions(): array
    {
        return ChatwootContact::all()->mapWithKeys(function ($contact) {
            $html = Blade::render('components.dashboard-contact-select-option', ['contact' => $contact]);
            return [$contact->id => $html];
        })->toArray();
    }

    protected function getChatwootConversationsOptions($contactId): array
    {
        $contactId = $this->filters['chatwootContactId'];

        if (!$contactId) {
            return [];
        }

        return ChatwootConversation::where('contact_id', $contactId)->get()->mapWithKeys(function ($conversation) {
            $html = Blade::render('components.dashboard-conversation-select-option', ['conversation' => $conversation]);
            return [$conversation->id => $html];
        })->toArray();
    }
}
