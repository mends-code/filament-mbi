<x-filament-widgets::widget wire:poll.15s>
    @if (isset($this->table))
        {{ $this->table }}
    @endif
    <x-filament-actions::modals />
</x-filament-widgets::widget>
