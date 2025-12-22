# SEO Checklist (KitchenManager)

## Lighthouse

1. Ejecuta Lighthouse en Chrome DevTools (pestaña Lighthouse).
2. Selecciona "Performance", "SEO" y "Best Practices".
3. Ejecuta el análisis en local y en producción.

## Verificación con curl

```bash
curl -i http://localhost:8000/robots.txt
curl -i http://localhost:8000/sitemap.xml
```

## Validación de Schema

- Usa la herramienta de Google Rich Results Test.
- Valida el JSON-LD de:
  - WebSite + Organization + SoftwareApplication (layout base)
  - BreadcrumbList (páginas públicas)
  - Article (detalle de blog)

## Search Console

1. Accede a https://search.google.com/search-console.
2. Añade tu dominio o prefijo de URL.
3. Verifica propiedad con DNS o meta tag.
4. Envía el sitemap: `https://tudominio.com/sitemap.xml`.
5. Revisa cobertura e indexación en las siguientes 48-72 horas.

## Revisión final

- Canonical absoluto en páginas públicas.
- Robots con disallow en zonas privadas.
- Meta `noindex` en login, dashboard y rutas privadas.
- Sitemap con URLs públicas reales y `lastmod`.
- Blog indexado solo con posts publicados.
