<div>
    @script
        <script>
            window.addEventListener('message', function(event) {
                console.log(event.data);
                // Assuming event.data.payload is a string for simplicity
                $wire.dispatch('updateChatwootContext', {
                    context: event.data
                });
            });
            $wire.on('getChatwootContext', () => {
                window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*')
            });
        </script>
    @endscript
</div>
