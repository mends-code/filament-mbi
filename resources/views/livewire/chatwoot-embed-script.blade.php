<!-- File: resources/views/livewire/chatwoot-embed-script.blade.php -->

<script>
    // Function to set a cookie
    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        const expires = "; expires=" + date.toUTCString();
        document.cookie = name + "=" + (value || "") + expires + "; path=/";
    }

    // Event listener to receive and store Chatwoot context
    window.addEventListener("message", function (event) {
        try {
            const eventData = JSON.parse(event.data);
            if (eventData.event === 'appContext') {
                setCookie('chatwootContext', JSON.stringify(eventData.data), 7); // Store context for 7 days
                setCookie('embedMode', 'true', 7); // Set embed mode for 7 days
            }
        } catch (e) {
            console.error("Invalid JSON received from Chatwoot:", e);
        }
    });

    // Request Chatwoot context
    window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
</script>
