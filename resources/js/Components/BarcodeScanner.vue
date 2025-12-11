<script setup>
import { onMounted, onBeforeUnmount, ref, nextTick } from 'vue';

const emit = defineEmits(['scanned', 'error', 'closed']);

const html5QrCode = ref(null);
const isStarting = ref(false);

const errorMessage = ref('');
const lastDecoded = ref(''); // Para ver en pantalla que ha leído algo

const stopScanner = async (emitClose = false) => {
    if (!html5QrCode.value) return;

    try {
        console.log('[BarcodeScanner] Parando escáner...');
        await html5QrCode.value.stop();
        await html5QrCode.value.clear();
        console.log('[BarcodeScanner] Escáner parado.');
    } catch (e) {
        console.warn('[BarcodeScanner] Error al parar el escáner:', e);
    } finally {
        html5QrCode.value = null;
        if (emitClose) {
            emit('closed');
        }
    }
};

const startScanner = async () => {
    if (isStarting.value) return;
    isStarting.value = true;
    errorMessage.value = '';
    lastDecoded.value = '';

    try {
        console.log('[BarcodeScanner] Montando escáner...');

        const {
            Html5Qrcode,
            Html5QrcodeSupportedFormats,
            Html5QrcodeScanType,
        } = await import('html5-qrcode');

        console.log('[BarcodeScanner] html5-qrcode importado.');

        html5QrCode.value = new Html5Qrcode('barcode-scanner', {
            verbose: false, // ⬅️ así dejas de ver NotFoundException en la consola
            supportedScanTypes: [Html5QrcodeScanType.SCAN_TYPE_CAMERA],
        });

        const config = {
            fps: 12,

            // Caja ancha (para códigos de barras) que se adapta al tamaño real
            qrbox: (viewWidth, viewHeight) => {
                const width = Math.min(viewWidth * 0.9, 420);
                const height = Math.min(viewHeight * 0.4, 220);
                console.log('[BarcodeScanner] qrbox:', { width, height });
                return { width, height };
            },

            experimentalFeatures: {
                useBarCodeDetectorIfSupported: true,
            },

            formatsToSupport: [
                Html5QrcodeSupportedFormats.EAN_13,
                Html5QrcodeSupportedFormats.EAN_8,
                Html5QrcodeSupportedFormats.CODE_128,
                Html5QrcodeSupportedFormats.CODE_39,
                Html5QrcodeSupportedFormats.QR_CODE,
            ],
        };

        const cameraConfig = {
            facingMode: 'environment', // En PC será la cámara por defecto (Iriun)
        };

        console.log('[BarcodeScanner] Iniciando escáner...');
        await html5QrCode.value.start(
            cameraConfig,
            config,
            async (decodedText, decodedResult) => {
                console.log(
                    '[BarcodeScanner] ✅ DECODIFICADO:',
                    decodedText,
                    decodedResult,
                );
                lastDecoded.value = decodedText;

                // Cerramos el escáner y avisamos al padre
                await stopScanner();
                emit('scanned', decodedText);
            },
            // Errores de escaneo de cada frame -> los ignoramos para no spamear
            () => {},
        );
        console.log('[BarcodeScanner] Escáner iniciado correctamente.');
    } catch (e) {
        console.error('[BarcodeScanner] ❌ Error al iniciar el escáner', e);
        errorMessage.value =
            'No se ha podido acceder a la cámara. Comprueba permisos o que la cámara no esté en uso por otra app.';
        emit('error', e);
        await stopScanner(true);
    } finally {
        isStarting.value = false;
    }
};

onMounted(async () => {
    await nextTick();
    console.log('[BarcodeScanner] onMounted');
    startScanner();
});

onBeforeUnmount(() => {
    console.log('[BarcodeScanner] onBeforeUnmount');
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
            Apunta la cámara al código de barras y colócalo dentro del recuadro horizontal
            hasta que lo detecte.
        </p>

        <p v-if="lastDecoded" class="text-xs text-emerald-400">
            Último código leído:
            <span class="font-mono">{{ lastDecoded }}</span>
        </p>

        <p v-if="errorMessage" class="text-xs text-red-400">
            {{ errorMessage }}
        </p>
    </div>
</template>
