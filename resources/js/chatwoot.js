// Function to validate JSON
function isJSONValid(str) {
    try {
        JSON.parse(str);
    } catch (e) {
        console.error('Invalid JSON string:', str);
        return false;
    }
    return true;
}

// Event listener for receiving messages from Chatwoot
window.addEventListener("message", function (event) {
    console.log('Received message event:', event);

    // Check if the message is from Chatwoot's expected origin
    if (event.origin !== "https://your-chatwoot-origin.com") {
        console.warn('Message origin not recognized:', event.origin);
        return;
    }

    // Validate and parse JSON data
    if (isJSONValid(event.data)) {
        const eventData = JSON.parse(event.data);
        console.log('Valid JSON received:', eventData);
        handleChatwootEvent(eventData);
    } else {
        console.warn('Received invalid JSON data:', event.data);
    }
});

// Function to handle Chatwoot event data
function handleChatwootEvent(data) {
    console.log('Processing Chatwoot event data:', data);
    // Process the event data as needed
}

// Initialize Laravel Echo for real-time WebSocket communication
const echo = new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_REVERB_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'], // Add more transports if needed
});

// Log the Echo connection details
console.log('Initializing Echo with config:', {
    broadcaster: 'pusher',
    key: process.env.MIX_REVERB_APP_KEY,
    wsHost: window.location.hostname,
    wsPort: 6001,
    forceTLS: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
});

// Listen for Chatwoot events in real-time
echo.channel('chatwoot')
    .listen('ChatwootEvent', (e) => {
        console.log('Received WebSocket event data:', e.data);
        handleChatwootEvent(e.data);
    });

// Log the successful setup
console.log('Chatwoot event listener and WebSocket setup complete.');
