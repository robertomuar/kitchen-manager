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
          'relative inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ' +
          'text-indigo-100 bg-slate-900/90 border border-indigo-400/70 ' +
          'shadow-sm shadow-indigo-500/30 transition-colors duration-150'
        : // INACTIVO
          'inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ' +
          'text-slate-300/90 border border-transparent ' +
          'hover:text-white hover:bg-slate-900/60 hover:border-slate-700 ' +
          'transition-colors duration-150'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
