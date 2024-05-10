FROM dunglas/frankenphp
 
RUN install-php-extensions \
    pcntl pgsql pdo_pgsql zip intl opcache brotli
 
COPY . /app
 
ENTRYPOINT ["php", "artisan", "octane:frankenphp"]