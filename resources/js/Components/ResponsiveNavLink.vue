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
          'block w-full rounded-lg ps-4 pe-4 py-2 text-sm font-medium ' +
          'bg-slate-900/90 text-indigo-100 border border-indigo-500/60 ' +
          'shadow-sm shadow-indigo-500/30 ' +
          'focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-950'
        : // INACTIVO
          'block w-full rounded-lg ps-4 pe-4 py-2 text-sm font-medium ' +
          'text-slate-300 border border-transparent ' +
          'hover:bg-slate-900/60 hover:text-white hover:border-slate-700 ' +
          'focus:outline-none focus:ring-2 focus:ring-slate-600 focus:ring-offset-2 focus:ring-offset-slate-950'
);
</script>

<template>
    <Link :href="href" :class="classes" :as="as" :method="method">
        <slot />
    </Link>
</template>
