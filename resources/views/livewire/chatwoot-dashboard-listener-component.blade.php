<div>
    @script
        <script>
            window.addEventListener(
                'message',
                function(event) {
                    $wire.dispatch('set-chatwoot-context', {
                        context: event.data
                    });
                });
            $wire.on('get-chatwoot-context', () => {
                window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
            });
            window.addEventListener(
                'load',
                function() {
                    $wire.dispatch('reset-cached-filters');
                }, {
                    once: true
                });
        </script>
    @endscript
</div>
