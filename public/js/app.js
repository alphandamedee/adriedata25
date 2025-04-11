document.addEventListener('DOMContentLoaded', function () {
    // ✅ Initialisation DataTable (corrigée et nettoyée)
    const table = $('#produit-table');
    if (table.length) {
        if ($.fn.DataTable.isDataTable(table)) {
            table.DataTable().destroy(); // Nettoyer une instance précédente s'il y a lieu
        }

        table.DataTable({
            destroy: true,
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            language: {
                url:  "https://cdn.datatables.net/plug-ins/1.13.4/i18n/fr-FR.json"  // ✅ Lien correct vers la version française
            }
        });
    }
});


