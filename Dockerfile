FROM dunglas/frankenphp

RUN install-php-extensions \
    pcntl pgsql pdo_pgsql zip intl opcache brotli

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction --prefer-dist --optimize-autoloader --no-dev

COPY . /app

ENTRYPOINT ["php", "artisan", "octane:frankenphp"]