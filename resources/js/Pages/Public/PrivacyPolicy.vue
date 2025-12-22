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
    'Política de privacidad de KitchenManager: cómo usamos y protegemos los datos de tu inventario de cocina.';

const breadcrumbs = computed(() => [
    { name: 'Inicio', url: props.baseUrl },
    { name: 'Privacidad', url: props.canonical },
]);
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="'Política de privacidad | KitchenManager'"
            :description="description"
            :canonical="canonical"
        />

        <Breadcrumbs :items="breadcrumbs" />

        <section class="mt-8 space-y-6">
            <h1 class="text-3xl font-semibold">Política de privacidad</h1>
            <p class="text-base text-[color:var(--km-muted)]">
                En KitchenManager nos tomamos la privacidad en serio. Esta política explica qué datos recopilamos, cómo los usamos y qué derechos tienes sobre tu información.
            </p>

            <div class="space-y-4 text-sm text-[color:var(--km-muted)]">
                <div>
                    <h2 class="text-base font-semibold text-[color:var(--km-text)]">Datos que almacenamos</h2>
                    <p class="mt-1">
                        Guardamos la información que introduces en tu cuenta: productos, ubicaciones, cantidades y fechas de caducidad. Estos datos se usan únicamente para ofrecerte la funcionalidad de inventario.
                    </p>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-[color:var(--km-text)]">Finalidad</h2>
                    <p class="mt-1">
                        Utilizamos tus datos para mostrar tu inventario, generar listas de compra y avisarte cuando un producto está por acabar o caducar. No vendemos ni compartimos tu información con terceros.
                    </p>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-[color:var(--km-text)]">Seguridad</h2>
                    <p class="mt-1">
                        Aplicamos medidas de seguridad para proteger los datos. Las áreas privadas no se indexan y requieren autenticación.
                    </p>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-[color:var(--km-text)]">Tus derechos</h2>
                    <p class="mt-1">
                        Puedes solicitar la modificación o eliminación de tus datos en cualquier momento. Escríbenos desde la página de contacto.
                    </p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
