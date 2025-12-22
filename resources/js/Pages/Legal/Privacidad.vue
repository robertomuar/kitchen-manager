<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() => usePage().props.app?.url ?? '');
const canonical = computed(() => `${baseUrl.value}/legal/privacidad`);

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'Privacidad', href: '/legal/privacidad' },
];
</script>

<template>
  <MarketingLayout>
    <Head>
      <title>Política de privacidad | KitchenManager</title>
      <meta
        name="description"
        content="Política de privacidad de KitchenManager: finalidades, base legal, conservación y derechos RGPD." 
      >
      <link rel="canonical" :href="canonical">
      <meta property="og:title" content="Política de privacidad | KitchenManager">
      <meta
        property="og:description"
        content="Conoce cómo tratamos tus datos personales en KitchenManager."
      >
      <meta property="og:url" :content="canonical">
      <meta property="twitter:card" content="summary">
      <meta property="twitter:title" content="Política de privacidad | KitchenManager">
      <meta
        property="twitter:description"
        content="Información RGPD sobre el tratamiento de datos en KitchenManager."
      >
    </Head>

    <Breadcrumbs :items="breadcrumbs" />

    <h1 class="text-3xl font-semibold text-slate-900">Política de privacidad</h1>
    <p class="mt-4 text-slate-600">
      En KitchenManager respetamos tu privacidad y tratamos tus datos conforme al Reglamento (UE) 2016/679
      (RGPD) y la normativa española aplicable. Esta política explica qué datos recopilamos y con qué fines.
    </p>

    <div class="mt-8 space-y-8 text-sm text-slate-600">
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Responsable del tratamiento</h2>
        <p>
          Responsable: <strong>[Titular]</strong>. NIF/CIF: <strong>[NIF]</strong>. Dirección: <strong>[Dirección]</strong>.
          Email de contacto: <strong>[Email]</strong>.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Datos tratados</h2>
        <ul class="list-disc space-y-2 pl-5">
          <li>Datos de registro: nombre, email y credenciales de acceso.</li>
          <li>Información doméstica: productos, ubicaciones, fechas de caducidad y hábitos de consumo.</li>
          <li>Datos técnicos básicos: dirección IP, identificadores de sesión y cookies necesarias.</li>
        </ul>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Finalidades</h2>
        <ul class="list-disc space-y-2 pl-5">
          <li>Gestionar tu cuenta y permitirte organizar el inventario doméstico.</li>
          <li>Enviar comunicaciones operativas sobre el servicio (recordatorios, incidencias).</li>
          <li>Mejorar la plataforma mediante estadísticas agregadas y anonimizadas.</li>
          <li>Cumplir obligaciones legales y atender posibles reclamaciones.</li>
        </ul>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Base legal</h2>
        <ul class="list-disc space-y-2 pl-5">
          <li>Ejecución del contrato: prestación del servicio solicitado.</li>
          <li>Consentimiento: cookies analíticas y comunicaciones no esenciales.</li>
          <li>Interés legítimo: seguridad del servicio y prevención de fraude.</li>
          <li>Cumplimiento legal: obligaciones fiscales o requerimientos de autoridades.</li>
        </ul>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Conservación de datos</h2>
        <p>
          Conservamos los datos mientras mantengas la cuenta activa. Al solicitar el borrado, eliminamos
          los datos personales identificativos y únicamente conservamos información anonimizada e irreversible
          con fines estadísticos o de mejora. Si existe una obligación legal o necesidad de defensa ante
          reclamaciones, bloquearemos los datos mínimos durante el plazo exigido.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Cesiones y encargados</h2>
        <p>
          No vendemos datos personales. Podemos compartir información con proveedores tecnológicos que
          actúan como encargados del tratamiento (alojamiento, correo, analítica) bajo contrato y con
          garantías adecuadas. En transferencias internacionales se aplicarán las salvaguardas legales
          pertinentes.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Derechos</h2>
        <p>
          Puedes ejercer los derechos de acceso, rectificación, supresión, oposición, limitación y
          portabilidad escribiendo a <strong>[Email]</strong>. Si consideras que tus derechos no han sido
          atendidos, puedes reclamar ante la Agencia Española de Protección de Datos (AEPD) en
          <a
            href="https://www.aepd.es"
            class="font-semibold text-amber-600 hover:text-amber-700"
            rel="noreferrer"
            target="_blank"
          >www.aepd.es</a>.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Normas imperativas internacionales</h2>
        <p>
          Para usuarios fuera de España se aplicarán las normas de protección de datos imperativas del
          país de residencia, además de las garantías previstas en el RGPD.
        </p>
      </section>
    </div>
  </MarketingLayout>
</template>
