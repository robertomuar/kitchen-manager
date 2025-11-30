<script setup>
import { useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    sharedUsers: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('kitchen.share.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset('email'),
    });
};

const removeAccess = (shareId) => {
    if (!confirm('¿Seguro que quieres quitar el acceso a esta persona?')) {
        return;
    }

    form.delete(route('kitchen.share.destroy', shareId), {
        preserveScroll: true,
    });
};
</script>

<template>
    <section class="km-card p-6 sm:p-8 space-y-6">
        <header>
            <h2 class="text-xl font-semibold text-slate-50">
                Compartir tu cocina
            </h2>
            <p class="mt-1 text-sm text-slate-400">
                Invita a otra persona para que pueda ver y modificar tu stock
                desde su propia cuenta. Puedes revocar el acceso cuando quieras.
            </p>
        </header>

        <!-- Formulario de invitación -->
        <form @submit.prevent="submit" class="space-y-4 max-w-xl">
            <div>
                <InputLabel
                    for="share_email"
                    value="Correo electrónico del usuario"
                    class="text-slate-200"
                />

                <TextInput
                    id="share_email"
                    v-model="form.email"
                    type="email"
                    placeholder="ej: usuario@example.com"
                    class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                />

                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <div class="flex items-center gap-3">
                <PrimaryButton :disabled="form.processing" type="submit">
                    Compartir cocina
                </PrimaryButton>

                <p
                    v-if="form.recentlySuccessful"
                    class="text-xs text-emerald-300"
                >
                    Invitación guardada.
                </p>
            </div>
        </form>

        <!-- Lista de personas con acceso -->
        <div class="border-t border-slate-800/80 pt-5">
            <h3 class="text-sm font-semibold text-slate-200">
                Personas con acceso
            </h3>

            <p
                v-if="!sharedUsers.length"
                class="mt-2 text-sm text-slate-400"
            >
                De momento nadie más tiene acceso a tu cocina.
            </p>

            <ul v-else class="mt-3 space-y-2">
                <li
                    v-for="share in sharedUsers"
                    :key="share.id"
                    class="flex items-center justify-between rounded-xl border border-slate-800 bg-slate-950/60 px-3 py-2 text-sm"
                >
                    <div>
                        <p class="text-slate-100">
                            {{ share.user.name }}
                        </p>
                        <p class="text-xs text-slate-400">
                            {{ share.user.email }}
                        </p>
                    </div>

                    <button
                        type="button"
                        class="text-xs rounded-lg border border-rose-500/70 px-3 py-1 text-rose-200 hover:bg-rose-500/15"
                        @click="removeAccess(share.id)"
                    >
                        Quitar acceso
                    </button>
                </li>
            </ul>
        </div>
    </section>
</template>
