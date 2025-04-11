document.addEventListener('DOMContentLoaded', function () {
    const startDateInput = document.getElementById('start-date');
    const endDateInput = document.getElementById('end-date');
    const realizationFilter = document.getElementById('realization-filter');
    const filterButton = document.getElementById('filter-button');
    const interventionCount = document.getElementById('intervention-count');
    const productCount = document.getElementById('product-count');

    const interventionsChartCtx = document.getElementById('interventionsChart').getContext('2d');
    const productsChartCtx = document.getElementById('productsChart').getContext('2d');

    let interventionsChart = new Chart(interventionsChartCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Interventions',
                data: [],
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    let productsChart = new Chart(productsChartCtx, {
        type: 'line',
        data: {
            labels: [],
            datasets: [{
                label: 'Produits Intervenus',
                data: [],
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    function updateDashboard() {
        const startDate = startDateInput.value;
        const endDate = endDateInput.value;
        const filter = realizationFilter.value;

        fetch(`/api/dashboard-data?start=${startDate}&end=${endDate}&filter=${filter}`)
            .then(response => {
                console.log("HTTP Response:", response); // Log the full response
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log("📊 Données reçues de l'API :", data);
                interventionCount.textContent = data.interventionsCount || 0;
                productCount.textContent = data.uniqueProductsCount || 0;

                interventionsChart.data.labels = data.labels || [];
                interventionsChart.data.datasets[0].data = data.interventionsData || [];
                interventionsChart.update();

                productsChart.data.labels = data.labels || [];
                productsChart.data.datasets[0].data = data.productsData || [];
                productsChart.update();
            })
            .catch(error => {
                console.error('❌ Erreur lors de la récupération des données du tableau de bord :', error);
                alert('Erreur lors du chargement des données du dashboard.');
            });
    }

    filterButton.addEventListener('click', updateDashboard);
    updateDashboard();
});