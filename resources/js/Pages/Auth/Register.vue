<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthCard from '@/Components/AuthCard.vue';
import { Head, Link } from '@inertiajs/vue3';
import { useCsrfForm } from '@/Composables/useCsrfForm';

const form = useCsrfForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const submit = () => {
  form.post(route('register'), {
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
    <Head title="Registro" />

    <AuthCard title="Crear cuenta" subtitle="Empieza a organizar tu cocina en minutos">
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label for="name" class="text-sm font-medium text-slate-700 dark:text-slate-200">Nombre</label>
          <input id="name" v-model="form.name" type="text" autocomplete="name" required :class="inputClass" />
          <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600 dark:text-rose-300">
            {{ form.errors.name }}
          </p>
        </div>

        <div>
          <label for="email" class="text-sm font-medium text-slate-700 dark:text-slate-200">Email</label>
          <input id="email" v-model="form.email" type="email" autocomplete="username" required :class="inputClass" />
          <p v-if="form.errors.email" class="mt-1 text-xs text-rose-600 dark:text-rose-300">
            {{ form.errors.email }}
          </p>
        </div>

        <div>
          <label for="password" class="text-sm font-medium text-slate-700 dark:text-slate-200">Contraseña</label>
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
          <span v-if="form.processing">Creando…</span>
          <span v-else>Crear cuenta</span>
        </button>
      </form>

      <template #footer>
        <div class="flex items-center justify-between">
          <span>¿Ya tienes cuenta?</span>
          <Link
            :href="route('login')"
            class="font-semibold text-amber-700 hover:text-amber-800 dark:text-amber-300 dark:hover:text-amber-200"
          >
            Iniciar sesión
          </Link>
        </div>
      </template>
    </AuthCard>
  </GuestLayout>
</template>
