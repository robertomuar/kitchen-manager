<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    method: {
        type: String,
        default: 'get',
    },
    as: {
        type: String,
        default: 'a',
    },
    data: {
        type: Object,
        default: () => ({}),
    },
    preserveScroll: {
        type: Boolean,
        default: false,
    },
    preserveState: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <Link
        :href="props.href"
        :method="props.method"
        :as="props.as"
        :data="props.data"
        :preserve-scroll="props.preserveScroll"
        :preserve-state="props.preserveState"
        class="block w-full px-4 py-2 text-start text-sm leading-5 text-[color:var(--km-text)] transition duration-150 ease-in-out hover:bg-[color:var(--km-bg-2)] focus:bg-[color:var(--km-bg-2)] focus:outline-none"
    >
        <slot />
    </Link>
</template>
