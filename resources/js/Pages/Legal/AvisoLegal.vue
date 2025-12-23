<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() =>
  usePage().props.app?.url ?? (typeof window !== 'undefined' ? window.location.origin : '')
);
const canonical = computed(() => `${baseUrl.value}/legal/aviso-legal`);

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'Aviso legal', href: '/legal/aviso-legal' },
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
      <title>Aviso legal | KitchenManager</title>
      <meta
        name="description"
        content="Aviso legal de KitchenManager: titular, condiciones de uso, propiedad intelectual y responsabilidad del servicio doméstico de inventario."
      >
      <link rel="canonical" :href="canonical">
      <meta property="og:title" content="Aviso legal | KitchenManager">
      <meta
        property="og:description"
        content="Información legal sobre KitchenManager, su titularidad y condiciones generales de uso."
      >
      <meta property="og:url" :content="canonical">
      <meta property="twitter:card" content="summary">
      <meta property="twitter:title" content="Aviso legal | KitchenManager">
      <meta
        property="twitter:description"
        content="Conoce el aviso legal y las condiciones de uso de KitchenManager."
      >
      <script type="application/ld+json" v-html="breadcrumbJsonLd" />
    </Head>

    <Breadcrumbs :items="breadcrumbs" />

    <h1 class="text-3xl font-semibold text-slate-900">Aviso legal</h1>
    <p class="mt-4 text-slate-600">
      Este aviso legal regula el acceso y uso de KitchenManager, una aplicación de gestión doméstica
      de inventario, caducidades y compras del hogar. Al navegar por este sitio aceptas las condiciones
      aquí descritas.
    </p>

    <div class="mt-8 space-y-8 text-sm text-slate-600">
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Identificación del titular</h2>
        <p>
          Titular: <strong>KitchenManager</strong>. Domicilio social en España. Contacto general:
          <strong>soporte@kitchenmanager.app</strong>.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Objeto del servicio</h2>
        <p>
          KitchenManager ofrece herramientas para organizar productos del hogar, registrar existencias,
          controlar caducidades y planificar compras. El servicio está pensado para un uso doméstico y
          no sustituye asesoramiento profesional sobre seguridad alimentaria.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Propiedad intelectual</h2>
        <p>
          Todos los contenidos, marcas, logotipos, textos y diseños de KitchenManager son titularidad
          del responsable o de terceros licenciantes. Queda prohibida su reproducción, distribución o
          transformación sin autorización expresa.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Disponibilidad del servicio</h2>
        <p>
          Nos esforzamos por mantener la disponibilidad y seguridad del sitio, aunque pueden existir
          interrupciones por mantenimiento, actualizaciones o causas ajenas. El acceso puede suspenderse
          temporalmente sin responsabilidad por daños derivados.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Limitación de responsabilidad</h2>
        <p>
          El uso de KitchenManager es bajo tu responsabilidad. El titular no garantiza resultados
          concretos ni se hace responsable de decisiones tomadas por el usuario sobre compra, consumo
          o eliminación de alimentos.
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
        <h2 class="text-lg font-semibold text-slate-900">Enlaces externos</h2>
        <p>
          Este sitio puede contener enlaces a terceros. KitchenManager no controla ni responde por sus
          contenidos o políticas. El acceso a dichos enlaces es responsabilidad del usuario.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Legislación aplicable y jurisdicción</h2>
        <p>
          La relación con el usuario se regirá por la normativa española. Para consumidores, se aplican
          además las normas imperativas del país de residencia. En caso de controversia, las partes se
          someterán a los juzgados y tribunales del domicilio del consumidor o, en su defecto, a los de
          la ciudad del titular en España.
        </p>
      </section>
    </div>
  </MarketingLayout>
</template>
