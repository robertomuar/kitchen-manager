<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import SeoHead from '@/Components/Public/SeoHead.vue';
import Breadcrumbs from '@/Components/Public/Breadcrumbs.vue';

const props = defineProps({
    baseUrl: String,
    canonical: String,
    title: String,
});

const page = usePage();
const canLogin = page.props.canLogin ?? true;
const canRegister = page.props.canRegister ?? true;

const description =
    'Organiza tu despensa, nevera y congelador con un inventario claro, alertas de caducidad y listas de compra automáticas. KitchenManager te ayuda a reducir desperdicio y ahorrar tiempo.';

const breadcrumbs = computed(() => [
    { name: 'Inicio', url: props.baseUrl },
]);
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="'KitchenManager | Control de inventario de cocina y despensa'"
            :description="description"
            :canonical="canonical"
        />

        <Breadcrumbs :items="breadcrumbs" />

        <section class="mt-8 grid gap-10 lg:grid-cols-[1.2fr,1fr] lg:items-center">
            <div class="space-y-6">
                <p class="inline-flex items-center gap-2 rounded-full border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] px-3 py-1 text-xs font-medium text-[color:var(--km-muted)]">
                    Inventario inteligente para cocinas domésticas
                </p>
                <h1 class="text-3xl font-semibold tracking-tight sm:text-4xl">
                    Ten tu despensa bajo control, evita caducidades y compra solo lo necesario.
                </h1>
                <p class="text-base text-[color:var(--km-muted)]">
                    KitchenManager es el panel que necesitas para saber qué productos tienes, dónde están y cuándo debes reponerlos. Diseñado para hogares reales, no para hojas de cálculo, reúne en un solo lugar el inventario de despensa, nevera y congelador. Puedes registrar cantidades, unidades, fechas de caducidad y notas, y la app se encarga de avisarte cuando algo está por acabarse o está a punto de vencer.
                </p>
                <div class="flex flex-wrap gap-3">
                    <Link v-if="canRegister" :href="route('register')" class="km-btn w-auto px-5 py-2">
                        Crear cuenta gratis
                    </Link>
                    <Link v-if="canLogin" :href="route('login')" class="km-link">
                        Ya tengo cuenta
                    </Link>
                </div>
            </div>
            <div class="rounded-3xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] p-6">
                <img
                    src="/images/marketing-dashboard.svg"
                    alt="Panel de control con stock y caducidades"
                    width="520"
                    height="360"
                    loading="lazy"
                    decoding="async"
                    class="h-auto w-full"
                />
            </div>
        </section>

        <section class="mt-12 space-y-6 text-[color:var(--km-text)]">
            <h2 class="text-2xl font-semibold">Por qué KitchenManager mejora tu rutina diaria</h2>
            <p class="text-base text-[color:var(--km-muted)]">
                El caos en la cocina suele empezar con la falta de visibilidad. Cuando no recuerdas qué hay en el congelador, compras de más; cuando no sabes qué vence pronto, desperdicias comida. KitchenManager te ofrece una vista clara y ordenada del inventario. Puedes clasificar productos por ubicación (despensa, nevera, congelador o armarios), establecer cantidades mínimas y generar tu lista de la compra desde el mismo panel. Así no duplicas compras y mantienes lo esencial siempre disponible.
            </p>
            <p class="text-base text-[color:var(--km-muted)]">
                A diferencia de las apps genéricas, esta herramienta está pensada para hogares y pequeños equipos familiares. Puedes registrar el formato del producto (pack, gramos, litros), agregar notas útiles y revisar el histórico de caducidades. Con esta información podrás planificar menús, evitar sorpresas al cocinar y aprovechar mejor los alimentos. Todo funciona desde el navegador móvil, así que puedes revisar el inventario cuando estás en el supermercado.
            </p>
            <p class="text-base text-[color:var(--km-muted)]">
                Además, el sistema está preparado para escalar. Si en el futuro quieres compartir la cocina con otra persona, tendrás la base lista para colaborar sin perder el control. Y si gestionas varias ubicaciones, podrás organizarlas con facilidad. KitchenManager no promete milagros, pero sí un método claro y sostenible para mantener el orden y reducir el desperdicio.
            </p>
        </section>

        <section class="mt-12 grid gap-6 md:grid-cols-3">
            <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                <h3 class="text-lg font-semibold">Alertas de caducidad</h3>
                <p class="mt-2 text-sm text-[color:var(--km-muted)]">
                    Marca fechas de vencimiento y recibe avisos para cocinar a tiempo. Reduce desperdicio y cuida tu presupuesto.
                </p>
            </div>
            <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                <h3 class="text-lg font-semibold">Lista de compra automática</h3>
                <p class="mt-2 text-sm text-[color:var(--km-muted)]">
                    Define mínimos por producto y genera una lista clara de lo que falta, sin duplicar compras.
                </p>
            </div>
            <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                <h3 class="text-lg font-semibold">Control por ubicaciones</h3>
                <p class="mt-2 text-sm text-[color:var(--km-muted)]">
                    Separa despensa, nevera y congelador para encontrar todo rápido. Ideal para hogares con varias personas.
                </p>
            </div>
        </section>

        <section class="mt-12 rounded-3xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] p-8">
            <h2 class="text-2xl font-semibold">Empieza hoy sin complicaciones</h2>
            <p class="mt-3 text-base text-[color:var(--km-muted)]">
                Crear tu inventario toma pocos minutos. Añade productos, define ubicaciones y listo: tu cocina empieza a ordenarse sola. No necesitas instalar nada, solo abrir el navegador. Si quieres probarlo, crea una cuenta y empieza con los productos que más compras cada semana.
            </p>
            <div class="mt-4 flex flex-wrap gap-3">
                <Link v-if="canRegister" :href="route('register')" class="km-btn w-auto px-5 py-2">
                    Crear cuenta gratis
                </Link>
                <Link :href="route('features')" class="km-link">
                    Ver funcionalidades
                </Link>
            </div>
        </section>
    </PublicLayout>
</template>
