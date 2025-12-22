<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/Public/SeoHead.vue';
import Breadcrumbs from '@/Components/Public/Breadcrumbs.vue';

const props = defineProps({
    baseUrl: String,
    canonical: String,
    title: String,
});

const description =
    'Resolvemos dudas frecuentes sobre KitchenManager: cómo gestionar inventario, caducidades, listas de compra y seguridad de tus datos.';

const breadcrumbs = computed(() => [
    { name: 'Inicio', url: props.baseUrl },
    { name: 'FAQ', url: props.canonical },
]);
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="'Preguntas frecuentes sobre KitchenManager'"
            :description="description"
            :canonical="canonical"
        />

        <Breadcrumbs :items="breadcrumbs" />

        <section class="mt-8 space-y-6">
            <h1 class="text-3xl font-semibold">Preguntas frecuentes</h1>
            <p class="text-base text-[color:var(--km-muted)]">
                Reunimos las dudas más habituales para que empieces a usar KitchenManager con seguridad. Si necesitas más ayuda, puedes escribirnos desde la página de contacto.
            </p>

            <div class="space-y-6">
                <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                    <h2 class="text-lg font-semibold">¿Puedo registrar productos con fechas de caducidad?</h2>
                    <p class="mt-2 text-sm text-[color:var(--km-muted)]">
                        Sí. Cada producto puede llevar fecha de vencimiento y la app los ordena para que consumas primero lo que caduca antes. Así reduces desperdicio y compras con mayor precisión.
                    </p>
                </div>
                <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                    <h2 class="text-lg font-semibold">¿KitchenManager funciona en móvil?</h2>
                    <p class="mt-2 text-sm text-[color:var(--km-muted)]">
                        Funciona desde el navegador del móvil sin instalar aplicaciones. Está optimizado para pantallas pequeñas y carga rápido incluso con conexiones lentas.
                    </p>
                </div>
                <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                    <h2 class="text-lg font-semibold">¿Cómo se crea la lista de compra?</h2>
                    <p class="mt-2 text-sm text-[color:var(--km-muted)]">
                        Puedes definir una cantidad mínima por producto. Cuando el stock baja de ese límite, aparece automáticamente en tu lista de reposición y puedes exportarla en CSV o PDF.
                    </p>
                </div>
                <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                    <h2 class="text-lg font-semibold">¿Mis datos son privados?</h2>
                    <p class="mt-2 text-sm text-[color:var(--km-muted)]">
                        Sí. Los datos del inventario se mantienen en tu cuenta y no se comparten. Además, las áreas privadas están protegidas y no se indexan en buscadores.
                    </p>
                </div>
                <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                    <h2 class="text-lg font-semibold">¿Puedo exportar mi inventario?</h2>
                    <p class="mt-2 text-sm text-[color:var(--km-muted)]">
                        Sí. Tienes exportaciones en CSV y PDF para imprimir o compartir. Es útil para preparar la compra o revisar la despensa con rapidez.
                    </p>
                </div>
            </div>

            <div class="rounded-3xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] p-6">
                <p class="text-sm text-[color:var(--km-muted)]">
                    ¿Tienes otra duda? Escríbenos y te ayudamos a organizar tu cocina.
                </p>
                <Link :href="route('contact')" class="km-link mt-2 inline-flex">Ir a contacto</Link>
            </div>
        </section>
    </PublicLayout>
</template>
