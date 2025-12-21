<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button
        class="km-btn inline-flex w-auto items-center justify-center px-5 py-2 text-xs uppercase tracking-widest"
        :class="{ 'opacity-25': disabled }"
        :disabled="disabled"
        :type="type"
    >
        <slot />
    </button>
</template>
