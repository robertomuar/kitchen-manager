<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useCsrfForm } from '@/Composables/useCsrfForm';

const props = defineProps({
    sharedUsers: {
        type: Array,
        default: () => [],
    },
});

const form = useCsrfForm({
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
            <h2 class="text-xl font-semibold text-[color:var(--km-text)]">
                Compartir tu cocina
            </h2>
            <p class="mt-1 text-sm text-[color:var(--km-muted)]">
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
                />

                <TextInput
                    id="share_email"
                    v-model="form.email"
                    type="email"
                    placeholder="ej: usuario@example.com"
                    class="mt-1 block w-full"
                />

                <InputError :message="form.errors.email" class="mt-2" />
            </div>

            <div class="flex items-center gap-3">
                <PrimaryButton :disabled="form.processing" type="submit">
                    Compartir cocina
                </PrimaryButton>

                <p
                    v-if="form.recentlySuccessful"
                    class="text-xs text-emerald-600"
                >
                    Invitación guardada.
                </p>
            </div>
        </form>

        <!-- Lista de personas con acceso -->
        <div class="pt-5">
            <div class="km-divider" />
            <h3 class="text-sm font-semibold text-[color:var(--km-text)]">
                Personas con acceso
            </h3>

            <p
                v-if="!sharedUsers.length"
                class="mt-2 text-sm text-[color:var(--km-muted)]"
            >
                De momento nadie más tiene acceso a tu cocina.
            </p>

            <ul v-else class="mt-3 space-y-2">
                <li
                    v-for="share in sharedUsers"
                    :key="share.id"
                    class="flex items-center justify-between rounded-xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] px-3 py-2 text-sm"
                >
                    <div>
                        <p class="text-[color:var(--km-text)]">
                            {{ share.user.name }}
                        </p>
                        <p class="text-xs text-[color:var(--km-muted)]">
                            {{ share.user.email }}
                        </p>
                    </div>

                    <button
                        type="button"
                        class="text-xs rounded-lg border border-rose-500/50 px-3 py-1 text-rose-600 hover:bg-rose-500/10"
                        @click="removeAccess(share.id)"
                    >
                        Quitar acceso
                    </button>
                </li>
            </ul>
        </div>
    </section>
</template>
