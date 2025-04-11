document.addEventListener('DOMContentLoaded', function () {
    const table = document.getElementById('interventions-table');
    const headers = table.querySelectorAll('.sort-btn');

    headers.forEach(header => {
        header.addEventListener('click', function () {
            const column = this.getAttribute('data-column');
            const order = this.getAttribute('data-order');
            const newOrder = order === 'asc' ? 'desc' : 'asc';
            this.setAttribute('data-order', newOrder);

            sortTable(table, column, newOrder);
        });
    });

    function sortTable(table, column, order) {
        const tbody = table.querySelector('tbody');
        const rows = Array.from(tbody.querySelectorAll('tr'));

        const columnIndex = Array.from(table.querySelectorAll('th')).findIndex(th => th.textContent.trim().toLowerCase().includes(column.toLowerCase()));

        rows.sort((a, b) => {
            const aText = a.children[columnIndex].textContent.trim();
            const bText = b.children[columnIndex].textContent.trim();

            if (column === 'date') {
                const aDate = new Date(aText.split('/').reverse().join('-'));
                const bDate = new Date(bText.split('/').reverse().join('-'));
                return order === 'asc' ? aDate - bDate : bDate - aDate;
            }

            if (!isNaN(aText) && !isNaN(bText)) {
                return order === 'asc' ? aText - bText : bText - aText;
            }

            return order === 'asc' ? aText.localeCompare(bText) : bText.localeCompare(aText);
        });

        rows.forEach(row => tbody.appendChild(row));
    }
});
