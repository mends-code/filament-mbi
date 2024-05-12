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
                .then(data => console.log('Success:', JSON.stringify(data)))
                .catch(error => console.error('Error:', JSON.stringify(error)));
        });
    </script>

</head>
<x-filament-panels::page>
</x-filament-panels::page>
