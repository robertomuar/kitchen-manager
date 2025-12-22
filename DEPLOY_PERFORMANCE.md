# Deploy performance (Nginx)

Este proyecto no incluye configuración de Nginx/Docker en el repo. Usa este ejemplo como base para una instalación Windows + Docker en producción.

## Nginx recomendado

```nginx
server {
    listen 80;
    server_name example.com;

    # Redirección a HTTPS (si tienes TLS configurado)
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl;
    server_name example.com;

    # Ajusta las rutas del certificado TLS
    ssl_certificate     /etc/ssl/certs/example.com.crt;
    ssl_certificate_key /etc/ssl/private/example.com.key;

    root /var/www/kitchen-manager/public;
    index index.php;

    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;

    # Brotli (si está disponible en tu build de Nginx)
    # brotli on;
    # brotli_comp_level 5;
    # brotli_types text/plain text/css application/json application/javascript application/xml+rss application/atom+xml image/svg+xml;

    location /build/assets/ {
        access_log off;
        expires 1y;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    location ~* \.(?:js|css|woff2?|ttf|eot|svg|gif|png|jpg|jpeg)$ {
        access_log off;
        expires 7d;
        add_header Cache-Control "public";
        try_files $uri =404;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
        add_header Cache-Control "no-store, no-cache, must-revalidate, max-age=0";
    }

    # Rutas privadas (no cache)
    location ~ ^/(dashboard|admin|profile|password|login|register|email) {
        add_header Cache-Control "no-store, no-cache, must-revalidate, max-age=0";
    }

    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_pass php-fpm:9000;
        fastcgi_buffering on;
    }
}
```

## Verificación con curl

```bash
# Assets versionados deben ser cacheables e immutable
curl -I http://example.com/build/assets/app.css

# HTML dinámico debe ser no-cache
curl -I http://example.com/

# Verificar gzip
curl -I -H "Accept-Encoding: gzip" http://example.com/build/assets/app.js

# Verificar brotli (si está habilitado)
curl -I -H "Accept-Encoding: br" http://example.com/build/assets/app.js

# Verificar redirección http->https (si aplica)
curl -I http://example.com/
```
