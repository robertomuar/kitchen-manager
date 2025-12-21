<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import { useCsrfForm } from '@/Composables/useCsrfForm';

const props = defineProps({
    location: {
        type: Object,
        default: null,
    },
    mode: {
        type: String,
        default: 'create', // 'create' | 'edit'
    },
});

const isEdit = computed(() => props.mode === 'edit' || !!props.location?.id);

const form = useCsrfForm({
    name: props.location?.name ?? '',
    description: props.location?.description ?? '',
    color: props.location?.color ?? '#000000',
    sort_order: props.location?.sort_order ?? '',
    is_active: props.location?.is_active ?? true,
});

const title = computed(() =>
    isEdit.value ? 'Editar ubicación' : 'Nueva ubicación',
);

const submitLabel = computed(() =>
    isEdit.value ? 'Guardar cambios' : 'Crear ubicación',
);

const submit = () => {
    if (isEdit.value) {
        form.put(route('locations.update', props.location.id));
    } else {
        form.post(route('locations.store'));
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <Head :title="title" />

        <div class="py-10">
            <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 space-y-6">
                <!-- Cabecera -->
                <div
                    class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h1 class="text-2xl font-semibold text-[color:var(--km-text)]">
                            {{ title }}
                        </h1>
                        <p class="mt-1 text-sm text-[color:var(--km-muted)]">
                            Configura una zona de tu cocina para poder asignarle
                            productos de ubicaciones y stock
                            (ej: Nevera, Congelador, Despensa superior…).
                        </p>
                    </div>

                    <Link
                        :href="route('locations.index')"
                        class="km-link text-sm"
                    >
                        Volver al listado
                    </Link>
                </div>

                <!-- Tarjeta -->
                <div class="mx-auto max-w-3xl p-6 km-card">
                    <form class="space-y-6" @submit.prevent="submit">
                        <!-- Nombre -->
                        <div class="space-y-1">
                            <label
                                for="name"
                                class="block text-sm font-medium text-[color:var(--km-text)]"
                            >
                                Nombre *
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="km-input"
                                placeholder="Armario aceite, Nevera, Despensa superior…"
                            />
                            <InputError
                                class="mt-1 text-xs text-rose-600"
                                :message="form.errors.name"
                            />
                        </div>

                        <!-- Descripción -->
                        <div class="space-y-1">
                            <label
                                for="description"
                                class="block text-sm font-medium text-[color:var(--km-text)]"
                            >
                                Descripción (opcional)
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="km-input"
                                placeholder="Ej: Estantería de arriba de la despensa, lado izquierdo."
                            ></textarea>
                            <InputError
                                class="mt-1 text-xs text-rose-600"
                                :message="form.errors.description"
                            />
                        </div>

                        <!-- Color y Orden -->
                        <div class="grid gap-6 sm:grid-cols-2">
                            <!-- Color -->
                            <div class="space-y-2">
                                <label
                                    for="color"
                                    class="block text-sm font-medium text-[color:var(--km-text)]"
                                >
                                    Color (opcional)
                                </label>
                                <div class="flex items-center gap-3">
                                    <input
                                        id="color"
                                        v-model="form.color"
                                        type="color"
                                        class="h-10 w-16 cursor-pointer rounded-md border border-[color:var(--km-border)] bg-white"
                                    />
                                    <span class="text-xs text-[color:var(--km-muted)]">
                                        Solo para ayudarte a identificar
                                        visualmente esta ubicación (puedes
                                        dejarlo vacío).
                                    </span>
                                </div>
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.color"
                                />
                            </div>

                            <!-- Orden -->
                            <div class="space-y-2">
                                <label
                                    for="sort_order"
                                    class="block text-sm font-medium text-[color:var(--km-text)]"
                                >
                                    Orden (opcional)
                                </label>
                                <input
                                    id="sort_order"
                                    v-model="form.sort_order"
                                    type="number"
                                    min="0"
                                    class="km-input"
                                    placeholder="Ej: 1, 2, 3…"
                                />
                                <p class="mt-1 text-xs text-[color:var(--km-muted)]">
                                    Si lo rellenas, las ubicaciones se
                                    ordenarán por este número. Si lo dejas
                                    vacío, se colocará automáticamente al final.
                                </p>
                                <InputError
                                    class="mt-1 text-xs text-rose-600"
                                    :message="form.errors.sort_order"
                                />
                            </div>
                        </div>

                        <!-- Activa / Inactiva -->
                        <div class="space-y-2">
                            <label class="inline-flex items-center gap-2">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-[color:var(--km-border)] text-[color:var(--km-accent)] focus:ring-[color:var(--km-ring)]"
                                />
                                <span
                                    class="text-sm font-medium text-[color:var(--km-text)]"
                                >
                                    Ubicación activa
                                </span>
                            </label>
                            <p class="text-xs text-[color:var(--km-muted)]">
                                Si en algún momento dejas de usar esta
                                ubicación, puedes marcarla como inactiva en
                                lugar de borrarla.
                            </p>
                            <InputError
                                class="mt-1 text-xs text-rose-600"
                                :message="form.errors.is_active"
                            />
                        </div>

                        <!-- Botón -->
                        <div class="pt-4 flex justify-end">
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="km-btn w-auto px-5 py-2.5 text-sm disabled:cursor-wait"
                            >
                                {{ submitLabel }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
