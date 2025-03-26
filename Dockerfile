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

ENV APP_NAME=Laravel
ENV APP_ENV=local
ENV APP_KEY=base64:famGtvBDKbelkGus5JPvc3TKdzibEEy1lho/xjpT6xU=
ENV APP_DEBUG=true
ENV APP_URL=http://localhost:5173
ENV DB_CONNECTION=mysql
ENV DB_HOST=localhost
ENV DB_PORT=3306
ENV DB_DATABASE=laravel
ENV DB_USERNAME=root
ENV DB_PASSWORD=
ENV MAIL_MAILER=smtp
ENV MAIL_HOST=sandbox.smtp.mailtrap.io
ENV MAIL_PORT=2525
ENV MAIL_USERNAME=23bf9e27fd641e
ENV MAIL_PASSWORD=3fad8e477b975f
ENV MAIL_FROM_ADDRESS="ameya.sawant82@gmail.com"
ENV MAIL_FROM_NAME="Ameya Sawant"
ENV SCOUT_DRIVER=elasticsearch
ENV ELASTICSEARCH_HOST=es01:9200
ENV ELASTICSEARCH_USER=elastic
ENV ELASTICSEARCH_PASS=ameya123
ENV QUEUE_CONNECTION=sync
ENV CACHE_DRIVER=array

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

COPY docker/supervisor.conf /etc/supervisord.conf

EXPOSE 80

CMD ["sh", "-c", "php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan elasticsearch:posts && supervisord -c /etc/supervisord.conf"]
