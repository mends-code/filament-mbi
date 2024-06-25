<x-filament-widgets::widget>
    <div @if ($pollingInterval = $this->getPollingInterval()) wire:poll.{{ $pollingInterval }} @endif>
        @if (isset($this->table))
            {{ $this->table }}
        @else
            <x-filament::loading-section />
        @endif
    </div>
    <x-filament-actions::modals />
</x-filament-widgets::widget>
