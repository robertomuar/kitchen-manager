<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
    // Para compatibilidad con usos anteriores
    variant: {
        type: String,
        default: 'light',
        validator: (value) => ['light', 'dark'].includes(value),
    },
});

const emit = defineEmits(['update:modelValue']);

const input = ref(null);

const isReadyToFocus = () => {
    if (!input.value) return false;

    const element = input.value;
    const isVisible = element.offsetParent !== null && !element.disabled;

    return isVisible;
};

const focus = () => {
    if (isReadyToFocus()) {
        input.value.focus();
    }
};

const canAutofocus = () => {
    const active = document.activeElement;
    return (
        !active ||
        active === document.body ||
        active === document.documentElement
    );
};

onMounted(() => {
    // Respeta autofocus si el input lo tiene, sin robar el foco a otros sitios
    if (input.value && input.value.hasAttribute('autofocus') && canAutofocus()) {
        requestAnimationFrame(() => {
            if (isReadyToFocus()) {
                input.value.focus();
            }
        });
    }
});

defineExpose({ focus });
</script>

<template>
    <input
        ref="input"
        class="km-input"
        :value="modelValue"
        @input="emit('update:modelValue', $event.target.value)"
    />
</template>
