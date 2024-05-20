<div>
    <script>
    window.addEventListener("message", function(event) {
        console.log(JSON.stringify(event.data));
        fetch('{{ route('store-chatwoot-data') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(event.data)
        }).then(response => response.json())
          .then(data => console.log('Success:', data))
          .catch((error) => console.error('Error:', error));
    });
    </script>
</div>
