# Use the official FrankenPHP image
FROM dunglas/frankenphp

# Copy the PHP extension installer for any additional extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install any necessary PHP extensions not included in the base image
RUN install-php-extensions @composer pgsql pdo_pgsql zip intl opcache brotli pcntl

# Copy Composer from the official Composer image
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy your Laravel application code to the container
COPY . /app

# Set the working directory to your application
WORKDIR /app

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader

# Ensure files are owned by the web server user
RUN chown -R www-data:www-data /app

# Set the entrypoint to use Laravel Octane with FrankenPHP
ENTRYPOINT ["php", "artisan", "octane:frankenphp", "--host=0.0.0.0", "--port=8000"]

# Expose the port the app runs on
EXPOSE 8000
