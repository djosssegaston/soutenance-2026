/**
 * Dashboard Charts - Institution Cockpit
 */

document.addEventListener('DOMContentLoaded', function() {
    fetchChartData();
});

async function fetchChartData() {
    try {
        const response = await fetch('/api/v1/institution/dashboard/charts');
        if (response.ok) {
            const result = await response.json();
            renderCharts(result.data);
        }
    } catch (error) {
        console.error('Error fetching chart data:', error);
    }
}

function renderCharts(data) {
    // 1. Funding Evolution (Line Chart)
    initFundingEvolutionChart(data.funding_evolution);

    // 2. Sector Distribution (Donut Chart)
    initSectorDistributionChart(data.sector_distribution);

    // 3. Repayment Status (Bar Chart)
    initRepaymentStatusChart(data.repayment_status);

    // 4. Risk Exposure (Stacked Bar Chart)
    initRiskExposureChart(data.risk_portion);
}

function initFundingEvolutionChart(data) {
    const months = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'];
    const seriesData = new Array(12).fill(0);
    
    data.forEach(item => {
        seriesData[item.month - 1] = parseFloat(item.total);
    });

    const options = {
        series: [{
            name: 'Financements (FCFA)',
            data: seriesData
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        colors: ['#0d6efd'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        xaxis: { categories: months },
        yaxis: {
            labels: {
                formatter: function(val) { return (val / 1000).toFixed(0) + 'K'; }
            }
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.7,
                opacityTo: 0.3,
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#chart-funding-evolution"), options);
    chart.render();
}

function initSectorDistributionChart(data) {
    if (!data || !data.length) {
        document.querySelector("#chart-sector-distribution").innerHTML = '<div class="text-center text-muted py-5"><i class="fe fe-pie-chart fs-30 mb-2"></i><p class="mb-0">Aucune donnée sectorielle</p></div>';
        return;
    }

    const labels = data.map(item => item.secteur || 'Autre');
    const series = data.map(item => item.count);

    const options = {
        series: series,
        labels: labels,
        chart: {
            height: 350,
            type: 'donut',
        },
        legend: { position: 'bottom' },
        colors: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6c757d'],
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { width: 200 },
                legend: { position: 'bottom' }
            }
        }]
    };

    const chart = new ApexCharts(document.querySelector("#chart-sector-distribution"), options);
    chart.render();
}

function initRepaymentStatusChart(data) {
    const categories = data.map(item => item.statut.toUpperCase());
    const series = data.map(item => item.count);

    const options = {
        series: [{
            name: 'Nombre de dossiers',
            data: series
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: { show: false }
        },
        colors: ['#198754'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                horizontal: true,
            }
        },
        dataLabels: { enabled: false },
        xaxis: { categories: categories }
    };

    const chart = new ApexCharts(document.querySelector("#chart-repayment-status"), options);
    chart.render();
}

function initRiskExposureChart(data) {
    const categories = ['Critique', 'Élevé', 'Moyen', 'Faible'];
    const seriesData = [0, 0, 0, 0];
    
    data.forEach(item => {
        if (item.niveau_risque === 'critique') seriesData[0] = item.count;
        if (item.niveau_risque === 'eleve') seriesData[1] = item.count;
        if (item.niveau_risque === 'moyen') seriesData[2] = item.count;
        if (item.niveau_risque === 'faible') seriesData[3] = item.count;
    });

    const options = {
        series: [{
            name: 'Nombre de projets',
            data: seriesData
        }],
        chart: {
            height: 350,
            type: 'bar',
            toolbar: { show: false }
        },
        colors: ['#dc3545', '#fd7e14', '#ffc107', '#198754'],
        distributed: true,
        plotOptions: {
            bar: {
                columnWidth: '45%',
                distributed: true,
            }
        },
        xaxis: {
            categories: categories,
            labels: { style: { colors: ['#dc3545', '#fd7e14', '#ffc107', '#198754'], fontWeight: 'bold' } }
        }
    };

    const chart = new ApexCharts(document.querySelector("#chart-risk-exposure"), options);
    chart.render();
}
