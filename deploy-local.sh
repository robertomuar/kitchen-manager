#!/usr/bin/env bash
set -e

# Ruta del proyecto LOCAL
PROJECT_DIR="/c/xampp/htdocs/kitchen-manager"

echo ">> Yendo al proyecto local: $PROJECT_DIR"
cd "$PROJECT_DIR"

echo ">> Compilando assets (npm run build)..."
npm run build

echo ">> Comprobando si hay cambios en Git..."
if git diff --quiet && git diff --cached --quiet; then
    echo "No hay cambios para subir. Nada que hacer."
    exit 0
fi

echo ">> Añadiendo cambios al commit..."
git add .

# Mensaje por defecto con fecha y hora
DEFAULT_MSG="Deploy local $(date '+%Y-%m-%d %H:%M')"
read -rp "Mensaje de commit (ENTER para usar '$DEFAULT_MSG'): " MSG
if [ -z "$MSG" ]; then
    MSG="$DEFAULT_MSG"
fi

echo ">> Haciendo commit con mensaje: $MSG"
git commit -m "$MSG"

echo ">> Subiendo a GitHub (origin main)..."
git push origin main

echo "✅ Deploy local -> GitHub completado."
