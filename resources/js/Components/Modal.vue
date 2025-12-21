<script setup>
import { computed, onMounted, onUnmounted, watch } from 'vue';

const props = defineProps({
  show: { type: Boolean, default: false },
  maxWidth: { type: String, default: '2xl' }, // sm, md, lg, xl, 2xl
  closeable: { type: Boolean, default: true },
});

const emit = defineEmits(['close']);

const close = () => {
  if (!props.closeable) return;
  emit('close');
};

const maxWidthClass = computed(() => {
  return {
    sm: 'sm:max-w-sm',
    md: 'sm:max-w-md',
    lg: 'sm:max-w-lg',
    xl: 'sm:max-w-xl',
    '2xl': 'sm:max-w-2xl',
  }[props.maxWidth] ?? 'sm:max-w-2xl';
});

const onKeydown = (e) => {
  if (e.key === 'Escape' && props.show) close();
};

watch(
  () => props.show,
  (val) => {
    document.body.classList.toggle('overflow-hidden', val);
  },
  { immediate: true }
);

onMounted(() => window.addEventListener('keydown', onKeydown));
onUnmounted(() => {
  window.removeEventListener('keydown', onKeydown);
  document.body.classList.remove('overflow-hidden');
});
</script>

<template>
  <Teleport to="body">
    <!-- Backdrop -->
    <Transition
      enter-active-class="ease-out duration-300"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="ease-in duration-200"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-show="show"
        class="fixed inset-0 z-40 bg-black/50"
        @click="close"
      />
    </Transition>

    <!-- Container -->
    <Transition
      enter-active-class="ease-out duration-300"
      enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      enter-to-class="opacity-100 translate-y-0 sm:scale-100"
      leave-active-class="ease-in duration-200"
      leave-from-class="opacity-100 translate-y-0 sm:scale-100"
      leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
      <div
        v-show="show"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6"
        role="dialog"
        aria-modal="true"
      >
        <!-- Panel -->
        <div
          class="km-card w-full transform overflow-hidden transition-all"
          :class="maxWidthClass"
          @click.stop
        >
          <slot />
        </div>
      </div>
    </Transition>
  </Teleport>
</template>
