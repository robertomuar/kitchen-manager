## Codex / CI

Este proyecto requiere:

- COMPOSER_GITHUB_TOKEN como secreto del entorno.
- Ejecutar:
  - composer install
  - npm ci && npm run build
  - scripts/ci-test.sh

Notas:
- No commitear archivos .env (incluido .env.testing).
- Tests usan sqlite en memoria.
