<script setup>
import { onMounted, ref } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const input = ref(null);

const focus = () => {
    if (input.value) {
        input.value.focus();
    }
};

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
        class="w-full rounded-md border border-gray-300 bg-white
               px-3 py-2 text-sm text-gray-900 placeholder-gray-400
               shadow-sm focus:border-indigo-500 focus:outline-none
               focus:ring-2 focus:ring-indigo-500/50"
        :value="modelValue"
        @input="emit('update:modelValue', $event.target.value)"
    />
</template>
