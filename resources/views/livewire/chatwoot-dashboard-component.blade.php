<div>
    <div wire:ignore>
        <button id="fetch-conversation-data">Fetch Conversation Data</button>
    </div>

    <div>
        <h3>Conversation Data:</h3>
        <pre>{{ json_encode($conversationData, JSON_PRETTY_PRINT) }}</pre>
    </div>

    <script>
        function isJSONValid(data) {
            try {
                JSON.parse(data);
                return true;
            } catch (e) {
                return false;
            }
        }

        window.addEventListener("message", function(event) {
            if (!isJSONValid(event.data)) {
                return;
            }

            const eventData = JSON.parse(event.data);

            $wire.dispatch('handleConversationData', eventData.appContext);
        });

        document.getElementById('fetch-conversation-data').addEventListener('click', function() {
            window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
        });
    </script>
</div>
