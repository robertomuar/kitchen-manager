<script setup>
import { computed, provide, ref } from 'vue';
import CookieConsent from '@/Components/CookieConsent.vue';
import CookieSettingsModal from '@/Components/CookieSettingsModal.vue';
import {
  cookieConsentKey,
  createCookieConsent,
  getCookieConsent,
  saveCookieConsent,
} from '@/utils/consent';

const consent = ref(getCookieConsent());
const showSettings = ref(false);

const showBanner = computed(() => !consent.value);

const persistConsent = (payload) => {
  consent.value = payload;
  saveCookieConsent(payload);
};

const acceptAll = () => {
  persistConsent(createCookieConsent({ choice: 'all', analytics: true }));
  showSettings.value = false;
};

const rejectNonEssential = () => {
  persistConsent(createCookieConsent({ choice: 'necessary', analytics: false }));
  showSettings.value = false;
};

const saveCustom = (analytics) => {
  persistConsent(createCookieConsent({ choice: 'custom', analytics }));
  showSettings.value = false;
};

const openSettings = () => {
  showSettings.value = true;
};

const closeSettings = () => {
  showSettings.value = false;
};

provide(cookieConsentKey, {
  consent,
  openSettings,
});
</script>

<template>
  <CookieConsent
    v-if="showBanner"
    @accept-all="acceptAll"
    @reject="rejectNonEssential"
    @configure="openSettings"
  />
  <CookieSettingsModal
    :open="showSettings"
    :consent="consent"
    @close="closeSettings"
    @accept-all="acceptAll"
    @reject="rejectNonEssential"
    @save="saveCustom"
  />
  <slot />
</template>
