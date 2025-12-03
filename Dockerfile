FROM webdevops/php-nginx:8.2

WORKDIR /app

ENV WEB_DOCUMENT_ROOT=/app/public

COPY . /app

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/* \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && composer install --no-interaction --prefer-dist \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

