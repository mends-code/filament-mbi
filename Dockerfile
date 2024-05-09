# Use Swoole PHP Docker image
FROM phpswoole/swoole:5.1-php8.3

# Install system dependencies
RUN apt-get update && apt-get install -y libzip-dev zip libpq-dev libicu-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Install additional PHP extensions
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pgsql pdo_pgsql zip intl

# Copy application code
COPY . /var/www/html

# Set the working directory to the Laravel application
WORKDIR /var/www/html

# Install Composer and project dependencies
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Ensure proper permissions on Laravel directories
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose port 80 and 443
EXPOSE 80

# Use a custom script to start Swoole server
COPY ./start-swoole.sh /usr/local/bin/start-swoole
RUN chmod +x /usr/local/bin/start-swoole

CMD ["start-swoole"]
