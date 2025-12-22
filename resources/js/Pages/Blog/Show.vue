<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import MarketingLayout from '@/Layouts/MarketingLayout.vue';
import Breadcrumbs from '@/Components/Breadcrumbs.vue';

const props = defineProps({
  post: {
    type: Object,
    required: true,
  },
});

const baseUrl = computed(() => usePage().props.app?.url ?? '');
const canonical = computed(() => props.post.canonical || `${baseUrl.value}/blog/${props.post.slug}`);

const breadcrumbs = computed(() => [
  { label: 'Inicio', href: '/' },
  { label: 'Blog', href: '/blog' },
  { label: props.post.title, href: `/blog/${props.post.slug}` },
]);

const articleJsonLd = computed(() => JSON.stringify({
  '@context': 'https://schema.org',
  '@type': 'Article',
  headline: props.post.title,
  description: props.post.meta_description || props.post.excerpt,
  datePublished: props.post.published_at,
  dateModified: props.post.published_at,
  author: {
    '@type': 'Organization',
    name: usePage().props.app?.name ?? 'KitchenManager',
  },
  mainEntityOfPage: canonical.value,
}));
</script>

<template>
  <MarketingLayout>
    <Head>
      <title>{{ props.post.meta_title || props.post.title }}</title>
      <meta
        name="description"
        :content="props.post.meta_description || props.post.excerpt"
      >
      <meta property="og:type" content="article">
      <meta property="og:title" :content="props.post.meta_title || props.post.title">
      <meta property="og:description" :content="props.post.meta_description || props.post.excerpt">
      <meta property="og:url" :content="canonical">
      <meta v-if="props.post.og_image" property="og:image" :content="props.post.og_image">
      <link rel="canonical" :href="canonical">
      <script type="application/ld+json" v-html="articleJsonLd" />
    </Head>

    <Breadcrumbs :items="breadcrumbs" />

    <article class="max-w-none">
      <div class="text-xs uppercase tracking-wide text-slate-400">{{ props.post.published_at }}</div>
      <h1 class="mt-2 text-4xl font-semibold text-slate-900">{{ props.post.title }}</h1>
      <p class="mt-4 text-lg text-slate-600">{{ props.post.excerpt }}</p>
      <div class="mt-8 space-y-4 text-base text-slate-700" v-html="props.post.content" />
    </article>

    <div class="mt-10 flex flex-wrap gap-3">
      <Link href="/blog" class="rounded-full border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-700 hover:border-slate-400">
        Volver al blog
      </Link>
      <Link href="/register" class="rounded-full bg-amber-500 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-600">
        Crear cuenta
      </Link>
    </div>
  </MarketingLayout>
</template>
