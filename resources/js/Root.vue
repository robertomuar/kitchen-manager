<script setup>
import TermsGate from '@/Components/TermsGate.vue';
import CookieConsentProvider from '@/Components/CookieConsentProvider.vue';
import AppFooter from '@/Components/AppFooter.vue';

defineProps({
  App: {
    type: [Object, Function],
    required: true,
  },
  pageProps: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <TermsGate />
  <CookieConsentProvider>
    <component :is="App" v-bind="pageProps" />
    <AppFooter />
  </CookieConsentProvider>
</template>
