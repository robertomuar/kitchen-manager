<script setup>
import { computed } from 'vue';

const props = defineProps({
  busy: { type: Boolean, default: false },
});

const clipId = `kmSteamClip_${Math.random().toString(36).slice(2, 9)}`;
const clipUrl = computed(() => `url(#${clipId})`);

/* Fallbacks por si el tema no carga (evita el “cocinero negro”) */
const C = {
  hat: 'var(--km-ill-hat, #D18B00)',
  face: 'var(--km-ill-face, #323846)',
  body: 'var(--km-ill-body, #D8DCE3)',
  body2: 'var(--km-ill-body2, #C7CDD7)',
  pot: 'var(--km-ill-pot, #D2D6DD)',
  pot2: 'var(--km-ill-pot2, #B8BFCA)',
  bg: 'var(--km-ill-bg, rgba(11,16,36,0.12))',
};
</script>

<template>
  <svg
    viewBox="0 0 520 520"
    class="km-chef h-[520px] w-[520px]"
    :class="{ 'km-chef--busy': props.busy }"
    aria-hidden="true"
    shape-rendering="geometricPrecision"
  >
    <defs>
      <!-- vapor SOLO en la zona de la olla -->
      <clipPath :id="clipId">
        <rect x="190" y="308" width="180" height="150" rx="56" />
      </clipPath>
    </defs>

    <!-- Círculo fondo -->
    <circle cx="260" cy="272" r="190" :fill="C.bg" />

    <!-- Sombrero -->
    <path
      d="M170 178c-22 0-40-18-40-40s18-40 40-40c6 0 12 1 17 3
         9-20 29-33 52-33 31 0 56 24 58 55 25 2 45 23 45 49
         0 27-22 49-49 49H170z"
      :fill="C.hat"
    />

    <!-- Cabeza (separada) -->
    <g>
      <circle cx="260" cy="210" r="44" :fill="C.body" />
      <!-- “cuello”/separación -->
      <path
        d="M232 252c8 10 18 15 28 15s20-5 28-15"
        fill="none"
        stroke="rgba(0,0,0,0.18)"
        stroke-width="10"
        stroke-linecap="round"
      />
      <!-- Cara -->
      <circle cx="246" cy="205" r="6" :fill="C.face" opacity="0.88" />
      <circle cx="274" cy="205" r="6" :fill="C.face" opacity="0.88" />
      <path
        d="M244 225c8 10 24 10 32 0"
        fill="none"
        :stroke="C.face"
        stroke-width="8"
        stroke-linecap="round"
        opacity="0.88"
      />
    </g>

    <!-- Cuerpo (BAJADO para que no toque cabeza) -->
    <g class="km-chef__bob">
      <path
        d="M142 362
           C162 296 208 266 260 266
           C312 266 358 296 378 362
           V412
           C378 442 355 466 326 466
           H194
           C165 466 142 442 142 412
           Z"
        :fill="C.body"
      />

      <!-- volumen -->
      <path
        d="M318 286
           C345 305 360 330 372 362
           V410
           C372 434 352 454 328 454
           H295
           C310 430 314 412 314 392
           C314 354 298 312 318 286Z"
        :fill="C.body2"
        opacity="0.55"
      />

      <!-- brazo izq -->
      <path
        d="M200 378c-34 10-62 40-76 70"
        fill="none"
        :stroke="C.body2"
        stroke-width="20"
        stroke-linecap="round"
        opacity="0.95"
      />

      <!-- brazo der + cuchara -->
      <g class="km-chef__stir">
        <path
          d="M330 362c28 10 52 40 65 70"
          fill="none"
          :stroke="C.body2"
          stroke-width="20"
          stroke-linecap="round"
          opacity="0.95"
        />
        <path
          d="M402 422L468 304"
          fill="none"
          :stroke="C.hat"
          stroke-width="12"
          stroke-linecap="round"
        />
        <circle cx="470" cy="300" r="12" :fill="C.hat" />
      </g>
    </g>

    <!-- Olla -->
    <g>
      <rect x="170" y="368" width="180" height="92" rx="22" :fill="C.pot" />
      <rect x="182" y="381" width="156" height="66" rx="18" :fill="C.pot2" opacity="0.25" />
      <path
        d="M170 406c-22 0-40 15-40 34s18 34 40 34"
        fill="none"
        :stroke="C.pot2"
        stroke-width="16"
        stroke-linecap="round"
      />
      <path
        d="M350 406c22 0 40 15 40 34s-18 34-40 34"
        fill="none"
        :stroke="C.pot2"
        stroke-width="16"
        stroke-linecap="round"
      />
    </g>

    <!-- Vapor (recortado para que NO se meta en la cabeza/cuerpo) -->
    <g class="km-chef__steam" :style="{ clipPath: clipUrl }" transform="translate(0,18)">
      <path
        d="M235 360c-10-12-10-24 0-36s10-24 0-36"
        fill="none"
        :stroke="C.hat"
        stroke-width="10"
        stroke-linecap="round"
        class="km-chef__steam1"
        opacity="0.95"
      />
      <path
        d="M260 356c-12-14-12-28 0-42s12-28 0-42"
        fill="none"
        :stroke="C.hat"
        stroke-width="10"
        stroke-linecap="round"
        class="km-chef__steam2"
        opacity="0.95"
      />
      <path
        d="M285 360c-10-12-10-24 0-36s10-24 0-36"
        fill="none"
        :stroke="C.hat"
        stroke-width="10"
        stroke-linecap="round"
        class="km-chef__steam3"
        opacity="0.95"
      />
    </g>
  </svg>
</template>

<style scoped>
.km-chef {
  opacity: 0.98;
  filter: drop-shadow(0 22px 45px rgba(0, 0, 0, 0.25)) contrast(1.06) saturate(1.06);
}

/* Animación SIEMPRE */
.km-chef .km-chef__bob {
  transform-origin: center;
  transform-box: fill-box;
  animation: km-bob 2.4s ease-in-out infinite;
}

.km-chef .km-chef__stir {
  transform-origin: 360px 412px;
  transform-box: fill-box;
  animation: km-stir 1.35s ease-in-out infinite;
}

.km-chef .km-chef__steam1 { animation: km-steam 1.55s ease-in-out infinite; }
.km-chef .km-chef__steam2 { animation: km-steam 1.75s ease-in-out infinite; }
.km-chef .km-chef__steam3 { animation: km-steam 1.65s ease-in-out infinite; }

/* Busy acelera */
.km-chef--busy .km-chef__bob { animation-duration: 1.10s; }
.km-chef--busy .km-chef__stir { animation-duration: 0.85s; }
.km-chef--busy .km-chef__steam1 { animation-duration: 0.90s; }
.km-chef--busy .km-chef__steam2 { animation-duration: 1.05s; }
.km-chef--busy .km-chef__steam3 { animation-duration: 0.98s; }

@keyframes km-bob {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(6px); }
}

@keyframes km-stir {
  0%   { transform: rotate(-7deg) translateY(0); }
  50%  { transform: rotate(9deg) translateY(2px); }
  100% { transform: rotate(-7deg) translateY(0); }
}

@keyframes km-steam {
  0%   { transform: translateY(0); opacity: 0.55; }
  50%  { transform: translateY(-10px); opacity: 1; }
  100% { transform: translateY(0); opacity: 0.55; }
}

@media (prefers-reduced-motion: reduce) {
  .km-chef .km-chef__bob,
  .km-chef .km-chef__stir,
  .km-chef .km-chef__steam1,
  .km-chef .km-chef__steam2,
  .km-chef .km-chef__steam3 {
    animation: none !important;
  }
}
</style>
