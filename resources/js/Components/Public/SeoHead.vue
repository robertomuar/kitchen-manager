<script setup>
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        required: true,
    },
    canonical: {
        type: String,
        required: true,
    },
    robots: {
        type: String,
        default: 'index,follow',
    },
    ogImage: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <Head :title="title">
        <meta name="description" :content="description" />
        <meta name="robots" :content="robots" />
        <link rel="canonical" :href="canonical" />

        <meta property="og:title" :content="title" />
        <meta property="og:description" :content="description" />
        <meta property="og:type" content="website" />
        <meta property="og:url" :content="canonical" />
        <meta v-if="ogImage" property="og:image" :content="ogImage" />

        <meta
            name="twitter:card"
            :content="ogImage ? 'summary_large_image' : 'summary'"
        />
        <meta name="twitter:title" :content="title" />
        <meta name="twitter:description" :content="description" />
        <meta v-if="ogImage" name="twitter:image" :content="ogImage" />
    </Head>
</template>
