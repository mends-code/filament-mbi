<div>
    <div wire:ignore>
        <button id="fetch-conversation-data">Fetch Conversation Data</button>
    </div>

    <div>
        <h3>Conversation Data:</h3>
        <pre>{{ json_encode($conversationData, JSON_PRETTY_PRINT) }}</pre>
    </div>

    <script>
        // Utility function to update URL query parameters
        function updateQueryParam(param, value) {
            const url = new URL(window.location.href);
            url.searchParams.set(param, value);
            window.history.replaceState({}, '', url);
        }

        // Listen for the message event
        window.addEventListener("message", function(event) {
            // Assuming event.data contains the conversation object
            if (event.data && event.data.conversation && event.data.conversation.id) {
                const conversationId = event.data.conversation.id;
                // Update the URL with the conversation ID as a query parameter
                updateQueryParam('filters[chatwootConversationId]', conversationId);
            }
        });

        // Trigger the parent window to send the conversation data
        window.addEventListener('load', function() {
            window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
        });
    </script>
</div>
