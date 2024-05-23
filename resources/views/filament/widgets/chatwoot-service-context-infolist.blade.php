<x-filament-widgets::widget>
    @script
        <script>
            window.addEventListener('message', function(event) {
                $wire.dispatch('push-chatwoot-context', {
                    context: event.data
                });
            });
            $wire.on('get-chatwoot-context', () => {
                window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
            });
            window.addEventListener(
                'load',
                function() {
                    $wire.dispatch('reset-chatwoot-context');
                    $wire.dispatch('reset-chatwoot-payload');
                }, {
                    once: true
                },
            );
        </script>
    @endscript
    <x-filament::section>
        {{ $this->infolist }}
    </x-filament::section>
</x-filament-widgets::widget>
