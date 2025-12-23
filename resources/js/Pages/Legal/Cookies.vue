<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() =>
  usePage().props.app?.url ?? (typeof window !== 'undefined' ? window.location.origin : '')
);
const canonical = computed(() => `${baseUrl.value}/legal/cookies`);

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'Cookies', href: '/legal/cookies' },
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
      <title>Política de cookies | KitchenManager</title>
      <meta
        name="description"
        content="Política de cookies de KitchenManager: tipos de cookies, finalidad, consentimiento y configuración."
      >
      <link rel="canonical" :href="canonical">
      <meta property="og:title" content="Política de cookies | KitchenManager">
      <meta
        property="og:description"
        content="Consulta cómo usamos cookies necesarias y analíticas en KitchenManager."
      >
      <meta property="og:url" :content="canonical">
      <meta property="twitter:card" content="summary">
      <meta property="twitter:title" content="Política de cookies | KitchenManager">
      <meta
        property="twitter:description"
        content="Información sobre cookies y cómo gestionar el consentimiento en KitchenManager."
      >
      <script type="application/ld+json" v-html="breadcrumbJsonLd" />
    </Head>

    <Breadcrumbs :items="breadcrumbs" />

    <h1 class="text-3xl font-semibold text-slate-900">Política de cookies</h1>
    <p class="mt-4 text-slate-600">
      KitchenManager utiliza cookies y tecnologías similares para garantizar el funcionamiento básico
      y, con tu consentimiento, obtener analíticas de uso que nos ayuden a mejorar la experiencia.
    </p>

    <div class="mt-8 space-y-8 text-sm text-slate-600">
      <section>
        <h2 class="text-lg font-semibold text-slate-900">¿Qué son las cookies?</h2>
        <p>
          Las cookies son pequeños archivos que se almacenan en tu dispositivo para recordar preferencias
          y facilitar el uso del servicio. Algunas son necesarias para el funcionamiento y otras son
          opcionales.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Cookies necesarias</h2>
        <p>
          Son imprescindibles para la seguridad, autenticación y funcionamiento de KitchenManager, como
          mantener tu sesión iniciada o recordar tu aceptación de términos. Se instalan por defecto y no
          pueden desactivarse desde el panel.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Cookies analíticas</h2>
        <p>
          Nos permiten comprender cómo se usa la aplicación (pantallas visitadas, frecuencia de uso) para
          mejorar el servicio. Solo se activan si otorgas tu consentimiento explícito.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Gestión del consentimiento</h2>
        <p>
          Puedes aceptar todas, rechazar las no esenciales o configurar tus preferencias desde el banner
          inicial. Rechazar es tan sencillo como aceptar, y puedes cambiar o retirar tu consentimiento en
          cualquier momento usando el enlace “Configurar cookies” del pie de página.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Baja y eliminación de cuenta</h2>
        <p>
          Puedes solicitar el borrado de tu cuenta. En ese caso eliminaremos los datos personales
          identificativos y conservaremos únicamente información anonimizada e irreversible para
          estadísticas y mejora del servicio. Si existiera una obligación legal o necesidad de defensa
          ante reclamaciones, podremos bloquear datos mínimos durante el plazo exigido.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Base legal</h2>
        <p>
          Las cookies necesarias se basan en nuestro interés legítimo de prestar el servicio. Las cookies
          analíticas se basan en tu consentimiento, que puedes modificar cuando quieras.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Normativa aplicable</h2>
        <p>
          Aplicamos la normativa española y europea sobre cookies y, en caso de usuarios internacionales,
          las normas imperativas del país del usuario en materia de privacidad y comunicaciones
          electrónicas.
        </p>
      </section>
    </div>
  </MarketingLayout>
</template>
