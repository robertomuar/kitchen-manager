# update.ps1
<#
  Script de despliegue para kitchen-manager
  — No borra update.ps1 ni nginx/default.conf
  — Solo limpia public/build antes de compilar assets
#>

Push-Location $PSScriptRoot
Write-Host "→ Directorio de trabajo: $(Get-Location)" -ForegroundColor Cyan
Write-Host "=== Despliegue iniciado: $(Get-Date) ===" -ForegroundColor Green

# 0) Reset duro solo de archivos versionados
Write-Host "`n-> git fetch && git reset --hard origin/main" -ForegroundColor Yellow
git fetch origin
git reset --hard origin/main

# 1) Limpiar solo carpeta de assets compilados
Write-Host "`n-> Limpiando carpeta public/build" -ForegroundColor Yellow
if (Test-Path .\public\build) {
    Remove-Item -Recurse -Force .\public\build
}

# 2) Tirar cambios remotos
Write-Host "`n-> git pull origin main" -ForegroundColor Yellow
git pull origin main

# 3) Compilar frontend en contenedor Node
Write-Host "`n-> npm install & npm run build (docker run node:18)" -ForegroundColor Yellow
docker run --rm `
  -v "${PWD}:/app" `
  -w /app `
  node:18 `
  /bin/sh -c "npm install && npm run build"

# 4) Instalar dependencias PHP
Write-Host "`n-> composer install (docker run composer:2)" -ForegroundColor Yellow
docker run --rm `
  -v "${PWD}:/app" `
  -w /app `
  composer:2 `
  install --no-dev --optimize-autoloader

# 5) Ejecutar migraciones
Write-Host "`n-> php artisan migrate --force" -ForegroundColor Yellow
docker exec kitchen_app php artisan migrate --force

# 6) Cache y limpieza
Write-Host "`n-> php artisan config:clear & config:cache & route:cache & view:clear" -ForegroundColor Yellow
docker exec kitchen_app php artisan config:clear
docker exec kitchen_app php artisan config:cache
docker exec kitchen_app php artisan route:cache
docker exec kitchen_app php artisan view:clear

# 7) Reconstruir solo app
Write-Host "`n-> docker-compose up -d --no-deps --build app" -ForegroundColor Yellow
docker-compose up -d --no-deps --build app

# 8) Recargar nginx
Write-Host "`n-> Recargando nginx" -ForegroundColor Yellow
docker exec kitchen_nginx nginx -s reload

Write-Host "`n=== Despliegue finalizado: $(Get-Date) ===" -ForegroundColor Green
Pop-Location
