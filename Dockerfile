FROM php:8.2-fpm

# ------------------- system -------------------
RUN apt-get update && apt-get install -y \
    git curl libpng-dev libonig-dev libxml2-dev \
    zip unzip libzip-dev libpq-dev \
    cron supervisor nano \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ------------------- php -------------------
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd zip
RUN pecl install redis && docker-php-ext-enable redis

# ------------------- composer -------------------
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# ------------------- app -------------------
WORKDIR /var/www

# zależności – zachowaj cache
COPY --chown=www-data:www-data composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# kod aplikacji
COPY --chown=www-data:www-data . .

# ------------------- supervisor & cron -------------------
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/cron/laravel-scheduler /etc/cron.d/laravel-scheduler
RUN chmod 0644 /etc/cron.d/laravel-scheduler && crontab /etc/cron.d/laravel-scheduler

# ------------------- start -------------------
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

# tylko wymagane katalogi muszą być zapisywalne
RUN chmod -R 755 storage bootstrap/cache

EXPOSE 9000
CMD ["/start.sh"]
