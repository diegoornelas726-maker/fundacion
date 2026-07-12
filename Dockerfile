FROM php:8.2-cli

# Instala dependencias del sistema y extensiones de PHP necesarias
RUN apt-get update && apt-get install -y \
    git unzip zip libpq-dev libzip-dev \
    && docker-php-ext-install pdo pdo_pgsql zip

# Instala Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copia todo el proyecto al contenedor
COPY . .

# Instala las dependencias de PHP (sin las de desarrollo)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Da permisos de escritura a las carpetas que Laravel necesita
RUN chmod -R 775 storage bootstrap/cache

# Render define la variable PORT automáticamente
EXPOSE 10000

CMD php artisan config:clear && php artisan serve --host=0.0.0.0 --port=$PORT
