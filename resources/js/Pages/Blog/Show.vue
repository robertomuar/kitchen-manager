<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/Public/SeoHead.vue';
import Breadcrumbs from '@/Components/Public/Breadcrumbs.vue';

const props = defineProps({
    baseUrl: String,
    canonical: String,
    title: String,
    post: Object,
});

const description = computed(() => props.post.meta_description || props.post.excerpt);

const breadcrumbs = computed(() => [
    { name: 'Inicio', url: props.baseUrl },
    { name: 'Blog', url: `${props.baseUrl}/blog` },
    { name: props.post.title, url: props.canonical },
]);

const articleJsonLd = computed(() => {
    return JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'Article',
        headline: props.post.title,
        description: description.value,
        datePublished: props.post.published_at,
        dateModified: props.post.updated_at || props.post.published_at,
        mainEntityOfPage: props.canonical,
        url: props.canonical,
        author: {
            '@type': 'Organization',
            name: 'KitchenManager',
        },
        publisher: {
            '@type': 'Organization',
            name: 'KitchenManager',
        },
        image: props.post.og_image || undefined,
    });
});
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="post.title"
            :description="description"
            :canonical="canonical"
            :og-image="post.og_image || ''"
        />

        <Head>
            <script type="application/ld+json" v-html="articleJsonLd"></script>
        </Head>

        <Breadcrumbs :items="breadcrumbs" />

        <article class="mt-8 space-y-6">
            <header class="space-y-3">
                <p class="text-xs text-[color:var(--km-muted)]">{{ post.published_at }}</p>
                <h1 class="text-3xl font-semibold">{{ post.title }}</h1>
                <p class="text-base text-[color:var(--km-muted)]">{{ post.excerpt }}</p>
            </header>

            <div class="space-y-4 text-base text-[color:var(--km-text)]" v-html="post.content"></div>

            <div class="rounded-2xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] p-6">
                <p class="text-sm text-[color:var(--km-muted)]">
                    ¿Quieres más ideas para organizar tu despensa? Revisa el blog completo.
                </p>
                <Link :href="route('blog.index')" class="km-link mt-2 inline-flex">
                    Ver más artículos
                </Link>
            </div>
        </article>
    </PublicLayout>
</template>
