<div>
    @script
        <script>
            window.addEventListener('message', function(event) {
                $wire.dispatch('push-chatwoot-context', {
                    context: event.data
                });
                console.log('push-chatwoot-context');
            });
            $wire.on('get-chatwoot-context', () => {
                console.log('get-chatwoot-context');
                window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
            });
            window.addEventListener(
                'load',
                function() {
                    $wire.dispatch('reset-chatwoot-payload');
                    console.log('reset-chatwoot-payload dispatched due to page load');
                }, {
                    once: true
                },
            );
        </script>
    @endscript
</div>
