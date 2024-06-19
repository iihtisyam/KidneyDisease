document.addEventListener('DOMContentLoaded', function () {
    let ckdChart;  // Variable to store the chart instance

    const fetchData = (month, year) => {
        fetch('php/chart.php')
            .then(response => response.json())
            .then(data => {
                const filteredData = {};
                if (month && year) {
                    const key = `${year}-${month}`;
                    if (data[key]) {
                        filteredData[key] = data[key];
                    }
                } else {
                    Object.assign(filteredData, data);
                }
                
                const labels = Object.keys(filteredData);
                const positiveCounts = labels.map(month => filteredData[month].positive);
                const negativeCounts = labels.map(month => filteredData[month].negative);

                // Function to convert "YYYY-MM" to "Month YYYY"
                function formatMonthYear(dateStr) {
                    const [year, month] = dateStr.split('-');
                    const monthNames = [
                        'January', 'February', 'March', 'April', 'May', 'June', 
                        'July', 'August', 'September', 'October', 'November', 'December'
                    ];
                    return `${monthNames[parseInt(month) - 1]} ${year}`;
                }

                // Convert labels to readable format
                const formattedLabels = labels.map(label => formatMonthYear(label));

                const ctx = document.getElementById('ckdChart').getContext('2d');

                // Destroy the previous chart instance if it exists
                if (ckdChart) {
                    ckdChart.destroy();
                }

                ckdChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: formattedLabels,
                        datasets: [
                            {
                                label: 'Positive for CKD',
                                data: positiveCounts,
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Negative for CKD',
                                data: negativeCounts,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // Hypothesis or Analysis Section
                const hypothesisText = document.getElementById('hypothesisText');

                if (labels.length === 0) {
                    hypothesisText.innerHTML = `
                        <b>No data available for the selected month and year.</b>
                    `;
                    return;
                }

                // Determine if positive cases are increasing or decreasing
                let trendMessage = '';
                let increasing = 0;
                let decreasing = 0;
                let noChange = 0;

                for (let i = 1; i < positiveCounts.length; i++) {
                    if (positiveCounts[i] > positiveCounts[i - 1]) {
                        increasing++;
                    } else if (positiveCounts[i] < positiveCounts[i - 1]) {
                        decreasing++;
                    } else {
                        noChange++;
                    }
                }

                if (increasing > decreasing) {
                    trendMessage = 'an overall increasing trend in positive CKD cases';
                } else if (decreasing > increasing) {
                    trendMessage = 'an overall decreasing trend in positive CKD cases';
                } else {
                    trendMessage = 'no significant trend in positive CKD cases';
                }

                // Sample Hypothesis
                hypothesisText.innerHTML = `
                    <b>Hypothesis:</b> Based on the data from the chart, there is ${trendMessage} over the observed months.

                    This analysis suggests that the number of positive CKD cases is ${increasing > decreasing ? 'rising' : 'falling'} overall, 

                    with ${increasing} months showing an increase and ${decreasing} months showing a decrease.
                    
                    Further investigation is required to determine the exact causes, which could include seasonal factors, lifestyle changes, or increased awareness and testing.
                `;
            })
            .catch(error => console.error('Error fetching data:', error));
    };

    fetchData();

    document.getElementById('searchButton').addEventListener('click', () => {
        const month = document.getElementById('monthSelect').value;
        const year = document.getElementById('yearSelect').value;
        fetchData(month, year);
    });
});
