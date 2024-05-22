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

            // Check if the chatwootSession cookie is set
            if (!getCookie(cookieName)) {
                // If the cookie is not found, dispatch the event to get the Chatwoot context
                $wire.dispatch('set-chatwoot-session');
                console.log('set-chatwoot-session dispatched');

                // Set the session cookie to indicate initialization is done
                setSessionCookie(cookieName, 'true');
            }

            // Event listener for receiving the Chatwoot context message
            window.addEventListener('message', function(event) {
                $wire.dispatch('update-chatwoot-context', {
                    context: event.data
                });
                console.log('update-chatwoot-context');
            });

            // Event listener for get-chatwoot-context
            $wire.on('get-chatwoot-context', () => {
                console.log('get-chatwoot-context');
                window.parent.postMessage('chatwoot-dashboard-app:fetch-info', '*');
            });
            
            // Additional event listener for popstate event (loading new request or similar)
            window.addEventListener('popstate', function () {
                $wire.dispatch('update-chatwoot-payload-dummy');
                console.log('update-chatwoot-payload dispatched due to popstate event');
            });

            // Additional event listener for loading new request (page load, URL change)
            window.addEventListener('load', function () {
                $wire.dispatch('update-chatwoot-payload-dummy');
                console.log('update-chatwoot-payload dispatched due to page load');
            });

            window.addEventListener('popstate', function () {
                $wire.dispatch('update-chatwoot-payload-dummy');
                console.log('update-chatwoot-payload dispatched due to popstate event');
            });

        </script>
    @endscript
</div>
