<script setup>
import { ref, nextTick, computed } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';

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

const username = computed(
    () => usePage().props.auth.user?.email ?? usePage().props.auth.user?.name ?? ''
);

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    deletionError.value = '';

    form.reset('password');

    nextTick(() => {
        passwordInput.value?.focus();
    });
};

const deleteUser = () => {
    if (form.processing) return;

    if (!form.password?.trim()) {
        deletionError.value = 'Introduce tu contraseña para eliminar tu cuenta.';

        nextTick(() => passwordInput.value?.focus());
        return;
    }

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
        onFinish: () => form.reset('password'),
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
            <DangerButton type="button" @click="confirmUserDeletion">
                Eliminar cuenta
            </DangerButton>
        </div>

        <!-- Modal de confirmación -->
        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <template #title>
                Eliminar cuenta
            </template>

            <template #content>
                <form
                    class="space-y-4 p-6 sm:p-8"
                    aria-describedby="delete-account-description"
                    @submit.prevent="deleteUser"
                >
                    <input
                        type="text"
                        name="username"
                        :value="username"
                        autocomplete="username"
                        class="sr-only"
                        tabindex="-1"
                        aria-hidden="true"
                    />

                    <p id="delete-account-description" class="text-sm text-slate-200">
                        ¿Seguro que quieres eliminar tu cuenta? Una vez eliminada,
                        todos tus datos serán borrados de forma permanente.
                    </p>

                    <div>
                        <InputLabel
                            for="password"
                            value="Contraseña"
                            class="text-rose-50"
                        />

                        <TextInput
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            class="mt-1"
                            variant="dark"
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
                            :disabled="form.processing"
                            :aria-busy="form.processing"
                        >
                            <span v-if="form.processing">Eliminando...</span>
                            <span v-else>Eliminar cuenta</span>
                        </DangerButton>
                    </div>
                </form>
            </template>
        </Modal>
    </div>
</template>
