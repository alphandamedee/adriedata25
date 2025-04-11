document.addEventListener('DOMContentLoaded', function () {
    const scannerContainer = document.getElementById('scanner');
    const alertBox = document.getElementById('alert-box');
    const scanSound = document.getElementById('scan-beep');
    const retryButton = document.getElementById('retry-scan');
    const scanButton = document.getElementById('scan-barcode-btn');
    const searchInput = document.getElementById('search-input');

    let isScannerActive = false;
    let hasScanned = false;

    function startScanner() {
        hasScanned = false;
        alertBox?.classList.add('d-none');

        console.log("🟢 Initialisation de Quagga...");

        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: scannerContainer,
                constraints: {
                    width: 1280,
                    height: 720,
                    facingMode: "environment"
                }
            },
            locator: {
                patchSize: "large", // ✅ Réduction pour améliorer la précision
                halfSample: false
            },
            decoder: {
                readers: [
                    "code_39_reader",
                    "code_128_reader",
                    "ean_reader",
                    "ean_8_reader", // ✅ Ajout d'un format supplémentaire
                    "upc_reader",
                    "code_39_vin_reader"
                ]
            },
            locate: true,
            numOfWorkers: 4,
            debug: true
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
            hasScanned = true;

            const code = result.codeResult.code;
            console.log("✅ Code détecté :", code);

            if (searchInput) {
                console.log("Champ de recherche trouvé :", searchInput);
                searchInput.value = code;
                searchInput.classList.add('is-valid');
            } else {
                console.error("Champ de recherche introuvable !");
            }

            if (scanSound) {
                scanSound.play().catch(error => console.error("Erreur lors de la lecture du son :", error));
            }

            stopScanner();

            const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeModal'));
            if (modal) {
                setTimeout(() => modal.hide(), 300);
            }

            const barcodeModal = document.getElementById('barcodeModal');
                if (barcodeModal) {
                    barcodeModal.addEventListener('shown.bs.modal', startScanner);
                    barcodeModal.addEventListener('hidden.bs.modal', stopScanner);
                } else {
                    console.error("Modal introuvable !");
                }

            const form = document.querySelector('form');
            if (form) {
                console.log("Formulaire trouvé :", form);
                setTimeout(() => form.submit(), 600);
            } else {
                console.error("Formulaire introuvable !");
            }
        });

        Quagga.onProcessed(function (result) {
            if (!result) {
                console.log("🧪 Image analysée par Quagga : Undefined");
                return;
            }
            console.log("🧪 Image analysée :", result);

            let drawingCtx = Quagga.canvas.ctx.overlay,
                drawingCanvas = Quagga.canvas.dom.overlay;

            drawingCtx.clearRect(0, 0, drawingCanvas.width, drawingCanvas.height);

            if (result.boxes) {
                result.boxes.forEach(function (box) {
                    Quagga.ImageDebug.drawPath(box, { x: 0, y: 1 }, drawingCtx, { color: "green", lineWidth: 2 });
                });
                console.log("🟢 Zone détectée :", result.boxes);
            } else {
                console.warn("⚠️ Aucune zone détectée.");
            }
        });
    }

    function stopScanner() {
        if (isScannerActive) {
            Quagga.stop();
            isScannerActive = false;
            console.log("🛑 Scanner arrêté !");
        }
    }

    if (retryButton) {
        retryButton.addEventListener('click', function () {
            stopScanner();
            startScanner();
        });
    }

    const barcodeModal = document.getElementById('barcodeModal');
    if (barcodeModal) {
        barcodeModal.addEventListener('shown.bs.modal', startScanner);
        barcodeModal.addEventListener('hidden.bs.modal', stopScanner);
    }
});



/////////
document.addEventListener('DOMContentLoaded', function () {
    const scannerContainer = document.getElementById('scanner');
    const alertBox = document.getElementById('alert-box');
    const scanSound = document.getElementById('scan-beep');
    const retryButton = document.getElementById('retry-scan');
    const scanButton = document.getElementById('scan-barcode-btn');
    const searchInput = document.getElementById('search-input');
    
    let isScannerActive = false;
    let hasScanned = false;

    function startScanner() {
        if (isScannerActive) return; // Évite de démarrer plusieurs fois
        isScannerActive = true;

        hasScanned = false;
        alertBox?.classList.add('d-none'); // Masquer l'alerte

        Quagga.init({
            inputStream: {
                name: "Live",
                type: "LiveStream",
                target: scannerContainer,
                constraints: {
                    width: 640,
                    height: 480,
                    facingMode: "environment" // Utiliser la caméra arrière
                }
            },
            decoder: {
                readers: ["code_128_reader", "ean_39_reader", "ean_8_reader", "upc_reader"] // Types de codes-barres pris en charge
            },
            locate: true
        }, function (err) {
            if (err) {
                console.error("Erreur lors de l'initialisation de Quagga :", err);
                return;
            }
            Quagga.start();
            isScannerActive = true;
            console.log("Scanner démarré !");

        });

        Quagga.onDetected(function (result) {
            if (hasScanned) return;
            hasScanned = true;
            const code = result.codeResult.code;
            console.log("Code détecté :", code);

    //         if (code) {
    //             // Jouer un son de confirmation
    //             scanSound.play();

    //             // Remplir le champ de recherche avec le code détecté
    //             if (searchInput) {
    //                 searchInput.value = code;
    //                 searchInput.classList.add('is-valid');
    //             }

    //             // Arrêter le scanner après détection
    //             stopScanner();

    //             // Fermer le modal après un court délai
    //             const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeModal'));
    //             if (modal) {
    //                 setTimeout(() => modal.hide(), 300);
    //             }
    //         } else {
    //             alertBox?.classList.remove('d-none');
    //             alertBox.textContent = "Aucun code détecté. Veuillez réessayer.";
    //         }
    //     });
    // }
        if (searchInput) {
            searchInput.value = code;
            searchInput.classList.add('is-valid');
        }

        if (scanSound) {
            scanSound.play().catch(error => console.error("Erreur audio :", error));
        }

        setTimeout(() => {
            Quagga.stop();
            isScannerActive = false;
            const modal = bootstrap.Modal.getInstance(document.getElementById('barcodeModal'));
            modal?.hide();
        }, 600);
        });
        }

    function stopScanner() {
        if (!isScannerActive) return; // Évite d'arrêter plusieurs fois
        Quagga.stop();
        isScannerActive = false;
        console.log("🛑 Scanner arrêté");

        // Désactiver explicitement le flux vidéo
        const videoTracks = Quagga.CameraAccess.getActiveTrack();
        if (videoTracks) {
            videoTracks.stop();
            console.log("Flux vidéo arrêté !");
        }

        console.log("Scanner arrêté !");
    }

    // Démarrer le scanner lorsque le bouton est cliqué
    scanButton?.addEventListener('click', function () {
        startScanner();
    });

    // Réessayer le scan
    retryButton?.addEventListener('click', function () {
        stopScanner();
        startScanner();
    });

    // Arrêter le scanner lorsque le modal est fermé
    const barcodeModal = document.getElementById('barcodeModal');
    if (barcodeModal) {
        barcodeModal.addEventListener('hidden.bs.modal', function () {
            stopScanner();
        });
    }

    // Arrêter le scanner lorsque la page est quittée ou rechargée
    window.addEventListener('beforeunload', function () {
        stopScanner();
    });
});