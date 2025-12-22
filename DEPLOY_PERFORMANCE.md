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

    location ~* \.(?:js|css|woff2?|ttf|eot|svg|gif|png|jpg|jpeg)$ {
        access_log off;
        expires 7d;
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
    }
}
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
