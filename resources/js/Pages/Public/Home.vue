<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() =>
  usePage().props.app?.url ?? (typeof window !== 'undefined' ? window.location.origin : '')
);
const canonical = computed(() => `${baseUrl.value}`);
const pageTitle = 'Control de stock y caducidades para tu cocina';
const description = 'Organiza despensa, nevera y congelador con alertas de caducidad, listas de compra automáticas y reportes de stock para equipos de cocina.';

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
];

const breadcrumbJsonLd = computed(() => JSON.stringify({
  '@context': 'https://schema.org',
  '@type': 'BreadcrumbList',
  itemListElement: breadcrumbs.map((crumb, index) => ({
    '@type': 'ListItem',
    position: index + 1,
    name: crumb.label,
    item: `${baseUrl.value}${crumb.href === '/' ? '' : crumb.href}`,
  })),
}));
</script>

<template>
  <MarketingLayout>
    <Head>
      <title>{{ pageTitle }}</title>
      <meta
        name="description"
        :content="description"
      >
      <meta property="og:type" content="website">
      <meta property="og:title" :content="pageTitle">
      <meta property="og:description" :content="description">
      <meta property="og:url" :content="canonical">
      <link rel="canonical" :href="canonical">
      <script type="application/ld+json" v-html="breadcrumbJsonLd" />
    </Head>

    <Breadcrumbs :items="breadcrumbs" />

    <section class="grid gap-10 lg:grid-cols-[1.2fr_0.8fr]">
      <div class="space-y-6">
        <h1 class="text-4xl font-semibold tracking-tight text-slate-900">
          Cocina ordenada, compras inteligentes y cero caducidades inesperadas.
        </h1>
        <p class="text-lg text-slate-600">
          KitchenManager es un sistema pensado para equipos de cocina doméstica o profesional que necesitan
          controlar el stock, detectar caducidades y coordinar compras sin perder tiempo. Centraliza en una sola
          vista los productos que tienes en despensa, nevera y congelador, y convierte los datos en decisiones
          prácticas: qué reponer, qué cocinar primero y qué ajustar para evitar desperdicio.
        </p>
        <div class="flex flex-wrap gap-3">
          <Link href="/register" class="rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-600">
            Crear cuenta gratis
          </Link>
          <Link href="/login" class="rounded-full border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-slate-400">
            Ya tengo cuenta
          </Link>
        </div>
      </div>
      <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8">
        <h2 class="text-lg font-semibold text-slate-900">Resumen rápido</h2>
        <ul class="mt-4 space-y-3 text-sm text-slate-600">
          <li>• Alertas de caducidad configurables por días de antelación.</li>
          <li>• Control de ubicaciones para saber dónde está cada producto.</li>
          <li>• Reportes de reposición por mínimos para comprar lo justo.</li>
          <li>• Historial de consumo para decidir compras futuras con datos reales.</li>
        </ul>
      </div>
    </section>

    <section class="mt-12 space-y-6 text-base text-slate-600">
      <h2 class="text-2xl font-semibold text-slate-900">Organiza tu inventario sin esfuerzo</h2>
      <p>
        El control de stock en cocina suele fallar por falta de visibilidad. KitchenManager resuelve este problema
        con un inventario claro y actualizado: cada producto tiene su ubicación, cantidad actual, unidad de medida
        y fecha de caducidad. Al registrar movimientos, el sistema actualiza automáticamente el estado y destaca
        los artículos que están por debajo de su mínimo o que caducan pronto. Así evitas sorpresas y reduces el
        desperdicio alimentario sin tener que mantener hojas de cálculo.
      </p>
      <p>
        La plataforma también ayuda a mantener consistencia en equipos con varias personas. Cada cocina trabaja
        con un propietario definido y puede compartir información con colaboradores, lo que significa que todos
        ven el mismo stock en tiempo real. Cuando llega un pedido o se utiliza un ingrediente, el cambio se refleja
        al instante. Esto mejora la coordinación, reduce llamadas de última hora y permite que el equipo se centre
        en cocinar, no en buscar productos.
      </p>
      <p>
        Además, el sistema está diseñado para generar conocimiento. Con reportes de reposición, puedes priorizar
        compras según consumo histórico y volumen disponible. Cuando preparas una lista de compra, puedes exportar
        los productos faltantes en formatos que se ajustan a tu flujo de trabajo. Así mantienes la cocina abastecida
        sin exceso de inventario ni compras impulsivas.
      </p>
      <p>
        Si estás buscando una herramienta para controlar despensa, nevera y congelador con alertas de caducidad,
        KitchenManager te da un punto de partida sólido. Es un sistema flexible que funciona tanto para cocinas
        familiares como para pequeños negocios gastronómicos, con información clara y accionable.
      </p>
    </section>

    <section class="mt-12 grid gap-6 rounded-3xl border border-amber-200 bg-amber-50 p-8">
      <h2 class="text-2xl font-semibold text-slate-900">Empieza hoy con una cocina más eficiente</h2>
      <p class="text-slate-700">
        Crea tu cuenta, carga tu inventario inicial y recibe alertas antes de que un producto se eche a perder.
        En menos de una hora tendrás una visión completa del stock y una lista de compra lista para compartir.
      </p>
      <div class="flex flex-wrap gap-3">
        <Link href="/register" class="rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-600">
          Crear cuenta
        </Link>
        <Link href="/features" class="rounded-full border border-amber-400 px-6 py-3 text-sm font-semibold text-amber-700 hover:border-amber-500">
          Ver funcionalidades
        </Link>
      </div>
    </section>
  </MarketingLayout>
</template>
