<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const props = defineProps({
  posts: {
    type: Object,
    required: true,
  },
});

const baseUrl = computed(() =>
  usePage().props.app?.url ?? (typeof window !== 'undefined' ? window.location.origin : '')
);
const canonical = computed(() => `${baseUrl.value}/blog`);
const pageTitle = 'Blog de organización de cocina y control de stock';
const description = 'Consejos prácticos sobre control de stock, caducidades, compras inteligentes y organización de despensa.';

const breadcrumbs = [
  { label: 'Inicio', href: '/' },
  { label: 'Blog', href: '/blog' },
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

    <h1 class="text-3xl font-semibold text-slate-900">Blog</h1>
    <p class="mt-3 text-slate-600">
      Ideas, guías y buenas prácticas para gestionar despensa, nevera y congelador con datos reales.
    </p>

    <div v-if="props.posts.data.length" class="mt-10 space-y-6">
      <article
        v-for="post in props.posts.data"
        :key="post.slug"
        class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm"
      >
        <div class="text-xs uppercase tracking-wide text-slate-600">{{ post.published_at }}</div>
        <h2 class="mt-2 text-2xl font-semibold text-slate-900">
          <Link :href="`/blog/${post.slug}`" class="hover:text-amber-600">
            {{ post.title }}
          </Link>
        </h2>
        <p class="mt-3 text-slate-600">{{ post.excerpt }}</p>
        <Link :href="`/blog/${post.slug}`" class="mt-4 inline-flex text-sm font-semibold text-amber-600">
          Leer artículo →
        </Link>
      </article>
    </div>

    <div v-else class="mt-10 rounded-3xl border border-slate-200 bg-slate-50 p-8 text-slate-600">
      Aún no hay artículos publicados. Vuelve pronto para nuevas guías y consejos.
    </div>

    <div v-if="props.posts.links.length > 3" class="mt-10 flex flex-wrap gap-2">
      <Link
        v-for="link in props.posts.links"
        :key="link.url ?? link.label"
        :href="link.url || ''"
        class="rounded-full border border-slate-200 px-4 py-2 text-sm"
        :class="{
          'bg-amber-500 text-white border-amber-500': link.active,
          'text-slate-500 pointer-events-none': !link.url,
        }"
        v-html="link.label"
      />
    </div>
  </MarketingLayout>
</template>
