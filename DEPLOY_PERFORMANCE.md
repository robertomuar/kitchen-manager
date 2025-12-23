# Deploy performance (Nginx)

Este proyecto no incluye configuración de Nginx/Docker en el repo. Usa este ejemplo como base para una instalación Windows + Docker en producción.

## Nginx recomendado

```nginx
# Redirect HTTP -> HTTPS
server {
    listen 80;
    server_name example.com;

    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name example.com;

    root /var/www/kitchen-manager/public;
    index index.php;

    # SSL (configura certificados según tu entorno)
    ssl_certificate /etc/ssl/certs/example.crt;
    ssl_certificate_key /etc/ssl/private/example.key;

    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;

    # Brotli opcional (si está disponible)
    # brotli on;
    # brotli_types text/plain text/css application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;

    location /build/assets/ {
        access_log off;
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    location ~* \.(?:png|jpe?g|webp|svg|woff2?|ttf|ico)$ {
        access_log off;
        expires 30d;
        add_header Cache-Control "public";
        try_files $uri =404;
    }

    # HTML público (cache corto opcional) y privado (no-cache)
    location / {
        try_files $uri $uri/ /index.php?$query_string;
        add_header Cache-Control "no-store, no-cache, must-revalidate, max-age=0";
    }

    location ~ \.(php)$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass php-fpm:9000;
        fastcgi_buffering on;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
        fastcgi_busy_buffers_size 64k;
    }
}
```

## Build de frontend (Vite)

- En producción, asegúrate de ejecutar:

```bash
npm ci
npm run build
```

- No uses el servidor de desarrollo de Vite en producción (no debe aparecer `@vite/client` ni `http://localhost:5173` en el HTML).

## Laravel en producción (TTFB)

- Establece `APP_ENV=production` y `APP_DEBUG=false` en tu entorno.
- Ejecuta en despliegue:

```bash
php artisan optimize
```

## PHP OPcache

En contenedores Docker, añade un archivo `opcache.ini` (por ejemplo en `docker/php/conf.d/opcache.ini`) con valores seguros:

```ini
opcache.enable=1
opcache.enable_cli=0
opcache.memory_consumption=128
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=10000
opcache.validate_timestamps=0
opcache.revalidate_freq=0
opcache.save_comments=1
opcache.fast_shutdown=1
```

## Verificación con curl

```bash
# Assets versionados deben ser cacheables e immutable
curl -I https://example.com/build/assets/app.css

# HTML dinámico debe ser no-cache
curl -I https://example.com/

# Verificar gzip (o brotli si está activo)
curl -I -H "Accept-Encoding: gzip" https://example.com/build/assets/app.js
```
