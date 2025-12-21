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

</script>

<template>
  <GuestLayout>
    <Head title="Registro" />

    <AuthCard title="Crear cuenta" subtitle="Empieza a organizar tu cocina en minutos">
      <form @submit.prevent="submit" class="space-y-4">
        <div>
          <label for="name" class="text-sm font-medium text-[color:var(--km-text)]">Nombre</label>
          <input id="name" v-model="form.name" type="text" autocomplete="name" required class="km-input mt-2" />
          <p v-if="form.errors.name" class="mt-1 text-xs text-rose-600">
            {{ form.errors.name }}
          </p>
        </div>

        <div>
          <label for="email" class="text-sm font-medium text-[color:var(--km-text)]">Email</label>
          <input id="email" v-model="form.email" type="email" autocomplete="username" required class="km-input mt-2" />
          <p v-if="form.errors.email" class="mt-1 text-xs text-rose-600">
            {{ form.errors.email }}
          </p>
        </div>

        <div>
          <label for="password" class="text-sm font-medium text-[color:var(--km-text)]">Contraseña</label>
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
          <span v-if="form.processing">Creando…</span>
          <span v-else>Crear cuenta</span>
        </button>
      </form>

      <template #footer>
        <div class="flex items-center justify-between">
          <span>¿Ya tienes cuenta?</span>
          <Link
            :href="route('login')"
            class="km-link"
          >
            Iniciar sesión
          </Link>
        </div>
      </template>
    </AuthCard>
  </GuestLayout>
</template>
