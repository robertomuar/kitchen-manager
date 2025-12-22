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
    'Términos y condiciones de KitchenManager para el uso del inventario de cocina, cuentas y servicios relacionados.';

const breadcrumbs = computed(() => [
    { name: 'Inicio', url: props.baseUrl },
    { name: 'Términos', url: props.canonical },
]);
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="'Términos y condiciones | KitchenManager'"
            :description="description"
            :canonical="canonical"
        />

        <Breadcrumbs :items="breadcrumbs" />

        <section class="mt-8 space-y-6">
            <h1 class="text-3xl font-semibold">Términos y condiciones</h1>
            <p class="text-base text-[color:var(--km-muted)]">
                Al usar KitchenManager aceptas estos términos. Nuestro objetivo es ofrecer un servicio estable y seguro, por lo que necesitamos ciertas reglas básicas.
            </p>

            <div class="space-y-4 text-sm text-[color:var(--km-muted)]">
                <div>
                    <h2 class="text-base font-semibold text-[color:var(--km-text)]">Uso permitido</h2>
                    <p class="mt-1">
                        Puedes utilizar la plataforma para gestionar tu inventario doméstico. No está permitido el uso con fines ilegales ni el acceso no autorizado a cuentas de terceros.
                    </p>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-[color:var(--km-text)]">Disponibilidad</h2>
                    <p class="mt-1">
                        Hacemos lo posible por mantener el servicio disponible, pero pueden existir interrupciones por mantenimiento o actualizaciones. Avisaremos cuando sea posible.
                    </p>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-[color:var(--km-text)]">Responsabilidad</h2>
                    <p class="mt-1">
                        KitchenManager proporciona herramientas para organizar información, pero no se responsabiliza de decisiones tomadas a partir de los datos introducidos por el usuario.
                    </p>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-[color:var(--km-text)]">Cambios</h2>
                    <p class="mt-1">
                        Podemos actualizar estos términos para mejorar el servicio. Recomendamos revisarlos periódicamente.
                    </p>
                </div>
            </div>
        </section>
    </PublicLayout>
</template>
