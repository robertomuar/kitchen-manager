#!/usr/bin/env bash
set -e

echo "== CI bootstrap =="

# 1) Composer auth (evita rate limit / bloqueos)
if [ -n "$COMPOSER_GITHUB_TOKEN" ]; then
  composer config -g github-oauth.github.com "$COMPOSER_GITHUB_TOKEN"
fi

# 2) Instalar dependencias
composer install --no-interaction --prefer-dist --no-progress

# 3) .env base
cp -n .env.example .env

# 4) APP_KEY normal
php artisan key:generate --force

# 5) Forzar entorno de testing independiente
TEST_KEY=$(php artisan key:generate --show)

cat > .env.testing <<EOF
APP_NAME=KitchenManager
APP_ENV=testing
APP_DEBUG=true
APP_KEY=$TEST_KEY

CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync

DB_CONNECTION=sqlite
DB_DATABASE=:memory:
EOF

# 6) Limpiar caches
php artisan optimize:clear

# 7) Ejecutar tests
php artisan test --env=testing
