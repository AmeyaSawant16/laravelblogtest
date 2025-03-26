FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
    nginx \
    supervisor \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    libzip-dev \
    zip \
    unzip \
    bash \
    nodejs \
    npm \
    mysql-client

RUN docker-php-ext-configure gd --with-jpeg && \
    docker-php-ext-install gd pdo pdo_mysql zip bcmath opcache

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader
# RUN echo "REDIS_HOST=127.0.0.1" > /tmp/.env \
#     && echo "CACHE_DRIVER=array" >> /tmp/.env \
#     && echo "QUEUE_CONNECTION=sync" >> /tmp/.env \
#     && cp /tmp/.env .env \
#     && composer install --no-dev --optimize-autoloader \
#     && rm -f /tmp/.env

RUN npm install && npm run build

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

RUN composer require laravel/pail --dev


COPY docker/supervisor.conf /etc/supervisord.conf

EXPOSE 80

CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && supervisord -c /etc/supervisord.conf"]
