# Deploy performance (Windows + Docker + Nginx)

Este documento contiene configuraciones recomendadas para mejorar la compresión y el caching de assets en producción. Copia/pega en tu `nginx.conf` o `default.conf` dentro del contenedor.

## 1) Compresión (gzip)

```nginx
gzip on;
gzip_disable "msie6";

gzip_vary on;
gzip_proxied any;
gzip_comp_level 6;
gzip_min_length 1024;
gzip_buffers 16 8k;

gzip_types
    application/javascript
    application/json
    application/manifest+json
    application/xml
    font/otf
    font/ttf
    font/woff
    font/woff2
    image/svg+xml
    text/css
    text/plain
    text/xml;
```

> Brotli no se fuerza aquí: úsalo solo si tu imagen Docker lo soporta.

## 2) Cache headers para assets versionados de Vite

```nginx
location ~* \.(?:css|js|mjs|map|woff2?|ttf|otf|eot|svg|png|jpg|jpeg|gif|webp|avif)$ {
    expires 1y;
    add_header Cache-Control "public, max-age=31536000, immutable" always;
    try_files $uri =404;
}
```

## 3) No-cache para HTML privado

```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
    add_header Cache-Control "no-store, no-cache, must-revalidate, proxy-revalidate" always;
    add_header Pragma "no-cache" always;
}
```

## 4) Headers mínimos de seguridad (sin romper Laravel)

```nginx
add_header X-Content-Type-Options "nosniff" always;
add_header Referrer-Policy "strict-origin-when-cross-origin" always;
add_header X-Frame-Options "SAMEORIGIN" always;
```

## 5) Ejemplo de server block (resumen)

```nginx
server {
    listen 80;
    server_name example.com;

    root /var/www/html/public;
    index index.php;

    include /etc/nginx/mime.types;

    gzip on;
    gzip_disable "msie6";
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_min_length 1024;
    gzip_buffers 16 8k;
    gzip_types application/javascript application/json application/manifest+json application/xml font/otf font/ttf font/woff font/woff2 image/svg+xml text/css text/plain text/xml;

    location ~* \.(?:css|js|mjs|map|woff2?|ttf|otf|eot|svg|png|jpg|jpeg|gif|webp|avif)$ {
        expires 1y;
        add_header Cache-Control "public, max-age=31536000, immutable" always;
        try_files $uri =404;
    }

    location / {
        try_files $uri $uri/ /index.php?$query_string;
        add_header Cache-Control "no-store, no-cache, must-revalidate, proxy-revalidate" always;
        add_header Pragma "no-cache" always;
    }

    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass app:9000;
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param PATH_INFO $fastcgi_path_info;
    }

    add_header X-Content-Type-Options "nosniff" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
}
```

## 6) Comprobaciones rápidas (curl)

```bash
# Verificar Cache-Control en un asset Vite
curl -I https://tu-dominio.com/build/assets/app-XXXXXXXX.js

# Verificar gzip (Content-Encoding: gzip)
curl -I -H "Accept-Encoding: gzip" https://tu-dominio.com/build/assets/app-XXXXXXXX.js
```

> En Windows puedes usar Git Bash, WSL o PowerShell (con `curl` moderno) para ejecutar estos comandos.
