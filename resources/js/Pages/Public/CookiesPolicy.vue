<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() => usePage().props.app?.url ?? '');
const canonical = computed(() => `${baseUrl.value}/cookies-policy`);

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'Política de cookies', href: '/cookies-policy' },
];
</script>

<template>
  <MarketingLayout>
    <Head>
      <title>Política de cookies</title>
      <meta
        name="description"
        content="Consulta cómo KitchenManager utiliza cookies necesarias y analíticas para mejorar tu experiencia."
      >
      <link rel="canonical" :href="canonical">
    </Head>

    <Breadcrumbs :items="breadcrumbs" />

    <h1 class="text-3xl font-semibold text-slate-900">Política de cookies</h1>
    <p class="mt-4 text-slate-600">
      Utilizamos cookies para asegurar el funcionamiento de KitchenManager y entender cómo se usa la plataforma.
      Puedes gestionar tus preferencias en cualquier momento desde el pie de página.
    </p>

    <div class="mt-8 space-y-6 text-sm text-slate-600">
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Cookies necesarias</h2>
        <p>
          Son imprescindibles para iniciar sesión, mantener la sesión activa y guardar tus preferencias básicas.
          Estas cookies siempre están activas porque sin ellas la aplicación no funcionaría correctamente.
        </p>
      </section>
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Cookies analíticas</h2>
        <p>
          Nos permiten medir el rendimiento y detectar mejoras. Solo se activan si das tu consentimiento y no
          contienen información personal directa.
        </p>
      </section>
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Gestión de preferencias</h2>
        <p>
          Puedes aceptar todas las cookies, rechazar las no esenciales o configurar únicamente las analíticas.
          Elige la opción que mejor se adapte a tus necesidades.
        </p>
      </section>
    </div>
  </MarketingLayout>
</template>
