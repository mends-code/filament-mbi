FROM dunglas/frankenphp

# Copy the PHP extension installer
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install necessary PHP extensions
RUN install-php-extensions @composer pdo_pgsql pgsql opcache brotli mbstring openssl

# Copy Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy your Laravel application code to the container
COPY . /app

# Set the working directory to your application
WORKDIR /app

# Update permissions
RUN chown -R www-data:www-data /app

# Install Composer dependencies with verbose output
RUN composer install --no-dev --optimize-autoloader -vvv

# Set the entrypoint to use Laravel Octane with FrankenPHP
ENTRYPOINT ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=8000"]

# Expose the port the app runs on
EXPOSE 8000
