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
    posts: Object,
});

const description =
    'Consejos y guías para mejorar el control de stock en la cocina, reducir desperdicio y planificar compras inteligentes.';

const breadcrumbs = computed(() => [
    { name: 'Inicio', url: props.baseUrl },
    { name: 'Blog', url: props.canonical },
]);

const prevLink = computed(() => props.posts?.prev_page_url ?? null);
const nextLink = computed(() => props.posts?.next_page_url ?? null);
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="'Blog de KitchenManager'"
            :description="description"
            :canonical="canonical"
        />

        <Head>
            <link v-if="prevLink" rel="prev" :href="prevLink" />
            <link v-if="nextLink" rel="next" :href="nextLink" />
        </Head>

        <Breadcrumbs :items="breadcrumbs" />

        <section class="mt-8 space-y-6">
            <h1 class="text-3xl font-semibold">Blog</h1>
            <p class="text-base text-[color:var(--km-muted)]">
                Publicamos ideas prácticas para organizar tu despensa, gestionar caducidades y mejorar la planificación de compras.
            </p>

            <div class="grid gap-6 md:grid-cols-2">
                <article
                    v-for="post in posts.data"
                    :key="post.slug"
                    class="rounded-2xl border border-[color:var(--km-border)] p-6"
                >
                    <p class="text-xs text-[color:var(--km-muted)]">
                        {{ post.published_at }}
                    </p>
                    <h2 class="mt-2 text-lg font-semibold">
                        <Link :href="route('blog.show', post.slug)">{{ post.title }}</Link>
                    </h2>
                    <p class="mt-2 text-sm text-[color:var(--km-muted)]">
                        {{ post.excerpt }}
                    </p>
                    <Link :href="route('blog.show', post.slug)" class="km-link mt-3 inline-flex">
                        Leer artículo
                    </Link>
                </article>
            </div>

            <p v-if="posts.data.length === 0" class="text-sm text-[color:var(--km-muted)]">
                Aún no hay publicaciones. Vuelve pronto para leer nuevas guías.
            </p>

            <div v-if="posts.links?.length" class="flex flex-wrap gap-2">
                <Link
                    v-for="link in posts.links"
                    :key="link.url ?? link.label"
                    :href="link.url || ''"
                    class="rounded-full border border-[color:var(--km-border)] px-3 py-1 text-xs"
                    :class="{
                        'bg-[color:var(--km-bg-2)] text-[color:var(--km-text)]': link.active,
                        'text-[color:var(--km-muted)] pointer-events-none': !link.url,
                    }"
                    v-html="link.label"
                />
            </div>
        </section>
    </PublicLayout>
</template>
