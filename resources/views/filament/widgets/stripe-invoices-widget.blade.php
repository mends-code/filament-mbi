<x-filament-widgets::widget wire:poll.visible.15s>
    @if (isset($this->table))
        {{ $this->table }}
    @endif
    <x-filament-actions::modals />
</x-filament-widgets::widget>
