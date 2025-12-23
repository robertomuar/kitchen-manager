<script setup>
import { Head, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const baseUrl = computed(() =>
  usePage().props.app?.url ?? (typeof window !== 'undefined' ? window.location.origin : '')
);
const canonical = computed(() => `${baseUrl.value}/legal/terminos`);

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'Términos', href: '/legal/terminos' },
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
      <title>Términos y condiciones | KitchenManager</title>
      <meta
        name="description"
        content="Términos y condiciones de uso de KitchenManager para gestión doméstica de inventario, caducidades y compras."
      >
      <link rel="canonical" :href="canonical">
      <meta property="og:title" content="Términos y condiciones | KitchenManager">
      <meta
        property="og:description"
        content="Condiciones de uso de KitchenManager para organizar la cocina del hogar."
      >
      <meta property="og:url" :content="canonical">
      <meta property="twitter:card" content="summary">
      <meta property="twitter:title" content="Términos y condiciones | KitchenManager">
      <meta
        property="twitter:description"
        content="Lee los términos para usar KitchenManager de forma segura."
      >
      <script type="application/ld+json" v-html="breadcrumbJsonLd" />
    </Head>

    <Breadcrumbs :items="breadcrumbs" />

    <h1 class="text-3xl font-semibold text-slate-900">Términos y condiciones</h1>
    <p class="mt-4 text-slate-600">
      Estas condiciones regulan el acceso y uso de KitchenManager. Al crear una cuenta o utilizar la
      aplicación aceptas cumplirlas. Si no estás de acuerdo, debes dejar de usar el servicio.
    </p>

    <div class="mt-8 space-y-8 text-sm text-slate-600">
      <section>
        <h2 class="text-lg font-semibold text-slate-900">Objeto y alcance</h2>
        <p>
          KitchenManager es una herramienta para gestionar inventarios domésticos, caducidades, listas
          de compra y ubicaciones de productos. El servicio está orientado a uso personal y no a fines
          profesionales o empresariales.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Cuenta y uso responsable</h2>
        <p>
          Eres responsable de la seguridad de tus credenciales y de la veracidad de los datos
          introducidos. No puedes utilizar la plataforma para fines ilícitos ni compartir contenidos
          que vulneren derechos de terceros.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Propiedad intelectual</h2>
        <p>
          El software, marcas y contenidos de KitchenManager pertenecen a su titular o licenciatarios.
          Se concede una licencia de uso personal, no exclusiva y revocable para acceder al servicio.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Disponibilidad y soporte</h2>
        <p>
          Podemos suspender temporalmente el servicio por tareas de mantenimiento o incidencias. No se
          garantiza disponibilidad ininterrumpida. Puedes contactar con nosotros en
          <strong>soporte@kitchenmanager.app</strong> para soporte y consultas.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Limitación de responsabilidad</h2>
        <p>
          KitchenManager facilita la organización doméstica, pero el usuario es quien decide qué
          comprar, consumir o desechar. No asumimos responsabilidad por pérdidas, desperdicio de
          alimentos ni decisiones tomadas basadas en la información registrada.
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
        <h2 class="text-lg font-semibold text-slate-900">Cambios en las condiciones</h2>
        <p>
          Podemos actualizar estos términos para adaptarnos a cambios legales o mejoras del servicio.
          Publicaremos la versión vigente en esta página e indicaremos la fecha de actualización.
        </p>
      </section>

      <section>
        <h2 class="text-lg font-semibold text-slate-900">Legislación aplicable y jurisdicción</h2>
        <p>
          Estas condiciones se rigen por la normativa española. Para consumidores internacionales,
          también se aplican las normas imperativas del país de residencia del usuario. En caso de
          conflicto, las partes se someterán a los tribunales del domicilio del consumidor o, en su
          defecto, a los de la ciudad del titular en España.
        </p>
      </section>
    </div>
  </MarketingLayout>
</template>
