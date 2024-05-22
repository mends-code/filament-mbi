<div>
    @script
        <script>
            window.addEventListener('message', function(event) {
                $wire.dispatch('update-chatwoot-context', {
                    context: event.data
                });
                console.log('update-chatwoot-context');
            });
            $wire.on('get-chatwoot-context', () => {
                console.log('get-chatwoot-context');
                window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
            });
            window.addEventListener('load', function() {
                $wire.dispatch('reset-chatwoot-context');
                console.log('reset-chatwoot-context dispatched due to page load');
            });
        </script>
    @endscript
</div>
