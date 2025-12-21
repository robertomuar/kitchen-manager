<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
    as: {
        type: String,
        default: 'a',
    },
    method: {
        type: String,
        default: 'get',
    },
});

const classes = computed(() =>
    props.active
        ? // ACTIVO
          'block w-full rounded-xl ps-4 pe-4 py-2 text-sm font-semibold ' +
          'bg-[color:var(--km-card)] text-[color:var(--km-text)] border border-[color:var(--km-border)] ' +
          'shadow-sm focus:outline-none focus:ring-2 focus:ring-[color:var(--km-ring)] focus:ring-offset-2'
        : // INACTIVO
          'block w-full rounded-xl ps-4 pe-4 py-2 text-sm font-medium ' +
          'text-[color:var(--km-muted)] border border-transparent ' +
          'hover:bg-[color:var(--km-bg-2)] hover:text-[color:var(--km-text)] hover:border-[color:var(--km-border)] ' +
          'focus:outline-none focus:ring-2 focus:ring-[color:var(--km-ring)] focus:ring-offset-2'
);
</script>

<template>
    <Link :href="href" :class="classes" :as="as" :method="method">
        <slot />
    </Link>
</template>
