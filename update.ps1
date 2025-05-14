# update.ps1
<#
  Despliegue para kitchen-manager
  — No borra update.ps1 ni nginx/default.conf
  — Limpia sólo public/build
  — Invoca docker-compose con la ruta correcta
#>

Push-Location $PSScriptRoot
Write-Host "→ Directorio de trabajo: $(Get-Location)" -ForegroundColor Cyan
Write-Host "=== Despliegue iniciado: $(Get-Date) ===" -ForegroundColor Green

# 1) Reset duro de archivos versionados
Write-Host "`n-> git fetch origin && git reset --hard origin/main" -ForegroundColor Yellow
git fetch origin
git reset --hard origin/main

# 2) Limpiar sólo los assets viejos
Write-Host "`n-> Limpiando carpeta public/build" -ForegroundColor Yellow
if (Test-Path .\public\build) {
    Remove-Item -Recurse -Force .\public\build
}

# 3) Pull de cambios
Write-Host "`n-> git pull origin main" -ForegroundColor Yellow
git pull origin main

# 4) Compilar frontend
Write-Host "`n-> npm install & npm run build (docker run node:18)" -ForegroundColor Yellow
docker run --rm `
  -v "${PWD}:/app" `
  -w /app `
  node:18 `
  /bin/sh -c "npm install && npm run build"

# 5) Instalar deps PHP
Write-Host "`n-> composer install (docker run composer:2)" -ForegroundColor Yellow
docker run --rm `
  -v "${PWD}:/app" `
  -w /app `
  composer:2 `
  install --no-dev --optimize-autoloader

# 6) Migraciones
Write-Host "`n-> php artisan migrate --force" -ForegroundColor Yellow
docker exec kitchen_app php artisan migrate --force

# 7) Cache y limpieza
Write-Host "`n-> Cache & clear commands" -ForegroundColor Yellow
docker exec kitchen_app php artisan config:clear
docker exec kitchen_app php artisan config:cache
docker exec kitchen_app php artisan route:cache
docker exec kitchen_app php artisan view:clear

# 8) Reconstruir sólo app con ruta explícita al compose
$composeFile = Join-Path $PSScriptRoot 'docker-compose.yml'
Write-Host "`n-> docker-compose -f `"$composeFile`" up -d --no-deps --build app" -ForegroundColor Yellow
docker-compose -f "$composeFile" up -d --no-deps --build app

# 9) Recargar nginx
Write-Host "`n-> Recargando nginx" -ForegroundColor Yellow
docker exec kitchen_nginx nginx -s reload

Write-Host "`n=== Despliegue finalizado: $(Get-Date) ===" -ForegroundColor Green
Pop-Location
