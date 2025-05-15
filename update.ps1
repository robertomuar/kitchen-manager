# update.ps1
<#
  Despliegue para kitchen-manager
  — Clona si hace falta
  — No borra update.ps1 ni nginx/default.conf
  — Limpia sólo public/build
  — Usa $PSScriptRoot para el volumen docker
#>

Push-Location $PSScriptRoot
Write-Host "`n→ Directorio de trabajo: $PSScriptRoot" -ForegroundColor Cyan
Write-Host "=== Despliegue iniciado: $(Get-Date) ===" -ForegroundColor Green

# 0) Si no existe .git, clonamos el repo
if (-not (Test-Path ".git")) {
  Write-Host "`n-> No se detecta repo Git, clonando desde GitHub…" -ForegroundColor Yellow
  Pop-Location
  git clone https://github.com/robertomuar/kitchen-manager.git .
  Push-Location $PSScriptRoot
}

# 1) Reset duro de archivos versionados
Write-Host "`n-> git fetch origin && git reset --hard origin/main" -ForegroundColor Yellow
git fetch origin
git reset --hard origin/main

# 1.1) Submódulos, si tienes
if (Test-Path ".gitmodules") {
  Write-Host "`n-> Actualizando submódulos" -ForegroundColor Yellow
  git submodule update --init --recursive
}

# 2) Limpiar sólo los assets viejos
Write-Host "`n-> Limpiando carpeta public/build" -ForegroundColor Yellow
if (Test-Path ".\public\build") {
    Remove-Item -Recurse -Force .\public\build
}

# 3) Pull de cambios
Write-Host "`n-> git pull origin main" -ForegroundColor Yellow
git pull origin main

# 4) Compilar frontend con Docker (Node 18)
Write-Host "`n-> npm install & npm run build dentro de Docker node:18" -ForegroundColor Yellow
docker run --rm `
  -v "${PSScriptRoot}:/app" `
  -w /app `
  node:18 `
  sh -c "npm install && npm run build"

# 5) Instalar deps PHP con Docker (Composer 2)
Write-Host "`n-> composer install dentro de Docker composer:2" -ForegroundColor Yellow
docker run --rm `
  -v "${PSScriptRoot}:/app" `
  -w /app `
  composer:2 `
  install --no-dev --optimize-autoloader

# 6) Migraciones
Write-Host "`n-> php artisan migrate --force dentro del contenedor app" -ForegroundColor Yellow
docker exec kitchen_app php artisan migrate --force

# 7) Cache y limpieza
Write-Host "`n-> Config & cache commands dentro del contenedor app" -ForegroundColor Yellow
docker exec kitchen_app php artisan config:clear
docker exec kitchen_app php artisan config:cache
docker exec kitchen_app php artisan route:cache
docker exec kitchen_app php artisan view:clear

# 8) Reconstruir sólo servicio app
$composeFile = Join-Path $PSScriptRoot 'docker-compose.yml'
Write-Host "`n-> docker-compose -f `"$composeFile`" up -d --no-deps --build app" -ForegroundColor Yellow
docker-compose -f "$composeFile" up -d --no-deps --build app

# 9) Recargar nginx
Write-Host "`n-> Recargando nginx en contenedor kitchen_nginx" -ForegroundColor Yellow
docker exec kitchen_nginx nginx -s reload

Write-Host "`n=== Despliegue finalizado: $(Get-Date) ===" -ForegroundColor Green
Pop-Location
