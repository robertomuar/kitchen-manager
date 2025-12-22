<script setup>
import { computed, onBeforeUnmount, onMounted, provide, ref, watch } from 'vue';
import CookieConsent from '@/Components/CookieConsent.vue';
import CookieSettingsModal from '@/Components/CookieSettingsModal.vue';
import { getJsonCookie, setJsonCookie } from '@/utils/cookies';
import {
  COOKIE_CONSENT_COOKIE,
  COOKIE_VERSION,
  cookieConsentKey,
} from '@/utils/consent';

const consent = ref(null);
const showSettings = ref(false);

const showBanner = computed(() => !consent.value);

const readConsent = () => {
  const stored = getJsonCookie(COOKIE_CONSENT_COOKIE);
  if (!stored || stored.version !== COOKIE_VERSION) return null;
  return stored;
};

const persistConsent = (payload) => {
  consent.value = payload;
  setJsonCookie(COOKIE_CONSENT_COOKIE, payload, 180);
};

const applyAnalyticsConsent = (payload) => {
  if (!payload?.analytics) {
    if (typeof window !== 'undefined' && typeof window.disableAnalytics === 'function') {
      window.disableAnalytics();
    }
    return;
  }

  if (typeof window !== 'undefined' && typeof window.enableAnalytics === 'function') {
    window.enableAnalytics();
  }
};

const acceptAll = () => {
  const payload = {
    version: COOKIE_VERSION,
    choice: 'all',
    analytics: true,
    acceptedAt: new Date().toISOString(),
  };
  persistConsent(payload);
  applyAnalyticsConsent(payload);
  showSettings.value = false;
};

const rejectNonEssential = () => {
  const payload = {
    version: COOKIE_VERSION,
    choice: 'necessary',
    analytics: false,
    acceptedAt: new Date().toISOString(),
  };
  persistConsent(payload);
  applyAnalyticsConsent(payload);
  showSettings.value = false;
};

const saveCustom = (analytics) => {
  const payload = {
    version: COOKIE_VERSION,
    choice: 'custom',
    analytics: Boolean(analytics),
    acceptedAt: new Date().toISOString(),
  };
  persistConsent(payload);
  applyAnalyticsConsent(payload);
  showSettings.value = false;
};

const openSettings = () => {
  showSettings.value = true;
};

const closeSettings = () => {
  showSettings.value = false;
};

const registerGlobalOpen = () => {
  if (typeof window === 'undefined') return;
  window.openCookieSettings = openSettings;
};

const clearGlobalOpen = () => {
  if (typeof window === 'undefined') return;
  if (window.openCookieSettings === openSettings) {
    delete window.openCookieSettings;
  }
};

provide(cookieConsentKey, {
  consent,
  openSettings,
});

onMounted(() => {
  consent.value = readConsent();
  applyAnalyticsConsent(consent.value);
  registerGlobalOpen();
});

onBeforeUnmount(() => {
  clearGlobalOpen();
});

watch(consent, (value) => {
  applyAnalyticsConsent(value);
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
