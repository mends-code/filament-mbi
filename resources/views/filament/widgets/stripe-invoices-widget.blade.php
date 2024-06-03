<x-filament-widgets::widget>
    @if (isset($this->table))
        {{ $this->table }}
    @endif
    <x-filament-actions::modals />
</x-filament-widgets::widget>
