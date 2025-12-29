# Stock en unidades (diagnóstico y objetivo)

## Estado actual (antes del cambio)
- **Campos de productos**: `default_quantity`, `default_unit`.
- **Campos de stock**: `quantity`, `unit`, `is_open` (booleano), `min_quantity`.
- La aplicación considera `quantity` como la cantidad disponible sin descontar envases abiertos.
- La alerta de bajo mínimo se calcula comparando directamente `quantity` contra `min_quantity`.
- Exportaciones (CSV/PDF) de faltantes usan `quantity` para calcular lo que falta.

## Estado deseado (después del cambio)
- Se añade `open_units` a `stock_items` para registrar cuántas unidades están abiertas.
- Se expone `available_units = max(quantity - open_units, 0)` para representar unidades realmente disponibles.
- Las comparaciones de mínimo y listados de bajo stock usan `available_units` en vez de `quantity`.
- Las exportaciones de faltantes y el dashboard también usan `available_units` para cálculos.
- El formulario trata `quantity` como número de **unidades** (packs) y permite indicar cuántas están abiertas (entre 0 y `quantity`).
- En la UI se muestran las unidades totales, las abiertas y las disponibles resultantes.
