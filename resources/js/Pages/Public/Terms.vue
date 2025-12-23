<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() =>
  usePage().props.app?.url ?? (typeof window !== 'undefined' ? window.location.origin : '')
);
const canonical = computed(() => `${baseUrl.value}/terms`);
const pageTitle = 'Términos de uso';
const description = 'Términos y condiciones para usar KitchenManager y gestionar tu inventario de cocina.';

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'Términos', href: '/terms' },
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

    <h1 class="text-3xl font-semibold text-slate-900">Términos de uso</h1>
    <p class="mt-4 text-slate-600">
      Al usar KitchenManager aceptas estos términos. Nuestro objetivo es ofrecer una plataforma segura para
      controlar tu stock y caducidades.
    </p>

    <div class="mt-8 space-y-6 text-sm text-slate-600">
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Uso responsable</h2>
        <p>
          Eres responsable de mantener la confidencialidad de tus credenciales y de la información que ingresas.
          No debes usar el servicio para actividades ilegales o no autorizadas.
        </p>
      </section>
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Disponibilidad</h2>
        <p>
          Hacemos lo posible para mantener el servicio disponible, pero puede haber interrupciones por mantenimiento
          o causas externas.
        </p>
      </section>
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Limitación de responsabilidad</h2>
        <p>
          KitchenManager proporciona herramientas de organización; las decisiones de compra o uso de alimentos
          son responsabilidad del usuario. No garantizamos resultados específicos.
        </p>
      </section>
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Cambios en el servicio</h2>
        <p>
          Podemos actualizar funcionalidades o términos para mejorar la plataforma. Notificaremos cambios relevantes
          en esta página.
        </p>
      </section>
    </div>
  </MarketingLayout>
</template>
