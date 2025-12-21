<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthCard from '@/Components/AuthCard.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useCsrfForm } from '@/Composables/useCsrfForm';

const props = defineProps({
  email: { type: String, required: true },
  token: { type: String, required: true },
});

const form = useCsrfForm({
  token: props.token,
  email: props.email,
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('password.store'), {
    onFinish: () => form.reset('password', 'password_confirmation'),
  });
};

</script>

<template>
  <GuestLayout>
    <Head title="Restablecer contraseña" />

    <AuthCard title="Restablecer contraseña" subtitle="Elige una nueva contraseña segura">
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label for="email" class="text-sm font-medium text-[color:var(--km-text)]">Email</label>
          <input id="email" v-model="form.email" type="email" autocomplete="username" required class="km-input mt-2" />
          <p v-if="form.errors.email" class="mt-1 text-xs text-rose-600">
            {{ form.errors.email }}
          </p>
        </div>

        <div>
          <label for="password" class="text-sm font-medium text-[color:var(--km-text)]">Nueva contraseña</label>
          <input id="password" v-model="form.password" type="password" autocomplete="new-password" required class="km-input mt-2" />
          <p v-if="form.errors.password" class="mt-1 text-xs text-rose-600">
            {{ form.errors.password }}
          </p>
        </div>

        <div>
          <label for="password_confirmation" class="text-sm font-medium text-[color:var(--km-text)]">
            Confirmar contraseña
          </label>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            autocomplete="new-password"
            required
            class="km-input mt-2"
          />
          <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-rose-600">
            {{ form.errors.password_confirmation }}
          </p>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="km-btn"
        >
          <span v-if="form.processing">Guardando…</span>
          <span v-else>Guardar contraseña</span>
        </button>

        <div class="pt-1 text-center">
          <Link
            :href="route('login')"
            class="km-link text-sm"
          >
            Volver al login
          </Link>
        </div>
      </form>
    </AuthCard>
  </GuestLayout>
</template>
