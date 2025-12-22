<script setup>
import TermsGate from '@/Components/TermsGate.vue';
import CookieConsentProvider from '@/Components/CookieConsentProvider.vue';
import AppFooter from '@/Components/AppFooter.vue';

defineProps({
  App: {
    type: [Object, Function],
    required: true,
  },
  props: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <TermsGate />
  <CookieConsentProvider>
    <component :is="App" v-bind="props" />
    <AppFooter />
  </CookieConsentProvider>
</template>
