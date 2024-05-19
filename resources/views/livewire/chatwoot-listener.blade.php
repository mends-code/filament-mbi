<script>
    console.log('test');
    window.addEventListener("message", function(event) {
        const eventData = JSON.parse(event.data);
        console.log(event.data);
    });
    {{ $this->emit('message') }}
</script>
<div>
    @if ($chatwootData)
        <pre>{{ json_encode($chatwootData, JSON_PRETTY_PRINT) }}</pre>
    @else
        <p>No data received yet.</p>
    @endif
</div>
