<script setup>
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    items: {
        type: Array,
        required: true,
    },
});

const jsonLd = computed(() => {
    const itemListElement = props.items.map((item, index) => ({
        '@type': 'ListItem',
        position: index + 1,
        name: item.name,
        item: item.url,
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
        <script type="application/ld+json" v-html="jsonLd"></script>
    </Head>

    <nav aria-label="Breadcrumb" class="text-sm text-[color:var(--km-muted)]">
        <ol class="flex flex-wrap items-center gap-2">
            <li v-for="(item, index) in items" :key="item.url" class="flex items-center gap-2">
                <span v-if="index > 0">/</span>
                <span v-if="index === items.length - 1" class="font-semibold text-[color:var(--km-text)]">
                    {{ item.name }}
                </span>
                <a v-else :href="item.url" class="hover:text-[color:var(--km-text)]">
                    {{ item.name }}
                </a>
            </li>
        </ol>
    </nav>
</template>
