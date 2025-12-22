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
const canRegister = page.props.canRegister ?? true;

const description =
    'Descubre las funcionalidades clave de KitchenManager: inventario por ubicaciones, alertas de caducidad, reposición automática y listados exportables para tu cocina.';

const breadcrumbs = computed(() => [
    { name: 'Inicio', url: props.baseUrl },
    { name: 'Funcionalidades', url: props.canonical },
]);
</script>

<template>
    <PublicLayout>
        <SeoHead
            :title="'Funcionalidades de KitchenManager'"
            :description="description"
            :canonical="canonical"
        />

        <Breadcrumbs :items="breadcrumbs" />

        <section class="mt-8 space-y-6">
            <h1 class="text-3xl font-semibold">Funcionalidades pensadas para una cocina organizada</h1>
            <p class="text-base text-[color:var(--km-muted)]">
                KitchenManager nació para resolver un problema real: no saber qué hay en la despensa ni cuándo toca reponer. Por eso las funcionalidades están diseñadas para darte visibilidad y control desde el primer día. Puedes crear productos con cantidades, unidades y tamaños de pack, registrar fechas de caducidad, asignar ubicaciones específicas y filtrar el stock en segundos. El resultado es un inventario simple de consultar que te ayuda a planificar mejor tus compras y menús.
            </p>
            <p class="text-base text-[color:var(--km-muted)]">
                El sistema de alertas te permite definir mínimos por producto. Si tu aceite de oliva baja del nivel recomendado, la app lo detecta y lo añade a una lista de reposición. Esto evita compras duplicadas y también evita quedarte sin los básicos cuando más los necesitas. Además, puedes exportar listados en CSV o PDF para llevarlos al supermercado o compartirlos con quien haga la compra.
            </p>
            <p class="text-base text-[color:var(--km-muted)]">
                Otra ventaja importante es la organización por ubicaciones. Puedes crear zonas como despensa, nevera, congelador, armario de limpieza o incluso cajas específicas. Esto te permite encontrar rápido lo que necesitas y mantener todo ordenado. Cuando abres la app desde el móvil, puedes filtrar por ubicación y comprobar de inmediato si ya tienes un producto sin recorrer la cocina entera.
            </p>
            <p class="text-base text-[color:var(--km-muted)]">
                KitchenManager también está preparado para crecer. Si más adelante quieres compartir la cocina con otra persona, podrás hacerlo sin perder el control del inventario. El enfoque es claro: ofrecer una experiencia sencilla, sin ruido ni configuraciones innecesarias, pero con información útil que te ayude a reducir desperdicio, ahorrar dinero y organizar mejor tu día a día.
            </p>
            <p class="text-base text-[color:var(--km-muted)]">
                El panel principal reúne métricas clave: cuántos productos tienes, cuáles están por debajo del mínimo y qué artículos están cerca de su fecha de caducidad. Esta vista rápida es especialmente útil para planificar menús semanales o preparar compras con antelación. En lugar de revisar armario por armario, puedes decidir en minutos qué necesitas y qué puedes aprovechar antes de que se pierda.
            </p>
            <p class="text-base text-[color:var(--km-muted)]">
                También puedes añadir notas personalizadas por producto, algo útil si quieres marcar ingredientes para dietas específicas, alergias o marcas preferidas. Con esta información contextual, la app se convierte en una memoria externa que te ayuda a tomar mejores decisiones al cocinar o comprar. Todo se mantiene simple y accesible, sin jerga técnica ni pasos extra.
            </p>
        </section>

        <section class="mt-10 grid gap-6 md:grid-cols-2">
            <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                <h2 class="text-xl font-semibold">Inventario con caducidades</h2>
                <ul class="mt-3 space-y-2 text-sm text-[color:var(--km-muted)]">
                    <li>• Registra fechas de vencimiento y prioriza lo que debes consumir antes.</li>
                    <li>• Visualiza productos próximos a caducar en un panel resumido.</li>
                    <li>• Evita desperdicio con recordatorios claros y fáciles de consultar.</li>
                </ul>
            </div>
            <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                <h2 class="text-xl font-semibold">Reposición inteligente</h2>
                <ul class="mt-3 space-y-2 text-sm text-[color:var(--km-muted)]">
                    <li>• Configura cantidades mínimas por producto.</li>
                    <li>• Genera listados automáticos para la compra semanal.</li>
                    <li>• Exporta a CSV o PDF cuando lo necesites.</li>
                </ul>
            </div>
            <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                <h2 class="text-xl font-semibold">Organización por zonas</h2>
                <ul class="mt-3 space-y-2 text-sm text-[color:var(--km-muted)]">
                    <li>• Separa despensa, nevera, congelador o estanterías.</li>
                    <li>• Filtra rápidamente para saber dónde está cada producto.</li>
                    <li>• Ordena ubicaciones por prioridad o frecuencia de uso.</li>
                </ul>
            </div>
            <div class="rounded-2xl border border-[color:var(--km-border)] p-6">
                <h2 class="text-xl font-semibold">Vista móvil rápida</h2>
                <ul class="mt-3 space-y-2 text-sm text-[color:var(--km-muted)]">
                    <li>• Accede desde el navegador del móvil sin instalar apps.</li>
                    <li>• Consulta inventario mientras compras o cocinas.</li>
                    <li>• Diseño ligero para cargar rápido en cualquier conexión.</li>
                </ul>
            </div>
        </section>

        <section class="mt-12 rounded-3xl border border-[color:var(--km-border)] bg-[color:var(--km-bg-2)] p-8">
            <h2 class="text-2xl font-semibold">¿Listo para organizar tu cocina?</h2>
            <p class="mt-3 text-base text-[color:var(--km-muted)]">
                Regístrate y empieza con los productos que más consumes. En pocos minutos tendrás claridad sobre tu stock y podrás dejar de depender de notas sueltas o memoria.
            </p>
            <div class="mt-4 flex flex-wrap gap-3">
                <Link v-if="canRegister" :href="route('register')" class="km-btn w-auto px-5 py-2">
                    Crear cuenta gratis
                </Link>
                <Link :href="route('pricing')" class="km-link">Ver precios</Link>
            </div>
        </section>
    </PublicLayout>
</template>
