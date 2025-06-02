FROM php:8.2-fpm

# Zainstaluj zależności systemowe
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    libzip-dev \
    libpq-dev \
    cron \
    supervisor

# Wyczyść cache apt
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Zainstaluj rozszerzenia PHP
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip

# Zainstaluj Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ustaw katalog roboczy
WORKDIR /var/www

# Skopiuj pliki composer
COPY composer.json composer.lock ./

# Zainstaluj zależności PHP
RUN composer install --no-dev --optimize-autoloader --no-scripts

# Skopiuj kod aplikacji
COPY . .

# Ustaw uprawnienia
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache

# Skopiuj konfigurację supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Skopiuj konfigurację cron
COPY docker/cron/laravel-scheduler /etc/cron.d/laravel-scheduler
RUN chmod 0644 /etc/cron.d/laravel-scheduler \
    && crontab /etc/cron.d/laravel-scheduler

# Skopiuj skrypt startowy
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 9000

CMD ["/start.sh"]