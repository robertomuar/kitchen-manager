<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import AuthCard from '@/Components/AuthCard.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  canResetPassword: { type: Boolean, default: true },
  status: { type: String, default: null },
});

const page = usePage();
const csrfToken = computed(() => page.props.csrf_token ?? page.props.csrfToken ?? '');

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <GuestLayout :busy="form.processing">
    <Head title="Iniciar sesión" />

    <AuthCard
      title="Bienvenido"
      subtitle="Accede a tu cuenta para gestionar tu cocina"
    >
      <div v-if="props.status" class="mb-4 text-sm font-semibold" style="color: rgba(34,197,94,.90)">
        {{ props.status }}
      </div>

      <!-- ✅ Sub-card con volumen (el “form” no se ve plano) -->
      <div class="relative km-surface p-5 sm:p-6">
        <form @submit.prevent="submit" class="space-y-4">
          <div>
            <InputLabel for="email" value="Email" />
            <input
              id="email"
              type="email"
              class="km-input mt-2"
              v-model="form.email"
              autocomplete="username"
              required
              autofocus
            />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>

          <div>
            <InputLabel for="password" value="Contraseña" />
            <input
              id="password"
              type="password"
              class="km-input mt-2"
              v-model="form.password"
              autocomplete="current-password"
              required
            />
            <InputError class="mt-2" :message="form.errors.password" />
          </div>

          <div class="flex items-center justify-between gap-3">
            <label class="inline-flex items-center gap-2">
              <input
                type="checkbox"
                class="h-4 w-4 rounded border-[color:var(--km-border)] text-[color:var(--km-accent)] focus:ring-[color:var(--km-ring)]"
                v-model="form.remember"
              />
              <span class="text-sm" style="color: var(--km-muted)">Recuérdame</span>
            </label>

            <Link
              v-if="props.canResetPassword"
              :href="route('password.request')"
              class="text-sm font-semibold"
              style="color: var(--km-accent)"
            >
              ¿Olvidaste la contraseña?
            </Link>
          </div>

          <button type="submit" class="km-btn" :disabled="form.processing">
            Entrar
          </button>
        </form>
      </div>

      <template #footer>
        <div class="flex items-center justify-between">
          <span>¿No tienes cuenta?</span>
          <Link :href="route('register')" class="km-link">Crear cuenta</Link>
        </div>
      </template>
    </AuthCard>
  </GuestLayout>
</template>
