<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() =>
  usePage().props.app?.url ?? (typeof window !== 'undefined' ? window.location.origin : '')
);
const canonical = computed(() => `${baseUrl.value}/privacy-policy`);
const pageTitle = 'Política de privacidad';
const description = 'Conoce cómo KitchenManager protege tus datos y garantiza la privacidad de tu inventario.';

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'Política de privacidad', href: '/privacy-policy' },
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

    <h1 class="text-3xl font-semibold text-slate-900">Política de privacidad</h1>
    <p class="mt-4 text-slate-600">
      En KitchenManager tratamos tu información con transparencia. Esta política describe qué datos se recopilan,
      cómo se usan y cómo puedes ejercer tus derechos.
    </p>

    <div class="mt-8 space-y-6 text-sm text-slate-600">
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Datos que recopilamos</h2>
        <p>
          Recopilamos los datos necesarios para operar la plataforma: nombre, email y la información de stock que
          introduzcas (productos, ubicaciones, caducidades). No vendemos ni compartimos tus datos con terceros con
          fines publicitarios.
        </p>
      </section>
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Finalidad del tratamiento</h2>
        <p>
          Utilizamos los datos para ofrecerte el servicio, generar alertas de caducidad, mantener sincronizado tu
          inventario y mejorar la experiencia. Podemos enviarte comunicaciones operativas relacionadas con tu cuenta.
        </p>
      </section>
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Seguridad</h2>
        <p>
          Aplicamos medidas de seguridad razonables para proteger la información. Aun así, ningún sistema es infalible
          y te recomendamos usar contraseñas robustas.
        </p>
      </section>
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Tus derechos</h2>
        <p>
          Puedes solicitar acceso, rectificación o eliminación de tus datos escribiendo a hola@kitchenmanager.app.
        </p>
      </section>
    </div>
  </MarketingLayout>
</template>
