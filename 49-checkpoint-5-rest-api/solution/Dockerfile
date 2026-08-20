# Checkpoint 5: контейнеризація застосунку (тема 48).
# Навчальний приклад — php -S у контейнері, не продакшн-конфігурація
# (у продакшені був би php-fpm + окремий nginx, тема 28).

FROM php:8.3-cli

RUN apt-get update && apt-get install -y --no-install-recommends unzip git \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --optimize-autoloader

COPY . .

EXPOSE 8000

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
