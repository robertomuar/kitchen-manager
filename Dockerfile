# Dockerfile para kitchen-manager (PHP 8.2 + FPM + pdo_mysql)

FROM php:8.2-fpm

# 1) Instala dependencias del sistema y extensiones PHP
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      libzip-dev zip unzip git \
 && docker-php-ext-install pdo_mysql zip \
 && rm -rf /var/lib/apt/lists/*

# 2) Define el directorio de trabajo
WORKDIR /var/www

# 3) Copia tu código (el volumen en docker-compose lo sobreescribe en dev)
COPY . .

# 4) Crea carpetas de logs y cache con permisos
RUN mkdir -p storage bootstrap/cache \
 && chown -R www-data:www-data storage bootstrap/cache

# 5) Usuario por defecto (opcional)
USER www-data

# 6) Expone el puerto FPM (9000)
EXPOSE 9000

# 7) El CMD por defecto ya es php-fpm; si quieres puedes reforzarlo:
CMD ["php-fpm"]
