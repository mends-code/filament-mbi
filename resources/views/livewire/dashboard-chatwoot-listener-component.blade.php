<div>
    @script
        <script>
            window.addEventListener(
                'message',
                function(event) {
                    $wire.dispatch('set-dashboard-context', {
                        context: event.data
                    });
                });
            $wire.on('get-dashboard-context', () => {
                window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
            });
        </script>
    @endscript
</div>
