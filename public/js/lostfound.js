document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("lostFoundChart").getContext("2d");

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: labelsData,
            datasets: [{
                label: "Number of Items Lost",
                data: countsData,
                backgroundColor: "rgba(75, 192, 192, 0.6)",
                borderColor: "rgba(75, 192, 192, 1)",
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: "Lost Count"
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: "Item Type"
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
