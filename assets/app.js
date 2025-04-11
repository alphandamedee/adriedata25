import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';

console.log('This log comes from assets/app.js - welcome to AssetMapper! 🎉');

// document.addEventListener('DOMContentLoaded', function() {
//     const scanBarcodeBtn = document.getElementById('scan-barcode-btn');
//     const scannerContainer = document.getElementById('scanner-container');
//     const searchInput = document.getElementById('search-input');

//     if (scanBarcodeBtn) {
//         scanBarcodeBtn.addEventListener('click', function() {
//             scannerContainer.style.display = 'block';

//             Quagga.init({
//                 inputStream: {
//                     name: "Live",
//                     type: "LiveStream",
//                     target: document.querySelector('#scanner'),
//                     constraints: {
//                         width: 640,
//                         height: 480,
//                         facingMode: "environment"
//                     },
//                 },
//                 decoder: {
//                     readers: ["code_128_reader", "ean_reader", "ean_8_reader", "code_39_reader", "code_39_vin_reader", "codabar_reader", "upc_reader", "upc_e_reader", "i2of5_reader"]
//                 },
//             }, function(err) {
//                 if (err) {
//                     console.log(err);
//                     return;
//                 }
//                 console.log("Initialization finished. Ready to start");
//                 Quagga.start();
//             });

//             Quagga.onDetected(function(result) {
//                 const code = result.codeResult.code;
//                 searchInput.value = code;
//                 Quagga.stop();
//                 scannerContainer.style.display = 'none';
//             });
//         });
//     }
// });