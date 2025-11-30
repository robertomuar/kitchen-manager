<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';

const props = defineProps({
    show: {
        type: Boolean,
        default: false,
    },
    maxWidth: {
        type: String,
        default: '2xl',
    },
    closeable: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);
const dialog = ref();
const showSlot = ref(props.show);

watch(
    () => props.show,
    () => {
        if (props.show) {
            document.body.style.overflow = 'hidden';
            showSlot.value = true;

            dialog.value?.showModal();
        } else {
            document.body.style.overflow = '';

            setTimeout(() => {
                dialog.value?.close();
                showSlot.value = false;
            }, 200);
        }
    },
);

const close = () => {
    if (props.closeable) {
        emit('close');
    }
};

const closeOnEscape = (e) => {
    if (e.key === 'Escape') {
        e.preventDefault();

        if (props.show) {
            close();
        }
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));

onUnmounted(() => {
    document.removeEventListener('keydown', closeOnEscape);

    document.body.style.overflow = '';
});

const maxWidthClass = computed(() => {
    return {
        sm: 'sm:max-w-sm',
        md: 'sm:max-w-md',
        lg: 'sm:max-w-lg',
        xl: 'sm:max-w-xl',
        '2xl': 'sm:max-w-2xl',
    }[props.maxWidth];
});
</script>

<template>
    <dialog
        ref="dialog"
        class="z-50 m-0 min-h-full min-w-full overflow-y-auto bg-transparent backdrop:bg-slate-950/80"
    >
        <div class="fixed inset-0 z-50 overflow-y-auto px-4 py-10 sm:px-0" scroll-region>
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
                    class="fixed inset-0 transform bg-slate-950/70 transition-all"
                    @click="close"
                />
            </Transition>

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
                    class="mb-10 transform overflow-hidden rounded-2xl border border-slate-700/80 bg-slate-900/95 text-slate-50 shadow-2xl ring-1 ring-slate-700/60 transition-all sm:mx-auto sm:w-full"
                    :class="maxWidthClass"
                >
                    <div v-if="$slots.title || closeable" class="flex items-start justify-between gap-3 border-b border-slate-800/70 px-5 py-4 sm:px-6">
                        <h3 class="text-base font-semibold leading-6 text-slate-100">
                            <slot name="title" />
                        </h3>

                        <button
                            v-if="closeable"
                            type="button"
                            class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-slate-800/70 text-slate-300 transition hover:bg-slate-700/70 hover:text-white focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:ring-offset-2 focus:ring-offset-slate-900"
                            @click="close"
                        >
                            <span class="sr-only">Cerrar</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 20 20"
                                fill="currentColor"
                                class="h-5 w-5"
                            >
                                <path
                                    fill-rule="evenodd"
                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                    clip-rule="evenodd"
                                />
                            </svg>
                        </button>
                    </div>

                    <div v-if="showSlot" class="space-y-6 px-5 py-6 sm:px-6 sm:py-7">
                        <slot name="content">
                            <slot />
                        </slot>
                    </div>

                    <div v-if="$slots.footer" class="border-t border-slate-800/70 bg-slate-950/60 px-5 py-4 sm:px-6">
                        <slot name="footer" />
                    </div>
                </div>
            </Transition>
        </div>
    </dialog>
</template>
