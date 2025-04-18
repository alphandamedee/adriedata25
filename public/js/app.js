// document.addEventListener('DOMContentLoaded', function () {
//     // ✅ Initialisation DataTable (corrigée et nettoyée)
//     const table = $('#produit-table');
//     if (table.length) {
//         if ($.fn.DataTable.isDataTable(table)) {
//             table.DataTable().destroy(); // Nettoyer une instance précédente s'il y a lieu
//         }

//         table.DataTable({
//             destroy: true,
//             paging: false,
//             lengthChange: true,
//             searching: true,
//             ordering: true,
//             info: false,
//             autoWidth: false,
//             responsive: true,
//             language: {
//                 url:  "https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"  // ✅ Lien correct vers la version française
//             }
//         });
//     }
// });

// Initialisation de DataTables avec en-tête fixe et pagination KnpPaginator
// Assurez-vous que jQuery et DataTables sont chargés avant ce script
// ✅ Initialisation DataTable (corrigée et nettoyée)
document.addEventListener('DOMContentLoaded', function () {
    const table = $('#produit-table');
    if (table.length) {
        if ($.fn.DataTable.isDataTable(table)) {
            table.DataTable().destroy(); // Nettoyer une instance précédente s'il y a lieu
        }
    }
    // Configuration DataTables avec en-tête fixe
    $('#produit-table').DataTable({
        paging: true,          // Désactiver la pagination DataTables (vous utilisez déjà KnpPaginator)
        info: true,            // Masquer les infos "Showing X to Y of Z entries"
        ordering: true,
        searching: true,       // Désactiver la recherche DataTables (vous avez déjà votre propre recherche)
        scrollY: 'calc(100vh - 300px)', // Hauteur de défilement, ajustez selon vos besoins
        scrollCollapse: true,   // Réduire la hauteur quand il y a peu de lignes
        fixedHeader: true,      // Activer l'en-tête fixe
        language: {             // Traduction en français
            emptyTable: "Aucun produit trouvé",
            zeroRecords: "Aucun résultat correspondant"
        }
    });
    
    // // Le reste de votre code JS existant
    // const input = document.getElementById('search-input');
    // const resultBox = document.getElementById('autocomplete-list');
    
    // input.addEventListener('input', function() {
    //     const query = this.value;
    //     if (query.length < 3) {
    //         resultBox.innerHTML = ''; // Effacer les résultats si la requête est trop courte
    //         return;
    //     }
        
    //     fetch(`/produit/search/${query}`)
    //         .then(response => response.json())
    //         .then(data => {
    //             resultBox.innerHTML = ''; // Effacer les résultats précédents
    //             data.forEach(item => {
    //                 const div = document.createElement('div');
    //                 div.textContent = item.name; // Assurez-vous que 'name' est la propriété correcte
    //                 div.classList.add('autocomplete-item');
    //                 div.addEventListener('click', () => {
    //                     input.value = item.name; // Remplir le champ de recherche avec le nom du produit
    //                     resultBox.innerHTML = ''; // Effacer les résultats après sélection
    //                 });
    //                 resultBox.appendChild(div);
    //             });
    //         })
    //         .catch(error => console.error('Erreur:', error));
    // });
});

