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

const inputClass =
  'mt-1 block w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm shadow-sm outline-none transition ' +
  'focus:border-amber-500 focus:ring-4 focus:ring-amber-500/20 ' +
  'dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-100 ' +
  'dark:focus:border-amber-400 dark:focus:ring-amber-400/20';
</script>

<template>
  <GuestLayout>
    <Head title="Restablecer contraseña" />

    <AuthCard title="Restablecer contraseña" subtitle="Elige una nueva contraseña segura">
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label for="email" class="text-sm font-medium text-slate-700 dark:text-slate-200">Email</label>
          <input id="email" v-model="form.email" type="email" autocomplete="username" required :class="inputClass" />
          <p v-if="form.errors.email" class="mt-1 text-xs text-rose-600 dark:text-rose-300">
            {{ form.errors.email }}
          </p>
        </div>

        <div>
          <label for="password" class="text-sm font-medium text-slate-700 dark:text-slate-200">Nueva contraseña</label>
          <input id="password" v-model="form.password" type="password" autocomplete="new-password" required :class="inputClass" />
          <p v-if="form.errors.password" class="mt-1 text-xs text-rose-600 dark:text-rose-300">
            {{ form.errors.password }}
          </p>
        </div>

        <div>
          <label for="password_confirmation" class="text-sm font-medium text-slate-700 dark:text-slate-200">
            Confirmar contraseña
          </label>
          <input
            id="password_confirmation"
            v-model="form.password_confirmation"
            type="password"
            autocomplete="new-password"
            required
            :class="inputClass"
          />
          <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-rose-600 dark:text-rose-300">
            {{ form.errors.password_confirmation }}
          </p>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="inline-flex w-full items-center justify-center rounded-2xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                 transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-60
                 dark:bg-amber-500 dark:hover:bg-amber-400"
        >
          <span v-if="form.processing">Guardando…</span>
          <span v-else>Guardar contraseña</span>
        </button>

        <div class="pt-1 text-center">
          <Link
            :href="route('login')"
            class="text-sm font-medium text-slate-700 hover:text-slate-900 dark:text-slate-200 dark:hover:text-white"
          >
            Volver al login
          </Link>
        </div>
      </form>
    </AuthCard>
  </GuestLayout>
</template>
