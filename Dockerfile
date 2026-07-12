# ── Etapa 1: compilar los assets de frontend (CSS/JS) con Node ──
FROM node:20-alpine AS assets

WORKDIR /app
COPY package*.json ./
RUN npm install
COPY . .
RUN npm run build

# ── Etapa 2: la app de PHP en sí ──
FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip zip libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

# Copia los assets ya compilados desde la etapa anterior
COPY --from=assets /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN chmod -R 775 storage bootstrap/cache

EXPOSE 10000

CMD php artisan config:clear && php artisan serve --host=0.0.0.0 --port=$PORT
