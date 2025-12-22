<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  items: {
    type: Array,
    required: true,
  },
});

const baseUrl = computed(() => usePage().props.app?.url ?? '');

const jsonLd = computed(() => {
  const itemListElement = props.items.map((item, index) => ({
    '@type': 'ListItem',
    position: index + 1,
    name: item.label,
    item: `${baseUrl.value}${item.href}`,
  }));

  return JSON.stringify({
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement,
  });
});
</script>

<template>
  <Head>
    <script type="application/ld+json" v-html="jsonLd" />
  </Head>
  <nav aria-label="Breadcrumb" class="mb-6 text-sm text-slate-500">
    <ol class="flex flex-wrap items-center gap-2">
      <li v-for="(item, index) in items" :key="item.href" class="flex items-center gap-2">
        <Link v-if="index < items.length - 1" :href="item.href" class="hover:text-slate-700">
          {{ item.label }}
        </Link>
        <span v-else class="text-slate-700">{{ item.label }}</span>
        <span v-if="index < items.length - 1" aria-hidden="true">/</span>
      </li>
    </ol>
  </nav>
</template>
