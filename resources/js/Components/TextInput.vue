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

const focus = () => {
    if (input.value) {
        input.value.focus();
    }
};

const inputClasses = computed(() => {
    const base =
        'w-full rounded-md px-3 py-2 text-sm shadow-sm focus:outline-none transition-colors duration-150';

    if (props.variant === 'dark') {
        return [
            base,
            'border border-slate-700 bg-slate-900/80 text-slate-100 placeholder:text-slate-400',
            'focus:border-rose-500 focus:ring-1 focus:ring-rose-500',
        ];
    }

    return [
        base,
        'border border-gray-300 bg-white text-gray-900 placeholder-gray-400',
        'focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/50',
    ];
});

onMounted(() => {
    // Respetar el autofocus si el input lo tiene
    if (input.value && input.value.hasAttribute('autofocus')) {
        input.value.focus();
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
