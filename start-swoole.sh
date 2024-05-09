#!/bin/bash

# Start Swoole HTTP server using Laravel Artisan
echo "Starting Swoole HTTP server..."
php artisan octane:start --host=127.0.0.1 --port=80

# You can customize the command with additional options like host and port
# Example: php artisan swoole:http start --host=127.0.0.1 --port=8080
