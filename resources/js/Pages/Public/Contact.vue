<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() => usePage().props.app?.url ?? '');
const canonical = computed(() => `${baseUrl.value}/contact`);

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'Contacto', href: '/contact' },
];
</script>

<template>
  <MarketingLayout>
    <Head>
      <title>Contacto</title>
      <meta
        name="description"
        content="Habla con el equipo de KitchenManager para mejorar el control de stock y caducidades en tu cocina."
      >
      <link rel="canonical" :href="canonical">
    </Head>

    <Breadcrumbs :items="breadcrumbs" />

    <h1 class="text-3xl font-semibold text-slate-900">Hablemos de tu cocina</h1>
    <p class="mt-3 text-slate-600">
      Escríbenos si quieres mejorar tu control de stock, integrar KitchenManager en tu rutina o recibir una guía
      personalizada para reducir desperdicios.
    </p>

    <div class="mt-10 grid gap-6 md:grid-cols-2">
      <div class="rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
        <h2 class="text-xl font-semibold text-slate-900">Datos de contacto</h2>
        <ul class="mt-4 space-y-3 text-sm text-slate-600">
          <li>• Email: hola@kitchenmanager.app</li>
          <li>• Horario: lunes a viernes, 9:00-18:00</li>
          <li>• Soporte en español</li>
        </ul>
        <p class="mt-6 text-sm text-slate-500">
          Respondemos normalmente en menos de 24 horas laborables.
        </p>
      </div>

      <div class="rounded-3xl border border-slate-200 bg-slate-50 p-8">
        <h2 class="text-xl font-semibold text-slate-900">¿Por dónde empezar?</h2>
        <p class="mt-3 text-sm text-slate-600">
          Si aún no tienes cuenta, prueba la plataforma gratis. Si ya la tienes, entra y revisa tus alertas de
          caducidad para ver las primeras oportunidades de mejora.
        </p>
        <div class="mt-6 flex flex-wrap gap-3">
          <Link href="/register" class="rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-600">
            Crear cuenta
          </Link>
          <Link href="/login" class="rounded-full border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-slate-400">
            Entrar
          </Link>
        </div>
      </div>
    </div>
  </MarketingLayout>
</template>
