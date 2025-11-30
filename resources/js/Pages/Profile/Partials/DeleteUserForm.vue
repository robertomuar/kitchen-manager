<script setup>
import { ref, nextTick } from 'vue';
import { useForm } from '@inertiajs/vue3';

import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const deletionError = ref('');

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    deletionError.value = '';

    nextTick(() => {
        passwordInput.value?.focus();
    });
};

const deleteUser = () => {
    if (form.processing) return;

    deletionError.value = '';

    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => {
            deletionError.value =
                form.errors.password ??
                'No pudimos eliminar tu cuenta. Comprueba la contraseña e inténtalo de nuevo.';
            passwordInput.value?.focus();
        },
        onFinish: () => form.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    form.reset();
    deletionError.value = '';
};
</script>

<template>
    <!-- Texto principal va en Show.vue, aquí solo la acción -->
    <div class="space-y-4 max-w-xl">
        <div>
            <DangerButton @click="confirmUserDeletion">
                Eliminar cuenta
            </DangerButton>
        </div>

        <!-- Modal de confirmación -->
        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <template #title>
                Eliminar cuenta
            </template>

            <template #content>
                <form class="space-y-4" @submit.prevent="deleteUser">
                    <p class="text-sm text-slate-300">
                        ¿Seguro que quieres eliminar tu cuenta? Una vez eliminada,
                        todos tus datos serán borrados de forma permanente.
                    </p>

                    <div>
                        <InputLabel
                            for="password"
                            value="Contraseña"
                            class="text-slate-200"
                        />

                        <TextInput
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            class="mt-1 block w-full rounded-xl border border-slate-700 bg-slate-950/70 px-3 py-2 text-sm text-slate-100 placeholder:text-slate-500 shadow-sm focus:border-rose-500 focus:outline-none focus:ring-1 focus:ring-rose-500"
                            placeholder="Introduce tu contraseña para confirmar"
                        />

                        <InputError
                            :message="form.errors.password || deletionError"
                            class="mt-2"
                        />
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-1">
                        <SecondaryButton type="button" @click="closeModal">
                            Cancelar
                        </SecondaryButton>

                        <DangerButton
                            type="submit"
                            class="ml-2"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Eliminar cuenta
                        </DangerButton>
                    </div>
                </form>
            </template>
        </Modal>
    </div>
</template>
