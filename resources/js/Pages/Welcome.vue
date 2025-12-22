<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import AppMark from '@/Components/AppMark.vue';

const props = defineProps({
    canLogin: {
        type: Boolean,
        default: true,
    },
    canRegister: {
        type: Boolean,
        default: true,
    },
});

const description =
    'KitchenManager te ayuda a controlar el stock de tu cocina, organizar ubicaciones y evitar desperdicio de comida.';

const canonicalUrl = computed(() => {
    if (typeof window === 'undefined') {
        return '';
    }
    return new URL('/', window.location.origin).toString();
});

const ogImage = computed(() => {
    if (typeof window === 'undefined') {
        return '';
    }
    return new URL('/sombrero.png', window.location.origin).toString();
});
</script>

<template>
    <div class="km-page relative">
        <Head title="Kitchen Manager - Control de stock de tu cocina">
            <meta name="description" :content="description" head-key="description" />
            <link rel="canonical" :href="canonicalUrl" head-key="canonical" />
            <meta property="og:title" content="Kitchen Manager - Control de stock de tu cocina" head-key="og:title" />
            <meta property="og:description" :content="description" head-key="og:description" />
            <meta property="og:type" content="website" head-key="og:type" />
            <meta property="og:url" :content="canonicalUrl" head-key="og:url" />
            <meta property="og:image" :content="ogImage" head-key="og:image" />
            <meta name="twitter:card" content="summary_large_image" head-key="twitter:card" />
            <meta name="twitter:title" content="Kitchen Manager - Control de stock de tu cocina" head-key="twitter:title" />
            <meta name="twitter:description" :content="description" head-key="twitter:description" />
            <meta name="twitter:image" :content="ogImage" head-key="twitter:image" />
        </Head>

        <!-- Fondo decorativo -->
        <div class="pointer-events-none absolute inset-0 -z-10 overflow-hidden">
            <div
                class="km-halo-amber absolute -left-1/4 top-[-10%] h-72 w-72 rounded-full blur-3xl"
            ></div>
            <div
                class="km-halo-neutral absolute right-[-10%] top-1/3 h-72 w-72 rounded-full blur-3xl"
            ></div>
            <div
                class="km-halo-neutral absolute bottom-[-20%] left-1/4 h-72 w-72 rounded-full blur-3xl"
            ></div>
        </div>

        <!-- Contenedor principal -->
        <div class="relative z-10 mx-auto flex min-h-screen max-w-6xl flex-col px-4">
            <!-- Top bar -->
            <header class="flex h-16 items-center justify-between">
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex h-9 w-9 items-center justify-center rounded-2xl"
                        style="background: rgba(209,139,0,0.14); border: 1px solid rgba(209,139,0,0.20);"
                    >
                        <AppMark class="h-6 w-6" style="color: var(--km-accent)" />
                    </span>
                    <span
                        class="text-lg font-semibold tracking-tight text-[color:var(--km-text)]"
                    >
                        Kitchen<span class="text-[color:var(--km-accent)]">Manager</span>
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="km-link text-sm"
                    >
                        Iniciar sesión
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="km-btn w-auto px-4 py-2 text-sm"
                    >
                        Crear cuenta
                    </Link>
                </div>
            </header>

            <!-- Contenido -->
            <main class="flex flex-1 flex-col justify-center py-8 sm:py-16">
                <div
                    class="grid gap-10 md:grid-cols-[minmax(0,1.2fr),minmax(0,1fr)] items-center"
                >
                    <!-- Hero -->
                    <section class="space-y-6">
                        <div
                            class="inline-flex items-center gap-2 rounded-full border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] px-3 py-1 text-xs font-medium text-[color:var(--km-muted)]"
                        >
                            <span
                                class="inline-flex h-1.5 w-1.5 rounded-full"
                                style="background: var(--km-accent)"
                            ></span>
                            Control de stock para tu cocina de casa
                        </div>

                        <div class="space-y-3">
                            <h1
                                class="text-3xl font-semibold tracking-tight text-[color:var(--km-text)] sm:text-4xl md:text-5xl"
                            >
                                Ten siempre
                                <span class="text-[color:var(--km-accent)]">
                                    tu despensa bajo control
                                </span>
                                sin hojas de Excel.
                            </h1>
                            <p
                                class="max-w-xl text-sm text-[color:var(--km-muted)] sm:text-base"
                            >
                                KitchenManager te ayuda a saber qué tienes en
                                la nevera, despensa y congelador, cuándo toca
                                reponer y evitar tirar comida por caducidad.
                            </p>
                        </div>

                        <!-- CTA principal -->
                        <div class="flex flex-wrap items-center gap-3 pt-2">
                            <Link
                                v-if="canLogin"
                                :href="route('login')"
                                class="km-btn w-auto px-5 py-2 text-sm"
                            >
                                Entrar a mi cocina
                                <span class="ml-2 text-xs">→</span>
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="km-link text-sm font-semibold"
                            >
                                Crear cuenta nueva
                            </Link>

                            <p class="text-xs text-[color:var(--km-muted)]">
                                100% privado. Pensado para tu casa, pero listo
                                para crecer si quieres monetizarlo en el futuro.
                            </p>
                        </div>

                        <!-- Lista de beneficios -->
                        <div class="grid gap-3 pt-4 text-sm text-[color:var(--km-text)] sm:grid-cols-2">
                            <div class="flex items-start gap-2.5">
                                <div
                                    class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-lg"
                                    style="background: rgba(209,139,0,0.16); color: var(--km-accent);"
                                >
                                    ✓
                                </div>
                                <div>
                                    <p class="font-medium">
                                        Reposición automática
                                    </p>
                                    <p class="text-xs text-[color:var(--km-muted)]">
                                        Define cantidades mínimas y deja que la
                                        app genere tu lista de la compra.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <div
                                    class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-lg"
                                    style="background: rgba(11,16,36,0.08); color: var(--km-text);"
                                >
                                    🧊
                                </div>
                                <div>
                                    <p class="font-medium">
                                        Ubicaciones inteligentes
                                    </p>
                                    <p class="text-xs text-[color:var(--km-muted)]">
                                        Despensa, nevera, congelador, armarios…
                                        organiza tus productos por zonas.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <div
                                    class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-lg"
                                    style="background: rgba(209,139,0,0.16); color: var(--km-accent);"
                                >
                                    👨‍👩‍👦
                                </div>
                                <div>
                                    <p class="font-medium">
                                        Multiusuario (a futuro)
                                    </p>
                                    <p class="text-xs text-[color:var(--km-muted)]">
                                        Cada usuario tiene su cocina, pero
                                        podrás compartir stock con tu pareja o familia.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <div
                                    class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-lg"
                                    style="background: rgba(11,16,36,0.08); color: var(--km-text);"
                                >
                                    📱
                                </div>
                                <div>
                                    <p class="font-medium">
                                        Pensado para móvil
                                    </p>
                                    <p class="text-xs text-[color:var(--km-muted)]">
                                        Funciona perfecto en navegador móvil y
                                        luego lo montaremos en tu servidor con Docker.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Panel “preview” -->
                    <section
                        class="relative mx-auto w-full max-w-md p-4 km-card"
                    >
                        <div
                            class="mb-3 flex items-center justify-between text-xs text-[color:var(--km-muted)]"
                        >
                            <span class="inline-flex items-center gap-1.5">
                                <span
                                    class="inline-flex h-1.5 w-1.5 rounded-full"
                                    style="background: var(--km-accent)"
                                ></span>
                                Vista previa de tu cocina
                            </span>
                            <span>Hoy</span>
                        </div>

                        <div class="space-y-3">
                            <!-- Tarjeta resumen -->
                            <div
                                class="rounded-xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] p-3"
                            >
                                <div
                                    class="flex items-center justify-between gap-3"
                                >
                                    <div>
                                        <p
                                            class="text-xs font-medium uppercase tracking-wide text-[color:var(--km-muted)]"
                                        >
                                            Estado de stock
                                        </p>
                                        <p
                                            class="text-2xl font-semibold text-[color:var(--km-text)]"
                                        >
                                            124
                                            <span
                                                class="text-sm font-normal text-[color:var(--km-muted)]"
                                            >
                                                productos
                                            </span>
                                        </p>
                                    </div>
                                    <div
                                        class="flex flex-col items-end text-xs"
                                    >
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full px-2 py-0.5"
                                            style="background: rgba(209,139,0,0.12); color: var(--km-accent);"
                                        >
                                            ◦ 12
                                            <span class="text-[11px]">
                                                por reponer
                                            </span>
                                        </span>
                                        <span
                                            class="mt-1 inline-flex items-center gap-1 rounded-full px-2 py-0.5"
                                            style="background: rgba(11,16,36,0.08); color: var(--km-text);"
                                        >
                                            ◦ 5
                                            <span class="text-[11px]">
                                                cerca del mínimo
                                            </span>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Lista de ejemplo -->
                            <div class="space-y-2">
                                <p
                                    class="text-xs font-medium uppercase tracking-wide text-[color:var(--km-muted)]"
                                >
                                    Próximas reposiciones
                                </p>

                                <div class="space-y-1.5 text-xs">
                                    <div
                                        class="flex items-center justify-between rounded-lg border border-[color:var(--km-border)] bg-white/70 px-2.5 py-2"
                                    >
                                        <div>
                                            <p
                                                class="font-medium text-[color:var(--km-text)]"
                                            >
                                                Leche entera
                                            </p>
                                            <p class="text-[11px] text-[color:var(--km-muted)]">
                                                Despensa · Quedan 2 unidades
                                            </p>
                                        </div>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px]"
                                            style="background: rgba(209,139,0,0.12); color: var(--km-accent);"
                                        >
                                            Añadir a lista
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between rounded-lg border border-[color:var(--km-border)] bg-white/70 px-2.5 py-2"
                                    >
                                        <div>
                                            <p
                                                class="font-medium text-[color:var(--km-text)]"
                                            >
                                                Pechugas de pollo
                                            </p>
                                            <p class="text-[11px]" style="color: var(--km-accent)">
                                                Congelador · Última bandeja
                                            </p>
                                        </div>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px]"
                                            style="background: rgba(209,139,0,0.16); color: var(--km-accent);"
                                        >
                                            Prioridad alta
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between rounded-lg border border-[color:var(--km-border)] bg-white/70 px-2.5 py-2"
                                    >
                                        <div>
                                            <p
                                                class="font-medium text-[color:var(--km-text)]"
                                            >
                                                Papel de cocina
                                            </p>
                                            <p class="text-[11px] text-[color:var(--km-muted)]">
                                                Armario limpieza · 1 rollo
                                            </p>
                                        </div>
                                        <span
                                            class="inline-flex items-center rounded-full px-2 py-0.5 text-[11px]"
                                            style="background: rgba(11,16,36,0.08); color: var(--km-text);"
                                        >
                                            Hogar
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pie de la tarjeta -->
                            <div
                                class="mt-3 pt-3 text-[11px] text-[color:var(--km-muted)]"
                            >
                                <div class="km-divider" />
                                <div class="flex items-center justify-between">
                                    <span>
                                        Todo conectado a tu base de datos MySQL.
                                    </span>
                                    <span class="text-[color:var(--km-muted)]">
                                        Laravel · Vue · Tailwind
                                    </span>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Bloque “Cómo funciona” -->
                <section class="mt-12 pt-8">
                    <div class="km-divider" />
                    <h2
                        class="text-sm font-semibold uppercase tracking-wide text-[color:var(--km-muted)]"
                    >
                        ¿Cómo funcionará tu app?
                    </h2>

                    <div
                        class="mt-4 grid gap-4 text-sm text-[color:var(--km-text)] sm:grid-cols-3"
                    >
                        <div class="space-y-1.5">
                            <p class="font-medium">1. Registra tu cocina</p>
                            <p class="text-xs text-[color:var(--km-muted)]">
                                Creas tus ubicaciones (nevera, despensa,
                                congelador, armarios…) y tus productos con sus
                                datos reales.
                            </p>
                        </div>
                        <div class="space-y-1.5">
                            <p class="font-medium">2. Controla tu stock</p>
                            <p class="text-xs text-[color:var(--km-muted)]">
                                Cada movimiento (entrada/salida) se guarda.
                                Siempre sabes qué tienes y cuánto.
                            </p>
                        </div>
                        <div class="space-y-1.5">
                            <p class="font-medium">3. Reposición automática</p>
                            <p class="text-xs text-[color:var(--km-muted)]">
                                El sistema genera tu lista de la compra según
                                mínimos y consumo medio. Más adelante lo
                                desplegaremos en tu servidor con Docker para
                                usarlo desde el móvil.
                            </p>
                        </div>
                    </div>
                </section>
            </main>

            <!-- Footer -->
            <footer
                class="mt-10 text-xs text-[color:var(--km-muted)]"
            >
                <div class="km-divider" />
                <div class="flex h-14 items-center justify-between">
                    <span>KitchenManager · Proyecto personal de control de stock</span>
                    <span>Laravel 11 · Vue 3 · Tailwind CSS</span>
                </div>
            </footer>
        </div>
    </div>
</template>
