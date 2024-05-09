# Use PHP with Apache as the base image
FROM php:8.2-apache as web

# Install Additional System Dependencies
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    libpq-dev \
    libicu-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite for URL rewriting
RUN a2enmod rewrite

# Install PHP extensions

RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql

RUN docker-php-ext-install pdo_pgsql zip intl opcache

# Set Apache DocumentRoot to point to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Update Apache configuration files to use the new DocumentRoot
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy the application code
COPY . /var/www/html

# Set the working directory
WORKDIR /var/www/html

RUN echo '\
<IfModule mpm_prefork_module> \
    StartServers 5 \
    MinSpareServers 5 \
    MaxSpareServers 10 \
    MaxRequestWorkers 150 \
    MaxConnectionsPerChild 3000 \
</IfModule> \
<IfModule mod_rewrite.c> \
    KeepAlive On \
    MaxKeepAliveRequests 100 \
    KeepAliveTimeout 5 \
</IfModule>' >> /etc/apache2/conf-available/performance-tuning.conf \
 && a2enconf performance-tuning

RUN { \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.interned_strings_buffer=16'; \
        echo 'opcache.max_accelerated_files=7963'; \
        echo 'opcache.revalidate_freq=2'; \
        echo 'opcache.fast_shutdown=1'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Install project dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Set permissions for Laravel's storage and cache directories
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose the HTTP port
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
