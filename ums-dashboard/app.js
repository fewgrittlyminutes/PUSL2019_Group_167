document.addEventListener('DOMContentLoaded', function () {

    const ctxRevenue = document.getElementById('revenueChart').getContext('2d');
    
    new Chart(ctxRevenue, {
        type: 'line', 
        data: {
        
            labels: ['Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov'], 
            datasets: [{
                label: 'Total Revenue (Rs)',
                data: [95000, 102000, 98000, 115000, 110000, 125430], 
                borderColor: '#0d6efd', 
                backgroundColor: 'rgba(13, 110, 253, 0.1)', 
                tension: 0.4, 
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top' }
            }
        }
    });


    const ctxUsage = document.getElementById('usageChart').getContext('2d');

    new Chart(ctxUsage, {
        type: 'doughnut',
        data: {
            labels: ['Electricity', 'Water', 'Gas'],
            datasets: [{
                data: [55, 30, 15], 
                backgroundColor: [
                    '#ffc107', 
                    '#0d6efd', 
                    '#dc3545'  
                ],
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });

    const filterUtility = document.getElementById('filterUtility');
    const filterStatus = document.getElementById('filterStatus');
    const table = document.getElementById('reportTable');
    const tableRows = table.querySelectorAll('tbody tr'); 
    const summaryText = document.getElementById('summaryText');

    function filterTable() {
        const selectedUtility = filterUtility.value; 
        const selectedStatus = filterStatus.value;   
        
        let visibleCount = 0;

        tableRows.forEach(row => {

            const rowUtility = row.getAttribute('data-utility');
            const rowStatus = row.getAttribute('data-status');

            const isUtilityMatch = (selectedUtility === 'all' || rowUtility === selectedUtility);
            const isStatusMatch = (selectedStatus === 'all' || rowStatus === selectedStatus);

            if (isUtilityMatch && isStatusMatch) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none'; 
            }
        });

        summaryText.textContent = `Showing ${visibleCount} record(s)`;
    }

    filterUtility.addEventListener('change', filterTable);
    filterStatus.addEventListener('change', filterTable);


    const btnExport = document.getElementById('btnExport');

    btnExport.addEventListener('click', function () {
        let csvContent = "data:text/csv;charset=utf-8,";

        let headers = [];
        const headerCells = table.querySelectorAll('thead th');
        headerCells.forEach(th => headers.push(th.innerText));
        csvContent += headers.join(",") + "\r\n"; 

        tableRows.forEach(row => {
            if (row.style.display !== 'none') {
                let rowData = [];
                const cells = row.querySelectorAll('td');
                
                cells.forEach(cell => {

                    let cleanText = cell.innerText.replace(/(\r\n|\n|\r)/gm, "").trim();
                    rowData.push(cleanText);
                });
                
                csvContent += rowData.join(",") + "\r\n";
            }
        });

        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "UMS_Report_Export.csv");
        document.body.appendChild(link);
        link.click(); 
        document.body.removeChild(link);
    });

});