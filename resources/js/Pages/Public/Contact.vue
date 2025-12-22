<script setup>
import { computed } from 'vue';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/Public/SeoHead.vue';
import Breadcrumbs from '@/Components/Public/Breadcrumbs.vue';

const props = defineProps({
    baseUrl: String,
    canonical: String,
    title: String,
});

const description =
    'Contacta con KitchenManager para soporte, sugerencias o mejoras. Resolvemos dudas sobre inventario, caducidades y uso de la plataforma.';

const breadcrumbs = computed(() => [
    { name: 'Inicio', url: props.baseUrl },
    { name: 'Contacto', url: props.canonical },
]);
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="'Contacto | KitchenManager'"
            :description="description"
            :canonical="canonical"
        />

        <Breadcrumbs :items="breadcrumbs" />

        <section class="mt-8 space-y-6">
            <h1 class="text-3xl font-semibold">Contacto</h1>
            <p class="text-base text-[color:var(--km-muted)]">
                ¿Necesitas ayuda para organizar tu cocina o tienes sugerencias para la app? Escríbenos y te responderemos lo antes posible.
            </p>

            <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                <p class="text-sm text-[color:var(--km-muted)]">Correo de soporte</p>
                <p class="mt-1 text-base font-semibold text-[color:var(--km-text)]">soporte@kitchenmanager.duckdns.org</p>
                <p class="mt-3 text-sm text-[color:var(--km-muted)]">
                    Horario de respuesta: lunes a viernes, 9:00 - 18:00 (CET).
                </p>
            </div>

            <div class="rounded-2xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] p-6">
                <p class="text-sm text-[color:var(--km-muted)]">
                    Si prefieres, también puedes escribirnos con detalles de tu caso y adjuntar capturas.
                </p>
            </div>
        </section>
    </PublicLayout>
</template>
