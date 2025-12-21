<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthCard from '@/Components/AuthCard.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { useCsrfForm } from '@/Composables/useCsrfForm';

const form = useCsrfForm({ password: '' });

const page = usePage();
const username = computed(() => page.props.auth?.user?.email ?? page.props.auth?.user?.name ?? '');

const submit = () => {
  form.post(route('password.confirm'), {
    onFinish: () => form.reset('password'),
  });
};

</script>

<template>
  <GuestLayout>
    <Head title="Confirmar contraseña" />

    <AuthCard title="Confirmación requerida" subtitle="Por seguridad, confirma tu contraseña para continuar">
      <p v-if="username" class="mb-4 text-sm text-[color:var(--km-muted)]">
        Estás autenticado como <span class="font-semibold text-[color:var(--km-text)]">{{ username }}</span>
      </p>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label for="password" class="text-sm font-medium text-[color:var(--km-text)]">Contraseña</label>
          <input id="password" v-model="form.password" type="password" autocomplete="current-password" required class="km-input mt-2" />
          <p v-if="form.errors.password" class="mt-1 text-xs text-rose-600">
            {{ form.errors.password }}
          </p>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="km-btn"
        >
          <span v-if="form.processing">Confirmando…</span>
          <span v-else>Confirmar</span>
        </button>
      </form>
    </AuthCard>
  </GuestLayout>
</template>
