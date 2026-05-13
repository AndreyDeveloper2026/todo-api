FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev curl \
    && docker-php-ext-install pdo pdo_mysql zip

# Redis extension
RUN pecl install redis \
    && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# сначала только зависимости (кеш слой)
COPY composer.json composer.lock ./

RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# потом весь код
COPY . .

# теперь artisan доступен
RUN php artisan package:discover --ansi || true

CMD ["php-fpm"]
