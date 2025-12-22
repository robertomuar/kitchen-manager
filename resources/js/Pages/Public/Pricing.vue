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
    'KitchenManager es gratis en su versión base. Conoce lo que incluye hoy y las mejoras planificadas para planes futuros.';

const breadcrumbs = computed(() => [
    { name: 'Inicio', url: props.baseUrl },
    { name: 'Precios', url: props.canonical },
]);
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="'Precios de KitchenManager'"
            :description="description"
            :canonical="canonical"
        />

        <Breadcrumbs :items="breadcrumbs" />

        <section class="mt-8 space-y-6">
            <h1 class="text-3xl font-semibold">Planes y precios</h1>
            <p class="text-base text-[color:var(--km-muted)]">
                KitchenManager está pensado para ayudarte desde el primer día sin coste. El plan gratuito incluye las funcionalidades clave para gestionar tu inventario doméstico. En el futuro podremos ofrecer planes con más automatizaciones, pero hoy puedes empezar sin compromiso.
            </p>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                    <h2 class="text-xl font-semibold">Plan Gratis</h2>
                    <p class="mt-2 text-sm text-[color:var(--km-muted)]">
                        Ideal para hogares que quieren organizar su despensa y reducir desperdicio.
                    </p>
                    <ul class="mt-4 space-y-2 text-sm text-[color:var(--km-muted)]">
                        <li>• Inventario ilimitado de productos y ubicaciones.</li>
                        <li>• Alertas de caducidad y mínimos de stock.</li>
                        <li>• Exportaciones en CSV y PDF.</li>
                        <li>• Acceso desde móvil y escritorio.</li>
                    </ul>
                    <Link :href="route('register')" class="km-btn mt-4 inline-flex w-auto px-5 py-2">
                        Crear cuenta gratis
                    </Link>
                </div>
                <div class="rounded-2xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] p-6">
                    <h2 class="text-xl font-semibold">Próximos planes</h2>
                    <p class="mt-2 text-sm text-[color:var(--km-muted)]">
                        Estamos preparando funciones avanzadas para quienes necesiten más automatización.
                    </p>
                    <ul class="mt-4 space-y-2 text-sm text-[color:var(--km-muted)]">
                        <li>• Compartir cocinas entre varios usuarios.</li>
                        <li>• Historial de consumo y reportes mensuales.</li>
                        <li>• Integraciones con escáner o códigos de barras.</li>
                        <li>• Soporte prioritario para equipos familiares.</li>
                    </ul>
                    <Link :href="route('contact')" class="km-link mt-4 inline-flex">
                        Quiero enterarme primero
                    </Link>
                </div>
            </div>

            <div class="rounded-3xl border border-[color:var(--km-border)] p-6 text-sm text-[color:var(--km-muted)]">
                ¿Tienes dudas sobre el plan gratuito? Escríbenos y te ayudamos a decidir.
            </div>
        </section>
    </PublicLayout>
</template>
