document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('search-input');
    const scannerVideo = document.getElementById('scanner');
    const alertBox = document.getElementById('alert-box');
    const scanSound = document.getElementById('scan-beep');
    const retryButton = document.getElementById('retry-scan');
    const barcodeModal = document.getElementById('barcodeModal');

    let isScannerActive = false;
    let hasScanned = false;

    function startScanner() {
        if (isScannerActive) return;

        hasScanned = false;
        alertBox?.classList.add('d-none');

        console.log("🟢 Quagga init...");

        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: scannerVideo,
                constraints: {
                    
                    facingMode: "environment"
                }
            },
            decoder: {
                readers: ["code_39_reader", "code_128_reader", "ean_reader", "ean_8_reader", "upc_reader"]
            },

            locate: true
        }, function (err) {
            if (err) {
                console.error("❌ Erreur Quagga :", err);
                return;
            }

            console.log("✅ Quagga a démarré !");
            Quagga.start();
            isScannerActive = true;
        });

        Quagga.onDetected(function (result) {
            if (hasScanned) return;
            const code = result.codeResult.code;
  
            hasScanned = true;

            console.log("✅ Code détecté :", code);

            if (searchInput) {
                searchInput.value = code;
                searchInput.classList.add('is-valid');
            }

            if (scanSound) {
                scanSound.play().catch(err => console.warn("Beep error", err));
            }

            // Soumettre le formulaire si besoin :
            const form = document.querySelector('form');
            if (form) {
                setTimeout(() => form.submit(), 800);
            }

            // Fermer la modale
            const modal = bootstrap.Modal.getInstance(barcodeModal);
            if (modal) {
                setTimeout(() => modal.hide(), 500);
            }

            stopScanner();
        });

        Quagga.onProcessed(function (result) {
            if (!result || !result.boxes || result.boxes.length === 0) {
                alertBox?.classList.remove('d-none');
            }
        });
    }

    function stopScanner() {
        if (isScannerActive) {
            Quagga.stop();
            isScannerActive = false;
            console.log("🛑 Quagga arrêté.");
        }
    }

    if (retryButton) {
        retryButton.addEventListener('click', function () {
            stopScanner();
            startScanner();
        });
    }

    if (barcodeModal) {
        barcodeModal.addEventListener('shown.bs.modal', () => {
            setTimeout(() => startScanner(), 300);
        });

        barcodeModal.addEventListener('hidden.bs.modal', () => {
            stopScanner();
        });
    }
});
