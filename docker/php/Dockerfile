FROM php:8.4-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev curl \
    && docker-php-ext-install pdo pdo_mysql zip pcntl

# Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# зависимости сначала (кэш слой)
COPY composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# код
COPY . .

# Laravel permissions fix (production-safe)
RUN chmod -R 775 storage bootstrap/cache

CMD ["php-fpm"]
