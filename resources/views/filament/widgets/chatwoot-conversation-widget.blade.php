<x-filament-widgets::widget wire:poll.visible.15s>
    @if (isset($this->infolist))
        {{ $this->infolist }}
    @endif
    <x-filament-actions::modals />
</x-filament-widgets::widget>
