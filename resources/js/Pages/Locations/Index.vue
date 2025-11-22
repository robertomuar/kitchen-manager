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
                        <h1 class="text-2xl font-semibold text-slate-50">
                            Ubicaciones de mi cocina
                        </h1>
                        <p class="mt-1 text-sm text-slate-400">
                            Define las zonas donde tienes productos: despensa,
                            nevera, congelador, estanterías, etc.
                        </p>
                    </div>

                    <Link
                        :href="route('locations.create')"
                        class="inline-flex items-center rounded-xl border border-indigo-500/70 bg-indigo-500/15 px-4 py-2 text-sm font-medium text-indigo-100 shadow-sm shadow-indigo-500/30 hover:bg-indigo-500/25"
                    >
                        Nueva ubicación
                    </Link>
                </div>

                <!-- Mensaje de éxito -->
                <div
                    v-if="hasSuccessMessage"
                    class="rounded-2xl border border-emerald-500/60 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200 shadow-sm shadow-emerald-500/30"
                >
                    {{ successMessage }}
                </div>

                <!-- Tabla de ubicaciones -->
                <div class="km-card overflow-hidden">
                    <div
                        v-if="!locations.length"
                        class="p-6 text-center text-sm text-slate-400"
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
                                        class="whitespace-nowrap text-sm text-slate-400"
                                    >
                                        {{ location.sort_order ?? '—' }}
                                    </td>

                                    <!-- Nombre -->
                                    <td
                                        class="whitespace-nowrap text-sm font-medium text-slate-50"
                                    >
                                        {{ location.name }}
                                    </td>

                                    <!-- Descripción -->
                                    <td
                                        class="whitespace-nowrap text-sm text-slate-400 max-w-xs truncate"
                                        :title="location.description || ''"
                                    >
                                        {{ location.description || '—' }}
                                    </td>

                                    <!-- Color (solo la bolita, sin código) -->
                                    <td class="whitespace-nowrap text-sm">
                                        <div class="flex items-center gap-2">
                                            <span
                                                v-if="location.color"
                                                class="inline-flex h-4 w-4 rounded-full border border-slate-700"
                                                :style="{
                                                    backgroundColor:
                                                        location.color,
                                                }"
                                            ></span>
                                            <span
                                                v-else
                                                class="text-xs text-slate-500"
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
                                                class="text-xs px-3 py-1 rounded-lg border border-slate-600/80 text-slate-100 hover:bg-slate-800/80"
                                            >
                                                Editar
                                            </Link>
                                            <button
                                                type="button"
                                                class="text-xs px-3 py-1 rounded-lg border border-rose-500/70 text-rose-300 hover:bg-rose-500/15"
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
