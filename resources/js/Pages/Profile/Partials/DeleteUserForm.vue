<script setup>
import { ref, nextTick, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import Modal from '@/Components/Modal.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { useCsrfForm } from '@/Composables/useCsrfForm';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const deletionError = ref('');
const formId = 'delete-account-form';

const form = useCsrfForm({
    password: '',
});

const username = computed(
    () => usePage().props.auth.user?.email ?? usePage().props.auth.user?.name ?? ''
);

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
                    :id="formId"
                    class="space-y-4"
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

                    <div class="space-y-1">
                        <p class="text-sm text-[color:var(--km-text)]">
                            Esta acción es definitiva. Si eliminas tu cuenta se
                            borrarán todas tus ubicaciones, productos y stock
                            asociados.
                        </p>
                        <p class="text-sm text-amber-700/90">
                            Escribe tu contraseña para confirmar que eres la
                            persona titular.
                        </p>
                    </div>

                    <div class="space-y-2">
                        <InputLabel
                            for="password"
                            value="Contraseña"
                        />

                        <TextInput
                            id="password"
                            ref="passwordInput"
                            v-model="form.password"
                            type="password"
                            autocomplete="current-password"
                            class="mt-1"
                            placeholder="Introduce tu contraseña para confirmar"
                        />

                        <InputError
                            :message="form.errors.password || deletionError"
                            class="mt-1"
                        />
                    </div>
                </form>
            </template>

            <template #footer>
                <div class="flex flex-wrap items-center justify-end gap-3">
                    <SecondaryButton type="button" @click="closeModal">
                        Cancelar
                    </SecondaryButton>

                    <DangerButton
                        :form="formId"
                        type="submit"
                        class="ml-0 sm:ml-2"
                        :disabled="form.processing"
                    >
                        Eliminar cuenta
                    </DangerButton>
                </div>
            </template>
        </Modal>
    </div>
</template>
