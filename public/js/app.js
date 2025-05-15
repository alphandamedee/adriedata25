// Initialisation de DataTables avec en-tête fixe et pagination KnpPaginator
// Assurez-vous que jQuery et DataTables sont chargés avant ce script
// Initialisation DataTable (corrigée et nettoyée)
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
    
});

