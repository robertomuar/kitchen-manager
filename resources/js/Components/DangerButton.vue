<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button
        class="inline-flex items-center rounded-xl border border-rose-500/40 bg-rose-500/15 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-rose-700 transition duration-150 ease-in-out hover:bg-rose-500/20 focus:outline-none focus:ring-2 focus:ring-rose-400/40 focus:ring-offset-2 active:bg-rose-500/25"
        :class="{ 'opacity-25': disabled }"
        :disabled="disabled"
        :type="type"
    >
        <slot />
    </button>
</template>
