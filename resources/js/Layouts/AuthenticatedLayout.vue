<script setup>
import { ref, computed } from 'vue';
import AppMark from '@/Components/AppMark.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { getCsrfToken } from '@/bootstrap';

const showingNavigationDropdown = ref(false);

const page = usePage();
const isAdmin = computed(() => !!page.props?.auth?.user?.is_admin);

// ✅ detectar si estamos en /admin para marcar “active” sin depender de Ziggy
const currentUrl = computed(() => page.url ?? '');
const inAdmin = computed(() => currentUrl.value.startsWith('/admin'));
const inAdminDb = computed(() => currentUrl.value.startsWith('/admin/db'));

// Leer el token CSRF cada vez que el componente se vuelve a renderizar para
// evitar usar valores antiguos cuando la sesión se renueva o el token rota.
const resolveCsrfToken = () => getCsrfToken() ?? '';
</script>

<template>
    <div class="km-page relative">
        <!-- Fondos decorativos -->
        <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
            <div
                class="km-halo-amber absolute -top-24 left-1/3 h-96 w-96 -translate-x-1/2 rounded-full blur-3xl"
            ></div>
            <div
                class="km-halo-neutral absolute -bottom-24 right-1/4 h-96 w-96 rounded-full blur-3xl"
            ></div>
        </div>

        <div class="relative z-10 mx-auto flex min-h-screen max-w-6xl flex-col px-4">
            <!-- Navbar -->
            <nav
                class="relative z-20 mt-4 mb-4 flex h-14 items-center justify-between rounded-2xl px-3 py-2 km-card"
            >
                <div class="flex items-center gap-4">
                    <!-- Logo + nombre app -->
                    <div class="flex items-center gap-2">
                        <Link :href="route('dashboard')">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex h-9 w-9 items-center justify-center rounded-2xl"
                                    style="background: rgba(209,139,0,0.14); border: 1px solid rgba(209,139,0,0.20);"
                                >
                                    <AppMark
                                        class="h-6 w-6"
                                        style="color: var(--km-accent)"
                                    />
                                </span>
                                <span
                                    class="hidden text-base font-semibold tracking-tight text-[color:var(--km-text)] sm:inline"
                                >
                                    Kitchen<span class="text-[color:var(--km-accent)]">Manager</span>
                                </span>
                            </div>
                        </Link>
                    </div>

                    <!-- Links desktop -->
                    <div class="hidden space-x-4 sm:flex">
                        <NavLink
                            :href="route('dashboard')"
                            :active="route().current('dashboard')"
                        >
                            Panel
                        </NavLink>

                        <NavLink
                            :href="route('products.index')"
                            :active="route().current('products.index')"
                        >
                            Productos
                        </NavLink>

                        <NavLink
                            :href="route('stock.index')"
                            :active="route().current('stock.index')"
                        >
                            Stock
                        </NavLink>

                        <NavLink
                            v-if="route().has('locations.index')"
                            :href="route('locations.index')"
                            :active="route().current('locations.index')"
                        >
                            Ubicaciones
                        </NavLink>

                        <!-- ✅ Admin FUERA (URLs directas) -->
                        <NavLink
                            v-if="isAdmin"
                            href="/admin"
                            :active="inAdmin && !inAdminDb"
                        >
                            Admin
                        </NavLink>

                        <NavLink
                            v-if="isAdmin"
                            href="/admin/db"
                            :active="inAdminDb"
                        >
                            DB
                        </NavLink>
                    </div>
                </div>

                <!-- Zona derecha (usuario / logout) -->
                <div class="hidden items-center gap-4 sm:flex">
                    <div class="text-xs text-[color:var(--km-muted)]">
                        {{ $page.props.auth.user.email }}
                    </div>

                    <div class="relative">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <span class="inline-flex rounded-md">
                                    <button
                                        type="button"
                                        class="inline-flex items-center rounded-xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] px-3 py-1.5 text-xs font-medium text-[color:var(--km-text)] shadow-sm hover:bg-white/80 focus:outline-none focus:ring-2 focus:ring-[color:var(--km-ring)] focus:ring-offset-2"
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

                                <form :action="route('logout')" method="post">
                                    <input
                                        type="hidden"
                                        name="_token"
                                        :value="resolveCsrfToken()"
                                    />
                                    <button
                                        type="submit"
                                        class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-700 transition duration-150 ease-in-out hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
                                    >
                                        Cerrar sesión
                                    </button>
                                </form>
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
                        class="inline-flex items-center justify-center rounded-md p-2 text-[color:var(--km-muted)] hover:bg-[color:var(--km-bg-2)] hover:text-[color:var(--km-text)] focus:outline-none focus:ring-2 focus:ring-[color:var(--km-ring)]"
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
                class="mb-4 rounded-2xl p-3 sm:hidden km-card"
            >
                <div class="space-y-1 pb-3">
                    <ResponsiveNavLink
                        :href="route('dashboard')"
                        :active="route().current('dashboard')"
                    >
                        Panel
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        :href="route('products.index')"
                        :active="route().current('products.index')"
                    >
                        Producto
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        :href="route('stock.index')"
                        :active="route().current('stock.index')"
                    >
                        Stock
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        v-if="route().has('locations.index')"
                        :href="route('locations.index')"
                        :active="route().current('locations.index')"
                    >
                        Ubicaciones
                    </ResponsiveNavLink>

                    <!-- ✅ Admin en móvil (URLs directas) -->
                    <ResponsiveNavLink
                        v-if="isAdmin"
                        href="/admin"
                        :active="inAdmin && !inAdminDb"
                    >
                        Admin
                    </ResponsiveNavLink>

                    <ResponsiveNavLink
                        v-if="isAdmin"
                        href="/admin/db"
                        :active="inAdminDb"
                    >
                        DB
                    </ResponsiveNavLink>
                </div>

                <!-- Opciones de usuario móviles -->
                <div class="pt-3">
                    <div class="km-divider" />
                    <div class="px-2">
                        <div class="text-sm font-medium text-[color:var(--km-text)]">
                            {{ $page.props.auth.user.name }}
                        </div>
                        <div class="text-xs text-[color:var(--km-muted)]">
                            {{ $page.props.auth.user.email }}
                        </div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <ResponsiveNavLink :href="route('profile.edit')">
                            Perfil
                        </ResponsiveNavLink>
                        <form :action="route('logout')" method="post">
                            <input
                                type="hidden"
                                name="_token"
                                :value="resolveCsrfToken()"
                            />
                            <button
                                type="submit"
                                class="block w-full rounded-lg border border-transparent ps-4 pe-4 py-2 text-start text-sm font-medium text-[color:var(--km-text)] hover:bg-[color:var(--km-bg-2)] focus:outline-none focus:ring-2 focus:ring-[color:var(--km-ring)] focus:ring-offset-2"
                            >
                                Cerrar sesión
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Cabecera de página (slot) -->
            <header
                v-if="$slots.header"
                class="mb-4 rounded-2xl px-4 py-4 km-card"
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
                class="mb-4 mt-auto text-[11px] text-[color:var(--km-muted)]"
            >
                <div class="km-divider" />
                <div class="flex h-10 items-center justify-between">
                    <span>KitchenManager · Área privada</span>
                    <span>Laravel 11 · Vue 3 · Tailwind CSS</span>
                </div>
            </footer>
        </div>
    </div>
</template>
