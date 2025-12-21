<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-xl border border-[color:var(--km-border)] bg-[color:var(--km-card)] px-4 py-2 text-xs font-semibold uppercase tracking-widest text-[color:var(--km-text)] shadow-sm transition duration-150 ease-in-out hover:bg-white/80 focus:outline-none focus:ring-2 focus:ring-[color:var(--km-ring)] focus:ring-offset-2 disabled:opacity-25"
    >
        <slot />
    </button>
</template>
