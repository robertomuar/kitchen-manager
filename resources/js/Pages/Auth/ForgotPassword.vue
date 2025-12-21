<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthCard from '@/Components/AuthCard.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useCsrfForm } from '@/Composables/useCsrfForm';

defineProps({
  status: { type: String, default: null },
});

const form = useCsrfForm({ email: '' });

const submit = () => {
  form.post(route('password.email'));
};

</script>

<template>
  <GuestLayout>
    <Head title="Recuperar contraseña" />

    <AuthCard title="Recuperar contraseña" subtitle="Te enviamos un enlace para restablecerla">
      <p class="mb-4 text-sm text-[color:var(--km-muted)]">
        Escribe tu email y te enviaremos un enlace para elegir una nueva contraseña.
      </p>

      <div
        v-if="status"
        class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-800"
      >
        {{ status }}
      </div>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label for="email" class="text-sm font-medium text-[color:var(--km-text)]">Email</label>
          <input id="email" v-model="form.email" type="email" autocomplete="username" required class="km-input mt-2" />
          <p v-if="form.errors.email" class="mt-1 text-xs text-rose-600">
            {{ form.errors.email }}
          </p>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="km-btn"
        >
          <span v-if="form.processing">Enviando…</span>
          <span v-else>Enviar enlace</span>
        </button>

        <div class="pt-1 text-center">
          <Link
            :href="route('login')"
            class="km-link text-sm"
          >
            Volver a iniciar sesión
          </Link>
        </div>
      </form>
    </AuthCard>
  </GuestLayout>
</template>
