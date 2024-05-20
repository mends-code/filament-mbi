<script>
    document.addEventListener('livewire:load', function() {
        $wire.on('chatwoot-dashboard-fetch-info', (payload) => {
            console.log('Payload received:', payload);
            window.parent.postMessage({
                type: 'chatwoot-dashboard-app:fetch-info',
                data: payload
            }, '*');
        })
    });
</script>
