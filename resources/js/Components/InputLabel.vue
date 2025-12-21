<script setup>
defineProps({
    value: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <label class="block text-sm font-medium text-[color:var(--km-text)]">
        <span v-if="value">{{ value }}</span>
        <span v-else><slot /></span>
    </label>
</template>
