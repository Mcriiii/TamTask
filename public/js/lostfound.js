
document.addEventListener("DOMContentLoaded", function () {
    // Register the datalabels plugin globally
    Chart.register(ChartDataLabels);

    // LOST ITEMS BAR CHART
    const lostCtx = document.getElementById("lostFoundChart").getContext("2d");
    const lostGradient = lostCtx.createLinearGradient(0, 0, 0, 300);
    lostGradient.addColorStop(0, "rgba(56, 173, 169, 1)");
    lostGradient.addColorStop(1, "rgba(56, 173, 169, 0.4)");

    new Chart(lostCtx, {
        type: "bar",
        data: {
            labels: labelsData,
            datasets: [{
                label: "Lost Items",
                data: countsData,
                backgroundColor: lostGradient,
                borderColor: "rgba(56, 173, 169, 1)",
                borderWidth: 2
            }]
        },
        options: {
            plugins: {
                datalabels: {
                    color: "#2e2e2e",
                    anchor: "end",
                    align: "start",
                    font: {
                        weight: "bold",
                        size: 18
                    },
                    formatter: value => value
                }
            },
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        callback: v => Number.isInteger(v) ? v : null
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    // VIOLATION CHART (Horizontal bar)
    const vCtx = document.getElementById("violationChart").getContext("2d");
    const vGradient = vCtx.createLinearGradient(0, 0, 600, 0);
    vGradient.addColorStop(0, "rgba(255, 99, 132, 1)");
    vGradient.addColorStop(1, "rgba(255, 99, 132, 0.3)");

    new Chart(vCtx, {
        type: "bar",
        data: {
            labels: vLabelsData,
            datasets: [{
                label: "Violations",
                data: vCountsData,
                backgroundColor: vGradient,
                borderColor: "rgba(255, 99, 132, 1)",
                borderWidth: 2
            }]
        },
        options: {
            indexAxis: 'y',
            plugins: {
                datalabels: {
                    color: "#2e2e2e",
                    anchor: "end",
                    align: "right",
                    font: {
                        weight: "bold",
                        size: 18
                    },
                    formatter: value => value
                }
            },
            responsive: true,
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        callback: v => Number.isInteger(v) ? v : null
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    // INCIDENT CHART
    const iCtx = document.getElementById("incidentChart").getContext("2d");
    const iGradient = iCtx.createLinearGradient(0, 0, 0, 300);
    iGradient.addColorStop(0, "rgba(255, 206, 86, 1)");
    iGradient.addColorStop(1, "rgba(255, 206, 86, 0.4)");

    new Chart(iCtx, {
        type: "bar",
        data: {
            labels: iLabelsData,
            datasets: [{
                label: "Incidents",
                data: iCountsData,
                backgroundColor: iGradient,
                borderColor: "rgba(255, 206, 86, 1)",
                borderWidth: 2
            }]
        },
        options: {
            plugins: {
                datalabels: {
                    color: "#2e2e2e",
                    anchor: "end",
                    align: "start",
                    font: {
                        weight: "bold",
                        size: 18
                    },
                    formatter: value => value
                }
            },
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1,
                        precision: 0,
                        callback: v => Number.isInteger(v) ? v : null
                    }
                }
            }
        },
        plugins: [ChartDataLabels]
    });
});

