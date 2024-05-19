    <script>
        window.addEventListener('DOMContentLoaded', (event) => {
            // Listener for context event from Chatwoot
            window.addEventListener('chatwoot:conversation', function (e) {
                const contextData = e.detail;

                // Send context data to the RESTful API route
                fetch('{{ url("/api/chatwoot-context") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(contextData)
                })
                .then(response => response.json())
                .then(data => console.log('Success:', data))
                .catch((error) => console.error('Error:', error));
            });
        });
    </script>
