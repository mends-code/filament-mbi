<!-- resources/views/livewire/chatwoot-embed-script.blade.php -->

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Listener for Chatwoot context event
    window.addEventListener('message', function (event) {
        if (typeof event.data === 'string' && event.data.startsWith('chatwoot-dashboard-app:context')) {
            const appContext = JSON.parse(event.data.replace('chatwoot-dashboard-app:context', ''));
            // Send app context to the backend
            fetch('/api/chatwoot/context', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(appContext)
            });
        }
    });

    // Request app context from Chatwoot
    window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
});
</script>
