<script setup>
import { computed, onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
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

const inputClasses = computed(() => {
    const base =
        'w-full rounded-xl border px-3 py-2 text-sm shadow-sm focus:outline-none transition-colors duration-150 caret-slate-100';

    if (props.variant === 'dark') {
        return [
            base,
            'border-slate-700 bg-slate-950/80 text-slate-50 placeholder:text-slate-400',
            'focus:border-indigo-400 focus:ring-2 focus:ring-indigo-500/50',
        ];
    }

    return [
        base,
        'border-gray-300 bg-white text-gray-900 placeholder:text-gray-500 caret-gray-700',
        'focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50',
    ];
});

const canAutofocus = () => {
    const active = document.activeElement;
    return !active || active === document.body || active === document.documentElement;
};

onMounted(() => {
    // Respetar el autofocus si el input lo tiene, pero sin interrumpir otro foco activo
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
        :class="inputClasses"
        :value="modelValue"
        @input="emit('update:modelValue', $event.target.value)"
    />
</template>
