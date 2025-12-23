<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() =>
  usePage().props.app?.url ?? (typeof window !== 'undefined' ? window.location.origin : '')
);
const canonical = computed(() => `${baseUrl.value}/pricing`);
const pageTitle = 'Precios de KitchenManager';
const description = 'Plan gratuito para control de stock y caducidades con acceso completo a funciones esenciales.';

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'Precios', href: '/pricing' },
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

    <h1 class="text-3xl font-semibold text-slate-900">Precios claros y sin sorpresas</h1>
    <p class="mt-3 text-slate-600">
      KitchenManager es gratuito para ayudarte a mantener tu cocina organizada. A futuro podremos ofrecer
      servicios opcionales, pero el control de stock esencial seguirá siendo accesible.
    </p>

    <div class="mt-10 grid gap-6 md:grid-cols-2">
      <div class="rounded-3xl border border-amber-200 bg-amber-50 p-8">
        <h2 class="text-xl font-semibold text-slate-900">Plan gratuito</h2>
        <p class="mt-2 text-slate-600">Todo lo que necesitas para empezar hoy.</p>
        <div class="mt-6 text-4xl font-semibold text-slate-900">0 €</div>
        <ul class="mt-6 space-y-3 text-sm text-slate-600">
          <li>• Control de productos, ubicaciones y caducidades.</li>
          <li>• Alertas de stock bajo y productos por caducar.</li>
          <li>• Reportes de reposición exportables.</li>
          <li>• Acceso desde cualquier dispositivo.</li>
        </ul>
        <Link href="/register" class="mt-6 inline-flex rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-600">
          Crear cuenta
        </Link>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <h2 class="text-xl font-semibold text-slate-900">Servicios opcionales</h2>
        <p class="mt-2 text-slate-600">
          Si necesitas soporte avanzado, formación o integración personalizada, podemos ayudarte.
        </p>
        <ul class="mt-6 space-y-3 text-sm text-slate-600">
          <li>• Consultoría para optimizar flujos de compras.</li>
          <li>• Configuración inicial asistida.</li>
          <li>• Exportaciones personalizadas y reportes avanzados.</li>
          <li>• Soporte prioritario para equipos.</li>
        </ul>
        <Link href="/contact" class="mt-6 inline-flex rounded-full border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-slate-400">
          Solicitar información
        </Link>
      </div>
    </div>

    <section class="mt-12 rounded-3xl border border-slate-200 bg-slate-50 p-8">
      <h2 class="text-2xl font-semibold text-slate-900">¿Quieres empezar ya?</h2>
      <p class="mt-2 text-slate-600">
        Regístrate en minutos y descubre cómo una buena organización reduce desperdicios y mejora tu rentabilidad.
      </p>
      <div class="mt-6 flex flex-wrap gap-3">
        <Link href="/register" class="rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-600">
          Crear cuenta gratis
        </Link>
        <Link href="/faq" class="rounded-full border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-slate-400">
          Ver FAQ
        </Link>
      </div>
    </section>
  </MarketingLayout>
</template>
