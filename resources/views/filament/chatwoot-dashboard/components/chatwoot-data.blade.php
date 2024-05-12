<x-filament::page>
    <script>
        function requestDataFromChatwoot() {
            // Send a message to the parent window requesting data
            window.parent.postMessage({ event: 'chatwoot-dashboard-app:fetch-info' }, '*');
        }

        // Listen for messages from the parent window
        window.addEventListener('message', function (event) {
            if (event.origin === "https://chat.mends.eu") { // Ensure this matches your Chatwoot instance URL
                console.log('Data received from Chatwoot:', event.data);
                // Process and display the data here or send it to the server
            }
        });

        // Example button to trigger data request
        function addRequestButton() {
            const button = document.createElement('button');
            button.textContent = 'Fetch Data from Chatwoot';
            button.onclick = requestDataFromChatwoot;
            document.body.appendChild(button);
        }

        window.onload = addRequestButton;
    </script>
</x-filament::page>
