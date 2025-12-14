#!/usr/bin/env bash
set -euo pipefail

echo "== CI bootstrap =="

# 1) Composer auth (evita rate limit / bloqueos)
if [[ -n "${COMPOSER_GITHUB_TOKEN:-}" ]]; then
  composer config -g github-oauth.github.com "${COMPOSER_GITHUB_TOKEN}"
fi

# 2) Instalar dependencias
composer install --no-interaction --prefer-dist --no-progress

# 3) Preparar .env para CI (seguro y sin dependencias externas)
#    IMPORTANTE: en CI no queremos cache en BD ni sqlite en archivo.
if [[ ! -f .env ]]; then
  cp .env.example .env
fi

# Generar APP_KEY en .env si falta (Laravel lo gestiona)
php artisan key:generate --force

# 4) Crear .env.testing SIEMPRE con sqlite en memoria + cache array
TEST_KEY="$(php artisan key:generate --show)"

cat > .env.testing <<EOF
APP_NAME=KitchenManager
APP_ENV=testing
APP_DEBUG=true
APP_KEY=${TEST_KEY}

CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync

DB_CONNECTION=sqlite
DB_DATABASE=:memory:
EOF

# 5) Limpiar caches en el entorno de testing (para no usar el .env normal)
php artisan optimize:clear --env=testing

# 6) Ejecutar tests en testing
php artisan test --env=testing
