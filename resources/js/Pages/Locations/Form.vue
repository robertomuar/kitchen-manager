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
                        <h1 class="text-2xl font-semibold text-slate-50">
                            {{ title }}
                        </h1>
                        <p class="mt-1 text-sm text-slate-400">
                            Configura una zona de tu cocina para poder asignarle
                            productos de ubicaciones y stock
                            (ej: Nevera, Congelador, Despensa superior…).
                        </p>
                    </div>

                    <Link
                        :href="route('locations.index')"
                        class="text-sm text-slate-300 hover:text-slate-100"
                    >
                        Volver al listado
                    </Link>
                </div>

                <!-- Tarjeta -->
                <div
                    class="mx-auto max-w-3xl rounded-2xl border border-slate-200 bg-white/95 p-6 shadow-xl shadow-slate-900/20 text-slate-900"
                >
                    <form class="space-y-6" @submit.prevent="submit">
                        <!-- Nombre -->
                        <div class="space-y-1">
                            <label
                                for="name"
                                class="block text-sm font-medium text-slate-800"
                            >
                                Nombre *
                            </label>
                            <input
                                id="name"
                                v-model="form.name"
                                type="text"
                                required
                                class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                       text-slate-900 placeholder-slate-400 shadow-sm
                                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
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
                                class="block text-sm font-medium text-slate-800"
                            >
                                Descripción (opcional)
                            </label>
                            <textarea
                                id="description"
                                v-model="form.description"
                                rows="3"
                                class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                       text-slate-900 placeholder-slate-400 shadow-sm
                                       focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
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
                                    class="block text-sm font-medium text-slate-800"
                                >
                                    Color (opcional)
                                </label>
                                <div class="flex items-center gap-3">
                                    <input
                                        id="color"
                                        v-model="form.color"
                                        type="color"
                                        class="h-10 w-16 cursor-pointer rounded-md border border-slate-300 bg-white"
                                    />
                                    <span class="text-xs text-slate-500">
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
                                    class="block text-sm font-medium text-slate-800"
                                >
                                    Orden (opcional)
                                </label>
                                <input
                                    id="sort_order"
                                    v-model="form.sort_order"
                                    type="number"
                                    min="0"
                                    class="block w-full rounded-md border border-slate-300 bg-white px-3 py-2 text-sm
                                           text-slate-900 placeholder-slate-400 shadow-sm
                                           focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/40 focus:outline-none"
                                    placeholder="Ej: 1, 2, 3…"
                                />
                                <p class="mt-1 text-xs text-slate-500">
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
                                    class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <span
                                    class="text-sm font-medium text-slate-800"
                                >
                                    Ubicación activa
                                </span>
                            </label>
                            <p class="text-xs text-slate-500">
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
                                class="inline-flex items-center justify-center rounded-xl bg-indigo-500 px-5 py-2.5 text-sm font-semibold text-white shadow-sm shadow-indigo-500/40 hover:bg-indigo-600 disabled:opacity-60 disabled:cursor-wait"
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
