<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() => usePage().props.app?.url ?? '');
const canonical = computed(() => `${baseUrl.value}/faq`);

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'FAQ', href: '/faq' },
];
</script>

<template>
  <MarketingLayout>
    <Head>
      <title>Preguntas frecuentes sobre control de stock de cocina</title>
      <meta
        name="description"
        content="Resuelve dudas sobre alertas de caducidad, inventario, listas de compra y seguridad de KitchenManager."
      >
      <link rel="canonical" :href="canonical">
    </Head>

    <Breadcrumbs :items="breadcrumbs" />

    <h1 class="text-3xl font-semibold text-slate-900">Preguntas frecuentes</h1>
    <p class="mt-3 text-slate-600">
      Aquí respondemos las dudas más comunes sobre cómo gestionar stock, caducidades y listas de compra con
      KitchenManager.
    </p>

    <div class="mt-10 space-y-8">
      <div>
        <h2 class="text-xl font-semibold text-slate-900">¿Necesito registrar cada producto manualmente?</h2>
        <p class="mt-2 text-slate-600">
          Puedes registrar productos de forma rápida con plantillas de unidades y cantidades típicas. Una vez
          guardado un producto, lo reutilizas cada vez que lo añades al stock. Esto reduce el trabajo manual en
          el día a día.
        </p>
      </div>

      <div>
        <h2 class="text-xl font-semibold text-slate-900">¿Cómo funcionan las alertas de caducidad?</h2>
        <p class="mt-2 text-slate-600">
          Cada ítem de stock tiene una fecha de caducidad. El panel principal muestra los productos que caducan
          pronto y aquellos que están por debajo del mínimo. Así puedes priorizar recetas o reposiciones con tiempo.
        </p>
      </div>

      <div>
        <h2 class="text-xl font-semibold text-slate-900">¿Puedo compartir el inventario con mi equipo?</h2>
        <p class="mt-2 text-slate-600">
          Sí. KitchenManager permite que varias personas trabajen sobre el mismo inventario, evitando duplicidades
          y asegurando que todos vean los cambios en tiempo real.
        </p>
      </div>

      <div>
        <h2 class="text-xl font-semibold text-slate-900">¿Hay listas de compra automáticas?</h2>
        <p class="mt-2 text-slate-600">
          Puedes generar reportes de reposición en base a mínimos. El sistema lista lo que falta y te permite
          exportarlo en formatos prácticos para compartir con proveedores o tu equipo.
        </p>
      </div>

      <div>
        <h2 class="text-xl font-semibold text-slate-900">¿Qué pasa si cambio de dispositivo?</h2>
        <p class="mt-2 text-slate-600">
          El inventario está centralizado, por lo que puedes acceder desde cualquier dispositivo con tu cuenta.
          Tus datos se mantienen sincronizados sin esfuerzo adicional.
        </p>
      </div>

      <div>
        <h2 class="text-xl font-semibold text-slate-900">¿KitchenManager es gratuito?</h2>
        <p class="mt-2 text-slate-600">
          Sí, el acceso básico es gratuito. Consulta la página de precios para ver qué opciones de soporte o
          servicios adicionales están disponibles a futuro.
        </p>
      </div>
    </div>

    <section class="mt-12 rounded-3xl border border-slate-200 bg-slate-50 p-8">
      <h2 class="text-2xl font-semibold text-slate-900">¿Te queda alguna duda?</h2>
      <p class="mt-2 text-slate-600">
        Escríbenos y te ayudamos a organizar tu cocina con un plan personalizado.
      </p>
      <div class="mt-6 flex flex-wrap gap-3">
        <Link href="/contact" class="rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-600">
          Contactar
        </Link>
        <Link href="/features" class="rounded-full border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-slate-400">
          Ver funcionalidades
        </Link>
      </div>
    </section>
  </MarketingLayout>
</template>
