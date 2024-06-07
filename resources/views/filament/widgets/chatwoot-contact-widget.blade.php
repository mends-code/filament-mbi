<x-filament-widgets::widget wire:poll.15s>
    @if (isset($this->infolist))
        {{ $this->infolist }}
    @else
        <x-filament::loading-section />
    @endif
    <x-filament-actions::modals />
</x-filament-widgets::widget>
