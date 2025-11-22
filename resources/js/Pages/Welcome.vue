<script setup>
import { Head, Link } from '@inertiajs/vue3';

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
</script>

<template>
    <div
        class="min-h-screen bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 text-slate-50"
    >
        <Head title="Kitchen Manager - Control de stock de tu cocina" />

        <!-- Fondo decorativo -->
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

        <!-- Contenedor principal -->
        <div class="mx-auto flex min-h-screen max-w-6xl flex-col px-4">
            <!-- Top bar -->
            <header class="flex h-16 items-center justify-between">
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex h-8 w-8 items-center justify-center rounded-xl bg-slate-900/80 ring-1 ring-slate-700/80"
                    >
                        🧊
                    </span>
                    <span
                        class="text-lg font-semibold tracking-tight text-slate-50"
                    >
                        Kitchen<span class="text-indigo-400">Manager</span>
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        v-if="canLogin"
                        :href="route('login')"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-200 hover:text-white hover:bg-slate-800/80 transition"
                    >
                        Iniciar sesión
                    </Link>
                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="inline-flex items-center rounded-lg bg-indigo-500 px-3.5 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-950 transition"
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
                            class="inline-flex items-center gap-2 rounded-full border border-slate-700/60 bg-slate-900/70 px-3 py-1 text-xs font-medium text-slate-300"
                        >
                            <span
                                class="inline-flex h-1.5 w-1.5 rounded-full bg-emerald-400"
                            ></span>
                            Control de stock para tu cocina de casa
                        </div>

                        <div class="space-y-3">
                            <h1
                                class="text-3xl font-semibold tracking-tight text-slate-50 sm:text-4xl md:text-5xl"
                            >
                                Ten siempre
                                <span class="text-indigo-400">
                                    tu despensa bajo control
                                </span>
                                sin hojas de Excel.
                            </h1>
                            <p
                                class="max-w-xl text-sm text-slate-300 sm:text-base"
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
                                class="inline-flex items-center rounded-lg bg-indigo-500 px-4 py-2 text-sm font-medium text-white shadow-lg shadow-indigo-500/30 hover:bg-indigo-400 focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-950 transition"
                            >
                                Entrar a mi cocina
                                <span class="ml-2 text-xs">→</span>
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="inline-flex items-center rounded-lg border border-slate-700/70 bg-slate-900/60 px-4 py-2 text-sm font-medium text-slate-200 hover:bg-slate-800/80 hover:text-white transition"
                            >
                                Crear cuenta nueva
                            </Link>

                            <p class="text-xs text-slate-400">
                                100% privado. Pensado para tu casa, pero listo
                                para crecer si quieres monetizarlo en el futuro.
                            </p>
                        </div>

                        <!-- Lista de beneficios -->
                        <div
                            class="grid gap-3 pt-4 text-sm text-slate-200 sm:grid-cols-2"
                        >
                            <div class="flex items-start gap-2.5">
                                <div
                                    class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-lg bg-emerald-500/20 text-emerald-300"
                                >
                                    ✓
                                </div>
                                <div>
                                    <p class="font-medium">
                                        Reposición automática
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        Define cantidades mínimas y deja que la
                                        app genere tu lista de la compra.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <div
                                    class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-lg bg-sky-500/20 text-sky-300"
                                >
                                    🧊
                                </div>
                                <div>
                                    <p class="font-medium">
                                        Ubicaciones inteligentes
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        Despensa, nevera, congelador, armarios…
                                        organiza tus productos por zonas.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <div
                                    class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-lg bg-amber-500/20 text-amber-300"
                                >
                                    👨‍👩‍👦
                                </div>
                                <div>
                                    <p class="font-medium">
                                        Multiusuario (a futuro)
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        Cada usuario tiene su cocina, pero
                                        podrás compartir stock con tu pareja o familia.
                                    </p>
                                </div>
                            </div>

                            <div class="flex items-start gap-2.5">
                                <div
                                    class="mt-0.5 inline-flex h-6 w-6 items-center justify-center rounded-lg bg-pink-500/20 text-pink-300"
                                >
                                    📱
                                </div>
                                <div>
                                    <p class="font-medium">
                                        Pensado para móvil
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        Funciona perfecto en navegador móvil y
                                        luego lo montaremos en tu servidor con Docker.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Panel “preview” -->
                    <section
                        class="relative mx-auto w-full max-w-md rounded-2xl border border-slate-800/80 bg-slate-900/70 p-4 shadow-2xl shadow-indigo-500/20 backdrop-blur-sm"
                    >
                        <div
                            class="mb-3 flex items-center justify-between text-xs text-slate-400"
                        >
                            <span class="inline-flex items-center gap-1.5">
                                <span
                                    class="inline-flex h-1.5 w-1.5 rounded-full bg-emerald-400"
                                ></span>
                                Vista previa de tu cocina
                            </span>
                            <span>Hoy</span>
                        </div>

                        <div class="space-y-3">
                            <!-- Tarjeta resumen -->
                            <div
                                class="rounded-xl border border-slate-700/80 bg-slate-950/60 p-3"
                            >
                                <div
                                    class="flex items-center justify-between gap-3"
                                >
                                    <div>
                                        <p
                                            class="text-xs font-medium uppercase tracking-wide text-slate-400"
                                        >
                                            Estado de stock
                                        </p>
                                        <p
                                            class="text-2xl font-semibold text-slate-50"
                                        >
                                            124
                                            <span
                                                class="text-sm font-normal text-slate-400"
                                            >
                                                productos
                                            </span>
                                        </p>
                                    </div>
                                    <div
                                        class="flex flex-col items-end text-xs"
                                    >
                                        <span
                                            class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-2 py-0.5 text-emerald-300"
                                        >
                                            ◦ 12
                                            <span class="text-[11px]">
                                                por reponer
                                            </span>
                                        </span>
                                        <span
                                            class="mt-1 inline-flex items-center gap-1 rounded-full bg-amber-500/10 px-2 py-0.5 text-amber-300"
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
                                    class="text-xs font-medium uppercase tracking-wide text-slate-400"
                                >
                                    Próximas reposiciones
                                </p>

                                <div class="space-y-1.5 text-xs">
                                    <div
                                        class="flex items-center justify-between rounded-lg border border-slate-800/80 bg-slate-950/50 px-2.5 py-2"
                                    >
                                        <div>
                                            <p
                                                class="font-medium text-slate-50"
                                            >
                                                Leche entera
                                            </p>
                                            <p class="text-[11px] text-slate-400">
                                                Despensa · Quedan 2 unidades
                                            </p>
                                        </div>
                                        <span
                                            class="inline-flex items-center rounded-full bg-emerald-500/15 px-2 py-0.5 text-[11px] text-emerald-300"
                                        >
                                            Añadir a lista
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between rounded-lg border border-slate-800/80 bg-slate-950/50 px-2.5 py-2"
                                    >
                                        <div>
                                            <p
                                                class="font-medium text-slate-50"
                                            >
                                                Pechugas de pollo
                                            </p>
                                            <p class="text-[11px] text-amber-300">
                                                Congelador · Última bandeja
                                            </p>
                                        </div>
                                        <span
                                            class="inline-flex items-center rounded-full bg-amber-500/15 px-2 py-0.5 text-[11px] text-amber-200"
                                        >
                                            Prioridad alta
                                        </span>
                                    </div>

                                    <div
                                        class="flex items-center justify-between rounded-lg border border-slate-800/80 bg-slate-950/50 px-2.5 py-2"
                                    >
                                        <div>
                                            <p
                                                class="font-medium text-slate-50"
                                            >
                                                Papel de cocina
                                            </p>
                                            <p class="text-[11px] text-slate-400">
                                                Armario limpieza · 1 rollo
                                            </p>
                                        </div>
                                        <span
                                            class="inline-flex items-center rounded-full bg-sky-500/15 px-2 py-0.5 text-[11px] text-sky-200"
                                        >
                                            Hogar
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pie de la tarjeta -->
                            <div
                                class="mt-3 flex items-center justify-between border-t border-slate-800/80 pt-3 text-[11px] text-slate-400"
                            >
                                <span>
                                    Todo conectado a tu base de datos MySQL.
                                </span>
                                <span class="text-slate-500">
                                    Laravel · Vue · Tailwind
                                </span>
                            </div>
                        </div>
                    </section>
                </div>

                <!-- Bloque “Cómo funciona” -->
                <section class="mt-12 border-t border-slate-800/80 pt-8">
                    <h2
                        class="text-sm font-semibold uppercase tracking-wide text-slate-400"
                    >
                        ¿Cómo funcionará tu app?
                    </h2>

                    <div
                        class="mt-4 grid gap-4 text-sm text-slate-200 sm:grid-cols-3"
                    >
                        <div class="space-y-1.5">
                            <p class="font-medium">1. Registra tu cocina</p>
                            <p class="text-xs text-slate-400">
                                Creas tus ubicaciones (nevera, despensa,
                                congelador, armarios…) y tus productos con sus
                                datos reales.
                            </p>
                        </div>
                        <div class="space-y-1.5">
                            <p class="font-medium">2. Controla tu stock</p>
                            <p class="text-xs text-slate-400">
                                Cada movimiento (entrada/salida) se guarda.
                                Siempre sabes qué tienes y cuánto.
                            </p>
                        </div>
                        <div class="space-y-1.5">
                            <p class="font-medium">3. Reposición automática</p>
                            <p class="text-xs text-slate-400">
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
                class="flex h-14 items-center justify-between border-t border-slate-800/80 text-xs text-slate-500"
            >
                <span>KitchenManager · Proyecto personal de control de stock</span>
                <span>Laravel 11 · Vue 3 · Tailwind CSS</span>
            </footer>
        </div>
    </div>
</template>
