FROM php:8.4-cli

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    git unzip libzip-dev \
    && docker-php-ext-install pdo pdo_mysql zip pcntl

# Redis extension (важно для queue)
RUN pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# зависимости
COPY composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --no-scripts

# код
COPY . .

# worker (PRODUCTION WAY)
CMD ["php", "artisan", "queue:work", "--sleep=3", "--tries=3", "--timeout=90"]
