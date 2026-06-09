$(function () {
    "use strict";

    /*LIne-Chart */
    var ctx = document.getElementById("chartLine").getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"],
            datasets: [{
                label: 'Profits',
                data: [360, 330, 220, 430, 200, 370, 150],
                borderWidth: 2,
                backgroundColor: 'transparent',
                borderColor: '#0d9c1e',
                borderWidth: 3,
                lineTension: 0.3,
                pointBackgroundColor: '#ffffff',
                pointRadius: 2
            }, {
                label: 'Expenses',
                data: [400, 250, 300, 350, 400, 250, 300],
                borderWidth: 2,
                backgroundColor: 'transparent',
                borderColor: '#007F6E',
                borderWidth: 3,
                lineTension: 0.3,
                pointBackgroundColor: '#ffffff',
                pointRadius: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    ticks: {
                        color: "#9ba6b5",
                    },
                    display: true,
                    grid: {
                        color: 'rgba(13 ,156, 30, 0.2)'
                    }
                },
                y: {
                    ticks: {
                        color: "#9ba6b5",
                    },
                    display: true,
                    grid: {
                        color: 'rgba(13 ,156, 30, 0.2)'
                    },
                    scaleLabel: {
                        display: false,
                        labelString: 'Thousands',
                        fontColor: 'rgba(13 ,156, 30, 0.2)'
                    }
                }
            },
            legend: {
                labels: {
                    fontColor: "#9ba6b5"
                },
            },
        }
    });


    /* Bar-Chart1 */
    var ctx = document.getElementById("chartBar1").getContext('2d');
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep"],
        datasets: [{
            label: 'Sales',
            data: [300, 400, 500, 600, 700, 800, 900, 1000, 1100], // Updated sales data
            borderWidth: 2,
            backgroundColor: '#0d9c1e',
            borderColor: '#0d9c1e',
            borderWidth: 2.0,
            pointBackgroundColor: '#ffffff',
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            display: true
        },
        scales: {
            y: {
                ticks: {
                    beginAtZero: true,
                    stepSize: 150,
                    color: "#9ba6b5",
                },
                grid: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            x: {
                barPercentage: 0.4,
                barValueSpacing: 0,
                barDatasetSpacing: 0,
                barRadius: 0,
                ticks: {
                    display: true,
                    color: "#9ba6b5",
                },
                grid: {
                    display: false,
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            }
        },
        legend: {
            labels: {
                fontColor: "#9ba6b5"
            },
        },
    }
});


    /* Bar-Chart2*/
    var ctx = document.getElementById("chartBar2");
