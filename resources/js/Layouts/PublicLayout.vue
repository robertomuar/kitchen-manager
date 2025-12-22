<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import AppMark from '@/Components/AppMark.vue';

const page = usePage();
const canLogin = page.props.canLogin ?? true;
const canRegister = page.props.canRegister ?? true;
</script>

<template>
    <div class="km-page min-h-screen bg-[color:var(--km-bg)] text-[color:var(--km-text)]">
        <header class="border-b border-[color:var(--km-border)]">
            <div class="mx-auto flex h-16 max-w-6xl items-center justify-between px-4">
                <Link :href="route('home')" class="flex items-center gap-2">
                    <span
                        class="inline-flex h-9 w-9 items-center justify-center rounded-2xl"
                        style="background: rgba(209,139,0,0.14); border: 1px solid rgba(209,139,0,0.20);"
                    >
                        <AppMark class="h-6 w-6" style="color: var(--km-accent)" />
                    </span>
                    <span class="text-lg font-semibold tracking-tight">
                        Kitchen<span class="text-[color:var(--km-accent)]">Manager</span>
                    </span>
                </Link>

                <nav class="hidden items-center gap-4 text-sm font-medium text-[color:var(--km-muted)] md:flex">
                    <Link :href="route('features')" class="hover:text-[color:var(--km-text)]">Funcionalidades</Link>
                    <Link :href="route('pricing')" class="hover:text-[color:var(--km-text)]">Precios</Link>
                    <Link :href="route('faq')" class="hover:text-[color:var(--km-text)]">FAQ</Link>
                    <Link :href="route('blog.index')" class="hover:text-[color:var(--km-text)]">Blog</Link>
                    <Link :href="route('contact')" class="hover:text-[color:var(--km-text)]">Contacto</Link>
                </nav>

                <div class="flex items-center gap-3 text-sm">
                    <Link v-if="canLogin" :href="route('login')" class="km-link">Iniciar sesión</Link>
                    <Link v-if="canRegister" :href="route('register')" class="km-btn w-auto px-4 py-2">Crear cuenta</Link>
                </div>
            </div>
        </header>

        <main class="mx-auto w-full max-w-6xl px-4 py-10">
            <slot />
        </main>

        <footer class="border-t border-[color:var(--km-border)]">
            <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-8 text-sm text-[color:var(--km-muted)] md:flex-row md:items-center md:justify-between">
                <div class="space-y-2">
                    <p class="font-semibold text-[color:var(--km-text)]">KitchenManager</p>
                    <p>Controla tu inventario de cocina, reduce el desperdicio y gana tiempo.</p>
                </div>
                <div class="flex flex-wrap gap-4">
                    <Link :href="route('privacy')" class="hover:text-[color:var(--km-text)]">Privacidad</Link>
                    <Link :href="route('terms')" class="hover:text-[color:var(--km-text)]">Términos</Link>
                    <Link :href="route('contact')" class="hover:text-[color:var(--km-text)]">Contacto</Link>
                </div>
            </div>
        </footer>
    </div>
</template>
