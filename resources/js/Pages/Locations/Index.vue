<script setup>
import { computed } from 'vue';
import { Head, usePage, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    locations: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();

const hasSuccessMessage = computed(() => {
    return Boolean(page.props.flash && page.props.flash.success);
});

const successMessage = computed(() => {
    return page.props.flash?.success ?? '';
});

const deleteLocation = (location) => {
    if (
        !confirm(
            `¿Seguro que quieres eliminar la ubicación "${location.name}"?`,
        )
    ) {
        return;
    }

    router.delete(route('locations.destroy', location.id), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <Head title="Ubicaciones" />

        <div class="py-8">
            <div class="mx-auto max-w-5xl sm:px-6 lg:px-8 space-y-6">
                <!-- Cabecera -->
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-semibold text-[color:var(--km-text)]">
                            Ubicaciones de mi cocina
                        </h1>
                        <p class="mt-1 text-sm text-[color:var(--km-muted)]">
                            Define las zonas donde tienes productos: despensa,
                            nevera, congelador, estanterías, etc.
                        </p>
                    </div>

                    <Link
                        :href="route('locations.create')"
                        class="km-btn w-auto px-4 py-2 text-sm"
                    >
                        Nueva ubicación
                    </Link>
                </div>

                <!-- Mensaje de éxito -->
                <div
                    v-if="hasSuccessMessage"
                    class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
                >
                    {{ successMessage }}
                </div>

                <!-- Tabla de ubicaciones -->
                <div class="km-card overflow-hidden">
                    <div
                        v-if="!locations.length"
                        class="p-6 text-center text-sm text-[color:var(--km-muted)]"
                    >
                        Todavía no tienes ubicaciones creadas. Crea al menos
                        una para empezar a organizar tu stock.
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="km-table">
                            <thead>
                                <tr>
                                    <th>
                                        Orden
                                    </th>
                                    <th>
                                        Nombre
                                    </th>
                                    <th>
                                        Descripción
                                    </th>
                                    <th>
                                        Color
                                    </th>
                                    <th>
                                        Estado
                                    </th>
                                    <th class="text-right">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="location in locations"
                                    :key="location.id"
                                >
                                    <!-- Orden -->
                                    <td
                                        class="whitespace-nowrap text-sm text-[color:var(--km-muted)]"
                                    >
                                        {{ location.sort_order ?? '—' }}
                                    </td>

                                    <!-- Nombre -->
                                    <td
                                        class="whitespace-nowrap text-sm font-medium text-[color:var(--km-text)]"
                                    >
                                        {{ location.name }}
                                    </td>

                                    <!-- Descripción -->
                                    <td
                                        class="whitespace-nowrap text-sm text-[color:var(--km-muted)] max-w-xs truncate"
                                        :title="location.description || ''"
                                    >
                                        {{ location.description || '—' }}
                                    </td>

                                    <!-- Color (solo la bolita, sin código) -->
                                    <td class="whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <span
                                                v-if="location.color"
                                                class="inline-flex h-4 w-4 rounded-full border border-[color:var(--km-border)]"
                                                :style="{
                                                    backgroundColor:
                                                        location.color,
                                                }"
                                            ></span>
                                            <span
                                                v-else
                                                class="text-xs text-[color:var(--km-muted)]"
                                            >
                                                —
                                            </span>
                                        </div>
                                    </td>

                                    <!-- Estado -->
                                    <td class="whitespace-nowrap text-sm">
                                        <span
                                            v-if="location.is_active"
                                            class="km-badge-green"
                                        >
                                            Activa
                                        </span>
                                        <span
                                            v-else
                                            class="km-badge-amber"
                                        >
                                            Inactiva
                                        </span>
                                    </td>

                                    <!-- Acciones -->
                                    <td
                                        class="whitespace-nowrap text-sm text-right"
                                    >
                                        <div class="inline-flex gap-2">
                                            <Link
                                                :href="
                                                    route(
                                                        'locations.edit',
                                                        location.id,
                                                    )
                                                "
                                                class="km-link text-xs"
                                            >
                                                Editar
                                            </Link>
                                            <button
                                                type="button"
                                                class="text-xs px-3 py-1 rounded-lg border border-rose-500/50 text-rose-600 hover:bg-rose-500/10"
                                                @click="
                                                    deleteLocation(location)
                                                "
                                            >
                                                Borrar
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
