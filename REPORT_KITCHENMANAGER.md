# KitchenManager Improvement Report

Estado inicial detectado (branch `main` -> `improve/report-all`). El proyecto es Laravel + Inertia/Vue. No existe documentación previa de mejoras.

## P0 — Seguridad y consistencia
- **Multi-tenant/owner**: ✅ Ajustado. CRUD de productos/ubicaciones/stock usan `kitchenOwnerId()` + `kitchen_id` en queries/altas y los dashboards limpian caché por cocina. Policies nuevas para validar acceso por owner+kitchen.【F:app/Http/Controllers/ProductController.php†L15-L180】【F:app/Http/Controllers/LocationController.php†L15-L128】【F:app/Http/Controllers/StockItemController.php†L17-L210】【F:app/Policies/ProductPolicy.php†L1-L40】【F:app/Policies/LocationPolicy.php†L1-L40】【F:app/Policies/StockItemPolicy.php†L1-L40】
- **Validación `exists` con scope**: ✅ Rules con `Rule::exists()->where('user_id')->where('kitchen_id')` en requests de producto/ubicación/stock.【F:app/Http/Requests/ProductRequest.php†L15-L36】【F:app/Http/Requests/StockItemRequest.php†L15-L40】【F:app/Http/Requests/LocationRequest.php†L9-L22】
- **Policies**: ✅ Policies registradas vía `AuthServiceProvider` y `bootstrap/app.php`. Controladores usan `authorize(...)` en show/edit/update/destroy.【F:bootstrap/app.php†L5-L24】【F:app/Providers/AuthServiceProvider.php†L1-L30】【F:app/Http/Controllers/ProductController.php†L76-L150】
- **KitchenShare**: ✅ Protegido con transacciones + locks al modificar `acting_as_user_id`.【F:app/Http/Controllers/KitchenShareController.php†L13-L67】
- **Cache dashboard**: ✅ Claves incluyen owner+kitchen y se invalidan tras cambios en CRUD relevantes.【F:routes/web.php†L91-L137】【F:app/Http/Controllers/Controller.php†L12-L38】
- **Rutas 405 recursos**: ✅ Done (already present).

## P1 — Rendimiento, despliegue y UX
- **Export CSV/PDF**: ✅ CSV con `chunk(200)` en streaming y PDF usando `lazy()` para evitar OOM.【F:app/Http/Controllers/StockItemController.php†L201-L248】
- **Legacy location string**: ◻️ Partial. Se mantiene el campo de compatibilidad en `StockItem`; pendiente definir migración/eliminación tras revisar datos históricos.【F:app/Models/StockItem.php†L10-L52】
- **Nginx/Vite headers**: ✅ Done (already present en DEPLOY_PERFORMANCE.md con cache largo assets y gzip).【F:DEPLOY_PERFORMANCE.md†L1-L66】
- **Barcode lookup**: ✅ Protegido con `auth`, `verified` y `throttle` en ruta.【F:routes/web.php†L74-L77】
- **UX accesible/empty states**: ✅ Estados vacíos con CTA y `aria-label` en listados de productos/stock/ubicaciones.【F:resources/js/Pages/Products/Index.vue†L270-L355】【F:resources/js/Pages/Stock/Index.vue†L396-L466】【F:resources/js/Pages/Locations/Index.vue†L33-L86】

## P2 — Features
- **Selector de cocina**: ✅ Selector en navbar (desktop/móvil) con switch persistente en sesión y endpoint seguro para cambiar de cocina/owner.【F:resources/js/Layouts/AuthenticatedLayout.vue†L30-L150】【F:app/Http/Controllers/KitchenSelectionController.php†L1-L45】【F:app/Http/Middleware/HandleInertiaRequests.php†L15-L74】
- **Selects async**: ✅ Endpoints paginados y selects con búsqueda/paginación en formulario de stock (productos/ubicaciones).【F:app/Http/Controllers/ProductController.php†L182-L209】【F:app/Http/Controllers/LocationController.php†L130-L157】【F:resources/js/Pages/Stock/Form.vue†L6-L640】
- **Historial de movimientos de stock**: ✅ Nuevo modelo/migración `stock_movements`, logging en CRUD y visualización en edición de stock.【F:database/migrations/2026_01_01_000000_create_stock_movements_table.php†L1-L38】【F:app/Http/Controllers/StockItemController.php†L205-L274】【F:resources/js/Pages/Stock/Form.vue†L560-L618】

## Pendientes obligatorios
Aplicar las mejoras anteriores en orden (Fase 0 → P0 → P1 → P2). Mantener commit pequeños y ejecutar tests + build tras cada bloque.
