<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script>
        function isJSONValid(str) {
            try {
                JSON.parse(str);
                return true;
            } catch (e) {
                return false;
            }
        }

        window.addEventListener("message", function(event) {
            // Check the origin of the data
            if (event.origin !== "https://chat.mends.eu") {
                console.error("Invalid origin: data received from unauthorized source.");
                return;
            }

            if (!isJSONValid(event.data)) {
                console.error("Invalid JSON data received.");
                return;
            }

            const eventData = JSON.parse(event.data);

            console.log(JSON.stringify(eventData));

            fetch('/filament/api/chatwoot-data-handler', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify(eventData)
                })
                .then(response => response.json())
                .then(data => console.log('Success:', data))
                .catch(error => console.error('Error:', error));
        });
    </script>

</head>
<x-filament-panels::page>
    <h1>Received Data from Chatwoot</h1>
    @if (!empty($data))
        <pre>{{ json_encode($data, JSON_PRETTY_PRINT) }}</pre>
    @else
        <p>No data received yet.</p>
    @endif
</x-filament-panels::page>
