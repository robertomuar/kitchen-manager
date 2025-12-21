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

const inputClass =
  'mt-1 block w-full rounded-xl border border-slate-300 bg-white/80 px-3 py-2 text-sm shadow-sm outline-none transition ' +
  'focus:border-amber-500 focus:ring-4 focus:ring-amber-500/20 ' +
  'dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-100 ' +
  'dark:focus:border-amber-400 dark:focus:ring-amber-400/20';
</script>

<template>
  <GuestLayout>
    <Head title="Confirmar contraseña" />

    <AuthCard title="Confirmación requerida" subtitle="Por seguridad, confirma tu contraseña para continuar">
      <p v-if="username" class="mb-4 text-sm text-slate-600 dark:text-slate-300">
        Estás autenticado como <span class="font-semibold text-slate-900 dark:text-slate-100">{{ username }}</span>
      </p>

      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label for="password" class="text-sm font-medium text-slate-700 dark:text-slate-200">Contraseña</label>
          <input id="password" v-model="form.password" type="password" autocomplete="current-password" required :class="inputClass" />
          <p v-if="form.errors.password" class="mt-1 text-xs text-rose-600 dark:text-rose-300">
            {{ form.errors.password }}
          </p>
        </div>

        <button
          type="submit"
          :disabled="form.processing"
          class="inline-flex w-full items-center justify-center rounded-2xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm
                 transition hover:bg-amber-700 disabled:cursor-not-allowed disabled:opacity-60
                 dark:bg-amber-500 dark:hover:bg-amber-400"
        >
          <span v-if="form.processing">Confirmando…</span>
          <span v-else>Confirmar</span>
        </button>
      </form>
    </AuthCard>
  </GuestLayout>
</template>
