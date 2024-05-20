@livewireScripts

<div>
    <div wire:ignore>
        <button id="fetch-conversation-data">Fetch Conversation Data</button>
    </div>

    <div>
        <h3>Conversation Data:</h3>
        <pre>{{ json_encode($conversationData, JSON_PRETTY_PRINT) }}</pre>
    </div>

    <script>
        document.getElementById('fetch-conversation-data').addEventListener('click', function() {
            window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
        });
    </script>
</div>
