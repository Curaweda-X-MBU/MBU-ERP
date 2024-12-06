<script src="{{asset('app-assets/vendors/js/charts/chart.min.js')}}"></script>
<script src="{{asset('app-assets/vendors/js/charts/chart-plugin.min.js')}}"></script>
<script>
    $(function () {
        const $allTd = $('#tbl-signature td');
        const $cells = $('#tbl-signature tr:first').find('td');
        const numberOfCells = $cells.length;
        const widthPercentage = (100 / numberOfCells) + '%';
        $allTd.css('width', widthPercentage);

        const dataMortality = @json($data->ph_mortality);
        let arrMati = [];
        let arrAfkir = [];
        for (let i = 1; i <= 7; i++) {
            const currentData = dataMortality.find(entry => entry.day === i);
            arrMati.push(currentData ? currentData.death : 0);
            arrAfkir.push(currentData ? currentData.culling : 0);
        }

        const datasets = [
            {
                label: 'Mati',
                data: arrMati,
                borderWidth: 2,
                borderColor: 'rgb(118, 168, 216)',
                backgroundColor: 'rgba(118, 168, 216, 0.5)',

            },
            {
                label: 'Afkir',
                data: arrAfkir,
                borderWidth: 2,
                borderColor: 'rgb(255, 113, 69)',
                backgroundColor: 'rgba(255, 113, 69, 0.5)',

            }
        ];

        const allData = datasets.flatMap(dataset => dataset.data);
        const maxDataValue = Math.max(...allData);
        const minDataValue = Math.min(...allData);
        const range = maxDataValue - minDataValue;
        const yMax = Math.ceil(maxDataValue * 1.2);
        let stepSize;
        if (range > 0) {
            stepSize = Math.ceil(range / 5);
        } else {
            stepSize = 1;
        }

        const data = {
            labels: ['1', '2', '3', '4', '5', '6', '7'],
            datasets: datasets
        };

        const config = {
            type: 'bar',
            data: data,
            plugins: [ChartDataLabels],
            options: {
                animation: false,
                responsive: false, // Make sure to set this to false
                maintainAspectRatio: false,
                tooltips: {
                    callbacks: {
                        title: function(tooltipItems) {
                            return 'Hari ke-' + tooltipItems[0].xLabel;
                        }
                    }
                },
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true,
                            stepSize: stepSize,
                            max: yMax
                        }
                    }]
                },
                plugins: {
                    datalabels: {
                        anchor: 'end',
                        align: 'end',
                        formatter: (value) => {
                            return value;
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