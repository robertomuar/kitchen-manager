<script setup>
import { onMounted, onBeforeUnmount, ref, nextTick } from 'vue';
import { Html5Qrcode } from 'html5-qrcode';

const emit = defineEmits(['detected', 'error']);

const html5QrCode = ref(null);

const startScanner = async () => {
    try {
        // El div con este id está en el template
        html5QrCode.value = new Html5Qrcode('barcode-scanner');

        await html5QrCode.value.start(
            { facingMode: 'environment' }, // cámara trasera en móvil
            {
                fps: 10,
                qrbox: { width: 250, height: 250 },
            },
            async (decodedText, decodedResult) => {
                try {
                    // Paramos el escáner tras la primera lectura
                    await html5QrCode.value.stop();
                    await html5QrCode.value.clear();
                } catch (e) {
                    // ignoramos errores al parar
                }

                emit('detected', decodedText);
            },
            (errorMessage) => {
                // Errores de escaneo continuos, los ignoramos
                // Si quieres log:
                // console.debug(errorMessage);
            },
        );
    } catch (e) {
        emit('error', e);
    }
};

const stopScanner = async () => {
    if (!html5QrCode.value) return;

    try {
        await html5QrCode.value.stop();
        await html5QrCode.value.clear();
    } catch (e) {
        // ignoramos
    } finally {
        html5QrCode.value = null;
    }
};

onMounted(async () => {
    await nextTick();
    startScanner();
});

onBeforeUnmount(() => {
    stopScanner();
});
</script>

<template>
    <div class="space-y-3">
        <div
            id="barcode-scanner"
            class="w-full aspect-[3/4] overflow-hidden rounded-xl border border-slate-700 bg-black/80"
        ></div>
        <p class="text-xs text-slate-400">
            Apunta la cámara al código de barras hasta que lo detecte.
        </p>
    </div>
</template>
