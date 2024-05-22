<div>
    @script
        <script>
            const cookieName = 'chatwootSession';

            // Function to get a cookie value by name
            function getCookie(name) {
                const value = `; ${document.cookie}`;
                const parts = value.split(`; ${name}=`);
                if (parts.length === 2) return parts.pop().split(';').shift();
            }

            // Function to set a session cookie
            function setSessionCookie(name, value) {
                document.cookie = `${name}=${value};path=/`;
            }

            // Check if the chatwootPayload cookie is set
            if (!getCookie(cookieName)) {
                // If the cookie is not found, dispatch the event to get the Chatwoot context
                $wire.dispatch('set-chatwoot-session');
                console.log('set-chatwoot-session dispatched');

                // Set the session cookie to indicate initialization is done
                setSessionCookie(cookieName, 'true');
            }

            window.addEventListener('message', function(event) {
                $wire.dispatch('update-chatwoot-context', {
                    context: event.data
                });
                console.log('update-chatwoot-context');
            });
            $wire.on('get-chatwoot-context', () => {
                console.log('get-chatwoot-context');
                window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
            });
        </script>
    @endscript
</div>
