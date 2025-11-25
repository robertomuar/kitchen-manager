<script setup>
import { ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();

// URL actual, tipo "/dashboard", "/products", etc.
const currentUrl = computed(() => page.url);

// Helpers para marcar activo el menú
const isDashboard = computed(() => currentUrl.value.startsWith('/dashboard'));
const isProducts = computed(() => currentUrl.value.startsWith('/products'));
const isStock = computed(() => currentUrl.value.startsWith('/stock'));
const isLocations = computed(() => currentUrl.value.startsWith('/locations'));
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-50"
    >
        <!-- Fondos decorativos -->
        <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
            <div
                class="absolute -left-1/4 top-[-10%] h-72 w-72 rounded-full bg-indigo-500/20 blur-3xl"
            ></div>
            <div
                class="absolute right-[-10%] top-1/3 h-72 w-72 rounded-full bg-emerald-500/20 blur-3xl"
            ></div>
            <div
                class="absolute bottom-[-20%] left-1/4 h-72 w-72 rounded-full bg-sky-500/20 blur-3xl"
            ></div>
        </div>

        <div class="mx-auto flex min-h-screen max-w-6xl flex-col px-4">
            <!-- Navbar -->
            <nav
                class="mt-4 mb-4 flex h-14 items-center justify-between rounded-2xl border border-slate-800/80 bg-slate-950/80 px-3 py-2 shadow-lg shadow-slate-950/60 backdrop-blur-sm"
            >
                <div class="flex items-center gap-4">
                    <!-- Logo + nombre app -->
                    <div class="flex items-center gap-2">
                        <Link :href="route('dashboard')">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900/80 ring-1 ring-slate-700/80"
                                >
                                    <ApplicationLogo
                                        class="h-6 w-6 fill-current text-indigo-400"
                                    />
                                </span>
                                <span
                                    class="hidden text-base font-semibold tracking-tight text-slate-50 sm:inline"
                                >
                                    Kitchen<span class="text-indigo-400"
                                        >Manager</span
                                    >
                                </span>
                            </div>
                        </Link>
                    </div>

                    <!-- Links desktop -->
                    <div class="hidden space-x-4 sm:flex">
                        <NavLink
                            :href="route('dashboard')"
                            :active="isDashboard"
                        >
                            Panel
                        </NavLink>

                        <NavLink
                            :href="route('products.index')"
                            :active="isProducts"
                        >
                            Productos
                        </NavLink>

                        <NavLink
                            :href="route('stock.index')"
                            :active="isStock"
                        >
                            Stock
                        </NavLink>

                        <NavLink
                            v-if="route('locations.index')"
                            :href="route('locations.index')"
                            :active="isLocations"
                        >
                            Ubicaciones
                        </NavLink>
                    </div>
                </div>

                <!-- Zona derecha (usuario / logout) -->
                <div class="hidden items-center gap-4 sm:flex">
                    <div class="text-xs text-slate-400">
                        {{ $page.props.auth.user.email }}
                    </div>

                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-xl border border-slate-700 bg-slate-900/80 px-3 py-1.5 text-xs font-medium text-slate-200 shadow-sm hover:border-slate-500 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-950"
                                    >
                                        {{ $page.props.auth.user.name }}

                                        <svg
                                            class="-me-0.5 ms-2 h-4 w-4"
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </button>
                                </span>
                            </template>

                            <template #content>
                                <DropdownLink :href="route('profile.edit')">
                                    Perfil
                                </DropdownLink>
                                <DropdownLink
                                    :href="route('logout')"
                                    method="post"
                                    as="button"
                                >
                                    Cerrar sesión
                                </DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>

                <!-- Botón hamburguesa móvil -->
                <div class="flex items-center sm:hidden">
                    <button
                        @click="
                            showingNavigationDropdown =
                                !showingNavigationDropdown
                        "
                        class="inline-flex items-center justify-center rounded-md p-2 text-slate-300 hover:bg-slate-800 hover:text-white focus:bg-slate-800 focus:text-white focus:outline-none"
                    >
                        <svg
                            class="h-6 w-6"
                            stroke="currentColor"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <path
                                :class="{
                                    hidden: showingNavigationDropdown,
                                    'inline-flex': !showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                            <path
                                :class="{
                                    hidden: !showingNavigationDropdown,
                                    'inline-flex': showingNavigationDropdown,
                                }"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
            </nav>

            <!-- Menú responsive -->
            <div
                :class="{
                    block: showingNavigationDropdown,
                    hidden: !showingNavigationDropdown,
                }"
                class="mb-4 rounded-2xl border border-slate-800/80 bg-slate-950/90 p-3 sm:hidden"
            >
                <div class="space-y-1 pb-3">
                    <ResponsiveNavLink
                        :href="route('dashboard')"
                        :active="isDashboard"
                    >
                        Panel
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        :href="route('products.index')"
                        :active="isProducts"
                    >
                        Productos
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        :href="route('stock.index')"
                        :active="isStock"
                    >
                        Stock
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        v-if="route('locations.index')"
                        :href="route('locations.index')"
                        :active="isLocations"
                    >
                        Ubicaciones
                    </ResponsiveNavLink>
                </div>

                <!-- Opciones de usuario móviles -->
                <div class="border-t border-slate-800/80 pt-3">
                    <div class="px-2">
                        <div class="text-sm font-medium text-slate-50">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="text-xs text-slate-400">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')">
                            Perfil
                        </ResponsiveNavLink>
                        <ResponsiveNavLink
                            :href="route('logout')"
                            method="post"
                            as="button"
                        >
                            Cerrar sesión
                        </ResponsiveNavLink>
                    </div>
                </div>
            </div>

            <!-- Cabecera de página (slot) -->
            <header
                v-if="$slots.header"
                class="mb-4 rounded-2xl border border-slate-800/80 bg-slate-950/80 px-4 py-4 shadow-sm backdrop-blur-sm"
            >
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <slot name="header" />
                    </div>
                </div>
            </header>

            <!-- Contenido principal -->
            <main class="mb-6 flex-1 pb-4">
                <slot />
            </main>

            <!-- Footer pequeño (opcional) -->
            <footer
                class="mb-4 mt-auto flex h-10 items-center justify-between border-t border-slate-800/80 text-[11px] text-slate-500"
            >
                <span>KitchenManager · Área privada</span>
                <span>Laravel 11 · Vue 3 · Tailwind CSS</span>
            </footer>
        </div>
    </div>
</template>
