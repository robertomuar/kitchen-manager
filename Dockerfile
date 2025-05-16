# 1) Partimos de php-fpm
FROM php:8.2-fpm

# 2) Instalamos deps de sistema
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      git zip unzip libzip-dev \
 && docker-php-ext-install pdo_mysql \
 && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www

# 3) Copiamos TODO el código (salvo lo que ignoramos)
COPY . .

# 4) Instalamos Composer
RUN curl -sS https://getcomposer.org/installer | php \
      -- --install-dir=/usr/local/bin --filename=composer

# 5) Ejecutamos Composer *después* de copiar artisan
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 6) Ajustamos permisos
RUN mkdir -p storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

# 7) Opcional: expone el puerto que php-fpm escucha
EXPOSE 9000

# 8) Arranca php-fpm (puedes omitir si lo gestionas desde docker-compose)
CMD ["php-fpm"]
