<x-filament::page>
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

                // Store data immediately and fetch once confirmed stored
                fetch('/api/chatwoot/app-context/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(eventData)
                })
                .then(response => response.json())
                .then(data => {
                    console.log('Success:', data);
                    fetchData(); // Fetch data right after it's confirmed stored
                })
                .catch(error => console.error('Error:', error));
            });

            function fetchData() {
                fetch('/api/chatwoot/app-context/fetch')
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.status === 'success') {
                            document.getElementById('data-display').innerHTML = JSON.stringify(data.data, null, 2);
                        } else {
                            console.error('Failed to fetch data');
                        }
                    })
                    .catch(error => console.error('Error fetching data:', error));
            }
        </script>
    </head>
    <h1>Received Data from Chatwoot</h1>
    <pre id="data-display">Waiting for data...</pre>
</x-filament::page>
