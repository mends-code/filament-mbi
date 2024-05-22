<div>
    @script
        <script>
            // Event listener for receiving the Chatwoot context message
            window.addEventListener('message', function(event) {
                $wire.dispatch('update-chatwoot-context', {
                    context: event.data
                });
                console.log('update-chatwoot-context');
            });

            // Event listener for get-chatwoot-context
            $wire.on('get-chatwoot-context', () => {
                console.log('get-chatwoot-context');
                window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
            });

            // Additional event listener for loading new request (page load, URL change)
            window.addEventListener('load', function() {
                $wire.dispatch('reset-chatwoot-payload');
                console.log('reset-chatwoot-payload dispatched due to page load');
            });
        </script>
    @endscript
</div>
