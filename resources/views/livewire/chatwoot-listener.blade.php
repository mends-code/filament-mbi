<!-- resources/views/livewire/chatwoot-listener.blade.php -->

<div>
    <h1>Chatwoot Event Data</h1>
</div>

<script>
    console.log('test');
    window.addEventListener("message", function(event) {

        const eventData = JSON.parse(event.data);
        console.log(event.data);
    });

    // Request data from Chatwoot
    window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
</script>
