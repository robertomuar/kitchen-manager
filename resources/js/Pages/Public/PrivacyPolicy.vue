<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() => usePage().props.app?.url ?? '');
const canonical = computed(() => `${baseUrl.value}/privacy-policy`);

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'Política de privacidad', href: '/privacy-policy' },
];
</script>

<template>
  <MarketingLayout>
    <Head>
      <title>Política de privacidad</title>
      <meta
        name="description"
        content="Conoce cómo KitchenManager protege tus datos y garantiza la privacidad de tu inventario."
      >
      <link rel="canonical" :href="canonical">
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
