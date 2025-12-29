<script setup>
import { computed } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

const page = usePage();

const kitchenData = computed(() => page.props?.kitchens ?? null);
const availableKitchens = computed(() => kitchenData.value?.available ?? []);
const currentKitchen = computed(() => kitchenData.value?.current ?? null);

const options = computed(() => {
    if (availableKitchens.value.length) {
        return availableKitchens.value;
    }

    return currentKitchen.value ? [currentKitchen.value] : [];
});

const selectedId = computed(() => currentKitchen.value?.id ?? '');
const isDisabled = computed(() => options.value.length <= 1);
const shouldRender = computed(() => !!kitchenData.value && options.value.length > 0);

const onChange = (event) => {
    const target = event?.target;
    const kitchenId = target?.value;

    if (!kitchenId || kitchenId === selectedId.value) {
        return;
    }

    router.post(
        route('kitchen.select'),
        { kitchen_id: kitchenId },
        { preserveScroll: true, preserveState: true },
    );
};
</script>

<template>
    <div
        v-if="shouldRender"
        class="flex min-w-0 items-center gap-2"
    >
        <label for="kitchen_switcher" class="sr-only">
            Seleccionar cocina
        </label>

        <span
            v-if="currentKitchen?.color"
            class="inline-flex h-3 w-3 rounded-full border border-[color:var(--km-border)]"
            :style="{ backgroundColor: currentKitchen.color || 'transparent' }"
        ></span>

        <div class="relative min-w-[10rem] max-w-[12rem]">
            <select
                id="kitchen_switcher"
                class="km-input h-9 w-full truncate rounded-full px-3 text-xs focus:ring-[color:var(--km-ring)]"
                :value="selectedId"
                :disabled="isDisabled"
                @change="onChange"
            >
                <option
                    v-for="kitchen in options"
                    :key="kitchen.id"
                    :value="kitchen.id"
                >
                    {{ kitchen.name }}
                </option>
            </select>
        </div>
    </div>
</template>
