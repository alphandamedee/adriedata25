document.addEventListener('DOMContentLoaded', function () { 
    // Récupération des éléments du DOM nécessaires pour le scan
    const searchInput = document.getElementById('search-input'); // Input pour le résultat du scan
    const scannerVideo = document.getElementById('scanner'); // Élément vidéo pour le scan
    const alertBox = document.getElementById('alert-box'); // Alerte en cas d'erreur
    const scanSound = document.getElementById('scan-beep'); // Son lors d'un scan réussi
    const retryButton = document.getElementById('retry-scan'); // Bouton pour réessayer
    const barcodeModal = document.getElementById('barcodeModal'); // Modal contenant le scanner

    let isScannerActive = false;  // Flag pour suivre l'état du scanner (actif/inactif)
    let hasScanned = false;       // Flag pour éviter les scans multiples

    function startScanner() {
        if (isScannerActive) return; // Évite de démarrer plusieurs fois

        hasScanned = false;
        alertBox?.classList.add('d-none');

        console.log("🟢 Quagga init...");

        Quagga.init({
            inputStream: { // Configuration du flux vidéo
                name: "Live",
                type: "LiveStream",
                target: scannerVideo,
                constraints: {
                    facingMode: "environment" // Utilise la caméra arrière si disponible
                }
            },
            decoder: { // Configuration des types de codes-barres supportés
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

        Quagga.onDetected(function (result) { // Gestion de la détection d'un code-barres
            if (hasScanned) return;
            const code = result.codeResult.code;
            hasScanned = true;
            console.log("✅ Code détecté :", code);
            if (searchInput) {
                searchInput.value = code;
                searchInput.classList.add('is-valid');
            }
            if (scanSound) { // Joue un son de confirmation
                scanSound.play().catch(err => console.warn("Beep error", err));
            }
            const form = document.querySelector('form');
            if (form) { // Soumission automatique du formulaire après scan
                setTimeout(() => form.submit(), 800);
            }
            const modal = bootstrap.Modal.getInstance(barcodeModal);
            if (modal) { // Fermeture automatique de la modale
                setTimeout(() => modal.hide(), 500);
            }
            stopScanner();
        });

        Quagga.onProcessed(function (result) { // Gestion du traitement continu
            if (!result || !result.boxes || result.boxes.length === 0) {
                alertBox?.classList.remove('d-none');
            }
        });
    }

    function stopScanner() { // Fonction pour arrêter le scanner
        if (isScannerActive) {
            Quagga.stop();
            isScannerActive = false;
            console.log("🛑 Quagga arrêté.");
        }
    }

    // Gestionnaires d'événements
    if (retryButton) { // Gestion du bouton de réessai
        retryButton.addEventListener('click', function () {
            stopScanner();
            startScanner();
        });
    }

    if (barcodeModal) { // Gestion des événements de la modale
        barcodeModal.addEventListener('shown.bs.modal', () => {
            setTimeout(() => startScanner(), 300); // Démarre le scanner après l'ouverture
        });

        barcodeModal.addEventListener('hidden.bs.modal', () => {
            stopScanner(); // Arrête le scanner à la fermeture
        });
    }
});
