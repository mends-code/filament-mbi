FROM dunglas/frankenphp

# Install system packages required for Composer and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

# Use the installer script for PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install PHP extensions
RUN install-php-extensions pcntl pgsql pdo_pgsql zip intl opcache brotli

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application code
COPY . /app

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --working-dir=/app

# Set the entry point to start FrankenPHP with Octane
ENTRYPOINT ["php", "artisan", "octane:frankenphp"]
