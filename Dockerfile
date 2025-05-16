# Dockerfile para PHP-FPM y Laravel
FROM php:8.2-fpm

# Instala extensiones necesarias
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libzip-dev zip unzip git \
 && docker-php-ext-install pdo_mysql zip

WORKDIR /var/www

# Copia composer.lock/composer.json e instala dependencias
COPY composer.json composer.lock /var/www/
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
 && composer install --no-dev --optimize-autoloader --no-interaction

# Copia el resto del código
COPY . /var/www

# Ajusta permisos
RUN chown -R www-data:www-data \
      storage bootstrap/cache

EXPOSE 9000
CMD ["php-fpm"]
