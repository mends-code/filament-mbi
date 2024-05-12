<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use App\Models\ChatwootContact;
use App\Models\User;

class Dashboard extends BaseDashboard
{
    use BaseDashboard\Concerns\HasFiltersForm;

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('chatwootContact')
                            ->options(
                                ChatwootContact::query()
                                    ->get(['id', 'name'])
                                    ->mapWithKeys(function ($item) {
                                        // Use the contact name if not null, otherwise use 'No Name' as a placeholder
                                        return [$item->id => $item->name ?? 'No Name'];
                                    })
                                    ->toArray()
                            )
                            ->preload()
                            ->searchable(),
                        DatePicker::make('startDate')
                            ->maxDate(fn (Get $get) => $get('endDate') ?: now()),
                        DatePicker::make('endDate')
                            ->minDate(fn (Get $get) => $get('startDate') ?: now())
                            ->maxDate(now()),
                    ])
                    ->columns(3),
            ]);
    }
}
