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

# Disable Event and Worker MPMs, enable Prefork MPM
RUN a2dismod mpm_event mpm_worker && a2enmod mpm_prefork

# Install PHP extensions
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/local/pgsql
RUN docker-php-ext-install pdo_pgsql zip intl opcache

# Set Apache DocumentRoot to point to Laravel's public directory
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Copy the application code and configuration files
COPY . /var/www/html
COPY ./docker/config/performance-tuning.conf /etc/apache2/conf-available/performance-tuning.conf
COPY ./docker/config/opcache-recommended.ini /usr/local/etc/php/conf.d/opcache-recommended.ini

# Enable Apache performance tuning configuration
RUN a2enconf performance-tuning

# Set the working directory
WORKDIR /var/www/html

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

# Set permissions for Laravel's storage and cache directories
RUN chown -R www-data:www-data storage bootstrap/cache

# Expose the HTTP port
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
