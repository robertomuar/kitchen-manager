<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useCsrfForm } from '@/Composables/useCsrfForm';
import { computed } from 'vue';

const form = useCsrfForm({
    password: '',
});

const username = computed(
    () => usePage().props.auth.user?.email ?? usePage().props.auth.user?.name ?? ''
);

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <div class="mb-4 text-sm text-gray-600">
            This is a secure area of the application. Please confirm your
            password before continuing.
        </div>

        <form @submit.prevent="submit">
            <input
                type="text"
                name="username"
                :value="username"
                autocomplete="username"
                class="sr-only"
                tabindex="-1"
                aria-hidden="true"
            />

            <div>
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 flex justify-end">
                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                    type="submit"
                >
                    Confirm
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>
