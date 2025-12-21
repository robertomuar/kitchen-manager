<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: String,
    active: Boolean,
});

const classes = computed(() =>
    props.active
        ? // ACTIVO
          'relative inline-flex items-center rounded-full px-3 py-1 text-sm font-semibold ' +
          'text-[color:var(--km-text)] bg-[color:var(--km-card)] border border-[color:var(--km-border)] ' +
          'shadow-sm transition-colors duration-150'
        : // INACTIVO
          'inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ' +
          'text-[color:var(--km-muted)] border border-transparent ' +
          'hover:text-[color:var(--km-text)] hover:bg-[color:var(--km-bg-2)] hover:border-[color:var(--km-border)] ' +
          'transition-colors duration-150'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
