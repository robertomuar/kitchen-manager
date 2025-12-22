# SEO Checklist y verificación

## Lighthouse (local y producción)
1. Local:
   - `npm install`
   - `npm run build`
   - `php artisan serve` y abre http://127.0.0.1:8000
   - Ejecuta Lighthouse desde Chrome DevTools > Lighthouse.
2. Producción:
   - Ejecuta Lighthouse sobre la URL pública.

## Validación robots.txt y sitemap
```bash
curl -I https://kitchenmanager.duckdns.org/robots.txt
curl -I https://kitchenmanager.duckdns.org/sitemap.xml
curl -s https://kitchenmanager.duckdns.org/sitemap.xml | head -n 20
```

## Validar schema (Rich Results Test)
- https://search.google.com/test/rich-results
- Probar:
  - Home: https://kitchenmanager.duckdns.org/
  - Blog post: https://kitchenmanager.duckdns.org/blog/{slug}

## Configurar Google Search Console
1. Accede a https://search.google.com/search-console
2. Añade la propiedad del dominio o la URL completa.
3. Verifica la propiedad (DNS o HTML).
4. Envía el sitemap: `/sitemap.xml`.
5. Espera a que se procese y revisa la sección de Cobertura.

## Qué revisar (SEO técnico)
- Canonical correcto y absoluto en cada página pública.
- Meta robots en páginas privadas: `noindex, nofollow`.
- Robots.txt bloqueando rutas privadas.
- Sitemap con solo URLs públicas reales.
- Páginas públicas con H1 único y contenido real.
- Blog con schema Article y breadcrumbs.
- Rendimiento estable en Lighthouse (CLS/LCP).
