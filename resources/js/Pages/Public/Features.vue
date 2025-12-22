<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() => usePage().props.app?.url ?? '');
const canonical = computed(() => `${baseUrl.value}/features`);

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'Funcionalidades', href: '/features' },
];
</script>

<template>
  <MarketingLayout>
    <Head>
      <title>Funcionalidades para controlar stock, caducidades y compras</title>
      <meta
        name="description"
        content="Descubre las funcionalidades clave de KitchenManager: alertas de caducidad, ubicaciones, reportes de reposición, exportación y control colaborativo."
      >
      <link rel="canonical" :href="canonical">
    </Head>

    <Breadcrumbs :items="breadcrumbs" />

    <h1 class="text-3xl font-semibold tracking-tight text-slate-900">Funcionalidades que transforman tu cocina</h1>
    <p class="mt-4 text-lg text-slate-600">
      KitchenManager está diseñado para que el control de stock sea una tarea simple y predecible. Aquí tienes
      las funciones principales que hacen la diferencia en la rutina diaria de una cocina organizada.
    </p>

    <section class="mt-10 space-y-6 text-base text-slate-600">
      <h2 class="text-2xl font-semibold text-slate-900">Inventario con ubicaciones reales</h2>
      <p>
        Cada producto puede asignarse a una ubicación específica: despensa, nevera, congelador o cualquier zona
        que defina tu cocina. Esto evita búsquedas innecesarias y mejora la planificación de mise en place. Puedes
        filtrar por ubicación y saber exactamente qué hay disponible antes de empezar a cocinar.
      </p>
      <p>
        Además, el inventario admite unidades personalizadas y cantidades por defecto, lo que acelera el registro
        de productos recurrentes. La información queda guardada para futuras altas, reduciendo tareas repetitivas.
      </p>
    </section>

    <section class="mt-10 space-y-6 text-base text-slate-600">
      <h2 class="text-2xl font-semibold text-slate-900">Alertas de caducidad inteligentes</h2>
      <p>
        Las fechas de caducidad se convierten en alertas automáticas. KitchenManager señala los productos que
        caducan pronto, priorizando la rotación del stock. Esto es clave para evitar desperdicio, mejorar la
        seguridad alimentaria y mantener costos controlados.
      </p>
      <p>
        Puedes revisar rápidamente los productos con caducidad urgente y planificar menús que aprovechen esos
        ingredientes antes de que sea tarde. La información está disponible en el panel principal y en la vista
        de stock con filtros específicos.
      </p>
    </section>

    <section class="mt-10 space-y-6 text-base text-slate-600">
      <h2 class="text-2xl font-semibold text-slate-900">Reposición guiada por mínimos</h2>
      <p>
        Define cantidades mínimas por producto para que el sistema detecte automáticamente cuándo necesitas
        reponer. Los reportes de reposición muestran solo lo esencial, evitando compras excesivas y ayudando a
        mantener el inventario alineado con el consumo real.
      </p>
      <p>
        Puedes exportar reportes en formatos prácticos (CSV o PDF), compartirlos con tu equipo o con el proveedor
        y tener una lista de compra lista para usar. Esto simplifica el proceso semanal y reduce errores manuales.
      </p>
    </section>

    <section class="mt-10 space-y-6 text-base text-slate-600">
      <h2 class="text-2xl font-semibold text-slate-900">Colaboración y trazabilidad</h2>
      <p>
        KitchenManager permite que varias personas trabajen con el mismo inventario sin perder trazabilidad.
        Cada cambio queda reflejado en el stock y las ubicaciones. Esto es ideal para cocinas compartidas o equipos
        pequeños que necesitan coordinar tareas sin duplicar esfuerzos.
      </p>
      <p>
        Con esta visibilidad, puedes revisar qué productos se movieron recientemente, detectar consumos atípicos
        y tomar decisiones más informadas sobre tus compras.
      </p>
    </section>

    <section class="mt-12 rounded-3xl border border-slate-200 bg-slate-50 p-8">
      <h2 class="text-2xl font-semibold text-slate-900">¿Listo para probar?</h2>
      <p class="mt-3 text-slate-600">
        Registra tu inventario en minutos, configura tus alertas y empieza a comprar con datos reales.
      </p>
      <div class="mt-6 flex flex-wrap gap-3">
        <Link href="/register" class="rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-600">
          Crear cuenta
        </Link>
        <Link href="/pricing" class="rounded-full border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-slate-400">
          Ver precios
        </Link>
      </div>
    </section>
  </MarketingLayout>
</template>
