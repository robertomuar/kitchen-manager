@echo off
REM — Sitúate en la carpeta donde está este script
cd /d "%~dp0"

REM — Pide mensaje de commit
set /p msg=Introduce mensaje de commit: 

REM — Añade los ficheros que quieras
git add update.ps1 nginx\default.conf resources\views\layouts\app.blade.php

REM — Si no hay nada que commitear, salimos
git diff --cached --quiet
if errorlevel 1 (
    git commit -m "%msg%"
    git push origin main
) else (
    echo No hay cambios para commitear.
)

pause
