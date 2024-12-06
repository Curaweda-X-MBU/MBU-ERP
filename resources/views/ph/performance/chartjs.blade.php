<script src="{{asset('app-assets/vendors/js/charts/chart.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/charts/chart-plugin.min.js')}}"></script>
<script>
    $(function () {
        const dataSummary = @json($arrSummary);
        const datasets = [
            {
                label: 'Depletion',
                data: dataSummary.map(item => item.percentage_depletion),
                borderWidth: 2,
                borderColor: 'rgb(118, 168, 216)',
                backgroundColor: 'rgba(118, 168, 216, 0.5)',

            }
        ];

        const allData = datasets.flatMap(dataset => dataset.data);
        const maxDataValue = Math.max(...allData);
        const minDataValue = Math.min(...allData);
        const range = maxDataValue - minDataValue;
        const yMax = Math.ceil(maxDataValue * 1);
        let stepSize;
        if (range > 0) {
            stepSize = Math.round(range / 5);
        } else {
            stepSize = 1;
        }
        
        const data = {
            labels: dataSummary.map(item => `${item.supplier_name}#${item.hatchery}`),
            datasets: datasets
        };

        const config = {
            type: 'bar',
            data: data,
            plugins: [ChartDataLabels],
            options: {
                animation: false,
                responsive: false,
                maintainAspectRatio: false,
                tooltips: {
                    callbacks: {
                        title: function(tooltipItems, data) {
                            const strLabel = tooltipItems[0].yLabel.split('#');
                            const labelVendor = strLabel[0];
                            const labelHatchery = strLabel[1];
                            return [ `${labelVendor}`, `${labelHatchery}` ];
                        }
                    }
                },
                scales: {
                    xAxes: [{
                        ticks: {
                            autoSkip: false,
                            callback: function(value) {
                                const arrValue = value.split("#");
                                const strVendor = arrValue[0];
                                const strHatchery = arrValue[1];
                                let arrResult = [strHatchery]
                                const maxChars = 15;
                                if (strVendor.length > maxChars) {
                                    const firstLine = strVendor.substring(0, maxChars);
                                    const secondLine = strVendor.substring(maxChars);
                                    arrResult.unshift(firstLine, secondLine);
                                } else {
                                    arrResult.unshift(strVendor);
                                }
                                return arrResult;
                            }
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Asal DOC'
                        }
                    }],
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: stepSize,
                            max: yMax,
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Depletion (%)'
                        }
                    }],
                },
                legend: {
                    display: false
                },
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        formatter: (value) => {
                            return `${value}%`;
                        },
                        color: 'black'
                    }
                }
            }
        };

        const ctx = document.getElementById('myChart');
        new Chart(ctx, config);
    });
</script>