var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
        datasets: [{
            label: "Data1",
            data: [50, 70, 60, 55, 75, 65, 80], // Updated data for Data1
            borderColor: "#0d9c1e",
            borderWidth: "0",
            backgroundColor: "#0d9c1e"
        }, {
            label: "Data2",
            data: [35, 45, 50, 40, 60, 70, 55], // Updated data for Data2
            borderColor: "#007F6E",
            borderWidth: "0",
            backgroundColor: "#007F6E"
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                barPercentage: 0.4,
                barValueSpacing: 0,
                barDatasetSpacing: 0,
                barRadius: 0,
                ticks: {
                    color: "#9ba6b5",
                },
                grid: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            y: {
                ticks: {
                    beginAtZero: true,
                    color: "#9ba6b5",
                },
                grid: {
                    color: 'rgba(119, 119, 142, 0.2)'
                },
            }
        },
        legend: {
            labels: {
                color: "#9ba6b5"
            },
        },
    }
});


    /* Area Chart*/
    var ctx = document.getElementById("chartArea");
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
        datasets: [{
            label: "Data1",
            borderColor: "#0d9c1e",
            borderWidth: "3",
            lineTension: 0.3,
            backgroundColor: "rgba(108, 95, 252, .1)",
            fill: true,
            data: [30, 50, 40, 60, 45, 70, 55] // Updated data for Data1
        }, {
            label: "Data2",
            borderColor: "rgba(5, 195, 251 ,0.9)",
            borderWidth: "3",
            lineTension: 0.3,
            backgroundColor: "rgba(5, 195, 251, 0.7)",
            pointHighlightStroke: "rgba(5, 195, 251 ,1)",
            fill: true,
            data: [20, 40, 30, 50, 35, 60, 45] // Updated data for Data2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        tooltips: {
            mode: 'index',
            intersect: false
        },
        hover: {
            mode: 'nearest',
            intersect: true
        },
        scales: {
            x: {
                ticks: {
                    color: "#9ba6b5",
                },
                grid: {
                    color: 'rgba(119, 119, 142, 0.2)'
                }
            },
            y: {
                ticks: {
                    beginAtZero: true,
                    color: "#9ba6b5",
                },
                grid: {
                    color: 'rgba(119, 119, 142, 0.2)'
                },
            }
        },
        legend: {
            labels: {
                color: "#9ba6b5"
            },
        },
    }
});


    /* Pie Chart*/
    var datapie = {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
        datasets: [{
            data: [15, 25, 20, 10, 30], 
            backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff']
        }]
    };
    
    var optionpie = {
        maintainAspectRatio: false,
        responsive: true,
        legend: {
            display: false,
        },
        animation: {
            animateScale: true,
            animateRotate: true
        }
    };

    /* Doughbut Chart*/
    var ctx6 = document.getElementById('chartPie');
    var myPieChart6 = new Chart(ctx6, {
        type: 'doughnut',
        data: datapie,
        options: optionpie
    });

    /* Pie Chart*/
    var ctx7 = document.getElementById('chartDonut');
    var myPieChart7 = new Chart(ctx7, {
        type: 'pie',
        data: datapie,
        options: optionpie
    });

    /* Radar chart*/
    var ctx = document.getElementById("chartRadar");
var myChart = new Chart(ctx, {
    type: 'radar',
    data: {
        labels: [
            ["Eating", "Dinner"],
            ["Drinking", "Water"], "Sleeping", ["Designing", "Graphics"], "Coding", "Cycling", "Running"
        ],
        datasets: [{
            label: "Data1",
            data: [70, 45, 60, 55, 70, 65, 50], // Updated data values for Data1
            borderColor: "rgba(255, 99, 132, 0.8)", // New border color for Data1
            borderWidth: "1",
            backgroundColor: "rgba(255, 99, 132, 0.4)" // New background color for Data1
        }, {
            label: "Data2",
            data: [40, 30, 50, 35, 60, 40, 45], // Updated data values for Data2
            borderColor: "rgba(54, 162, 235, 0.8)", // New border color for Data2
            borderWidth: "1",
            backgroundColor: "rgba(54, 162, 235, 0.4)" // New background color for Data2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            display: false
        },
        scale: {
            angleLines: { color: '#9ba6b5' },
            grid: {
                color: 'rgba(119, 119, 142, 0.2)'
            },
            ticks: {
                beginAtZero: true,
                color: 'rgba(119, 119, 142, 0.2)'
            },
            pointLabels: {
                color: '#9ba6b5',
            },
        }
    }
});


    /* polar chart */
    var ctx = document.getElementById("chartPolar");
var myChart = new Chart(ctx, {
    type: 'polarArea',
    data: {
        datasets: [{
            data: [20, 25, 15, 10, 30], // Updated data values
            backgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff'], // New background colors
            hoverBackgroundColor: ['#ff6384', '#36a2eb', '#ffce56', '#4bc0c0', '#9966ff'], // New hover background colors
            borderColor: 'transparent',
        }],
        labels: ["Data1", "Data2", "Data3", "Data4", "Data5"] // Updated labels
    },
    options: {
        scale: {
            grid: {
                color: 'rgba(119, 119, 142, 0.2)'
            }
        },
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            labels: {
                color: "#9ba6b5"
            },
        },
    }
});


});