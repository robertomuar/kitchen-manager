<script setup>
defineProps({
  class: { type: String, default: 'h-6 w-6' },
});
</script>

<template>
  <!--
    El icono original queda “arriba” porque su path está entre y=3..24.
    Lo bajamos ~18px para centrarlo en un viewBox 64x64.
  -->
  <svg
    :class="['block', $props.class]"
    viewBox="0 0 64 64"
    fill="none"
    xmlns="http://www.w3.org/2000/svg"
    aria-hidden="true"
    focusable="false"
    preserveAspectRatio="xMidYMid meet"
  >
    <g transform="translate(0, 18)">
      <path
        d="M20 24c-4.4 0-8-3.6-8-8s3.6-8 8-8c1 0 2 .2 2.9.5C25.9 5.8 29.7 3 34.2 3c5.6 0 10.1 4.3 10.4 9.8
           4.5.3 8.1 4.1 8.1 8.7 0 4.8-3.9 8.7-8.7 8.7H20z"
        fill="currentColor"
      />
    </g>
  </svg>
</template>
