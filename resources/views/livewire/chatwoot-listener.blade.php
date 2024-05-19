<!-- resources/views/livewire/chatwoot-listener.blade.php -->

<div>
    <h1>Chatwoot Event Data</h1>
</div>

<script>
    window.addEventListener("message", function(event) {
        if (event.data && typeof event.data === 'string' && event.data.includes(
                'chatwoot-dashboard-app:fetch-info')) {
            Livewire.emit('chatwootEventReceived', JSON.parse(event.data));
            console.log(event.data);
        }
    });

    // Request data from Chatwoot
    window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
</script>
