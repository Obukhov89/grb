let startInpVal = $('#date_start_chart').val();
let endInpVal = $('#date_end_chart').val();
let endOzVal = $('#date_end').val();
let startOzVal = $('#date_start').val();
let labVal = $('#lab_chart').val();

let sd = new Date(startInpVal);
let maxEndDate = new Date(endInpVal);

const ruDateConf = { year: 'numeric', month: 'numeric', day: 'numeric' };

let toDay = new Date();
let toDayString = toDay.toLocaleString('ru-RU', ruDateConf);

let mainChart = '';
let dayChart = '';

let isFiltered = false;

function sendAjax(dateStart, dateEnd, labVal) {
    $.ajax({
        method: 'POST',
        url: '/ulab/statistic/getChartStatAjax',
        data: {
            date_start: dateStart,
            date_end: dateEnd,
            lab_id: labVal
        },


        dataType: 'json',
        success: function (data) {
            if(Object.keys(data).length <= 0) return

            // console.log(data);

            let tz_titles = [];
            let dates = [];
            let labelLinksArray = [];
            let company_titles = [];

            let divHeight = Object.keys(data).length * 25 + 100;

            $('#chartForm').css('height', `${divHeight}px`);

            $.each(data, function (key, val) {
                let maxDate = new Date(val.end_date);

                let object =
                    {
                        min: new Date(val.date_max),
                        max: new Date(val.end_date),
                        color: '',
                        title: ''
                    }

                labelLinksArray.push(`/ulab/request/card/${val.id_tz}`);
                company_titles.push(val.company_title);

                let days = Math.ceil(((maxDate - toDay) / 1000) / 86400);

                if (days <= 0) {
                    object.color = 'rgba(255, 0, 0, 0.77)';
                    object.title = 'Просрочена';
                } else if (days <= 3 && days > 0) {
                    object.color = 'rgba(255, 255, 0, 0.77)';
                    object.title = 'Скоро просрочится';
                } else {
                    object.color = 'rgba(0, 255, 0, 0.77)';
                    object.title = 'В работе';
                }

                dates.push(object)

                tz_titles.push(val.title)
            })

            dateArr = getDatesInRange(sd, maxEndDate);

            dates.forEach(object => {
                let minDate = new Date(object.min);
                let maxDate = new Date(object.max);

                if (maxDate.getTime() < sd.getTime() || minDate.getDate() > maxEndDate.getTime()) {
                    object.min = sd;
                    object.max = sd;
                }

                if (minDate.getTime() < sd.getTime()) {
                    object.min = sd;
                }

                if (maxDate.getTime() > maxEndDate.getTime()) {
                    object.max = maxEndDate;
                }

                object.min = object.min.toLocaleString('ru-RU', ruDateConf);
                object.max = object.max.toLocaleString('ru-RU', ruDateConf);
            });

            const ctx = $('#myChart');
            const ctx1 = $('#myChart1');

            mainChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: tz_titles,
                    datasets: [
                        {
                            label: dates.map(row => row.title),
                            labelLinks: labelLinksArray,
                            labelCompanies: company_titles,
                            data: dates.map(row => [row.min, row.max]),
                            borderWidth: 1,
                            backgroundColor: dates.map(row => row.color),
                        }
                    ],
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'category',
                            labels: dateArr,
                        },
                        y: {
                            ticks: {
                                autoSkip: false,
                                color: 'blue'
                            },
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    if (context.raw) {
                                        let options = {
                                            year: 'numeric',
                                            month: 'numeric',
                                            day: 'numeric',
                                            timezone: 'UTC',
                                        }

                                        // console.log(mainChart.data.datasets)

                                        return `Компания - ${mainChart.data.datasets[0].labelCompanies[context.dataIndex]}, сроки - [${context.raw[0]}, ${context.raw[1]}]`;
                                    } else {
                                        return 'Сегодня';
                                    }
                                }
                            }
                        },
                    },
                }
            });

            if (toDay.getTime() <= maxEndDate.getTime() && toDay.getTime() >= sd.getTime()) {
                dayChart = new Chart(ctx1, {
                    type: 'bar',
                    data: {
                        labels: '',
                        datasets: [
                            {
                                label: 'Сегодня',
                                data: [{x: toDayString, y: 5}],
                                borderWidth: 1,
                                backgroundColor: 'rgba(255, 255, 0, 1)',
                                barThickness: 7,
                                maxBarThickness: 7
                            },
                        ],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: {
                                type: 'category',
                                labels: dateArr,
                                display: false,
                            },
                            y: {
                                display: false
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }

            mainChart.canvas.addEventListener('click', (e) => {
                clickableScales(mainChart, e);
            });
        }
    })
}

sendAjax(startInpVal, endInpVal);

$(document).ready(function () {

    $.ajax({
        method: 'POST',
        url: '/ulab/statistic/reportOzChartAjax',
        data: {
            date_start: startOzVal,
            date_end: endOzVal,
        },
        dataType: 'json',
        success: function (data) {
            console.log(data)
            let allSUM = data.allSUM;
            let wonSum = data.wonSum;

            // Метки для сегментов диаграммы
            let labels = ["Сумма всех заявок", "Сумма закрытых заявок"];

            // Значения для каждого сегмента
            let data1 = [allSUM, wonSum];

            // Получаем контекст canvas-элемента
            let ctx = $('#ChartOz')[0].getContext('2d');

            // Создаем объект диаграммы
            let myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Руб',
                        data: data1,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top'
                        }
                    }
                }
            });
        }
     });

});

function getDatesInRange(startDate, endDate) {
    const date = new Date(startDate.getTime());

    date.setDate(date.getDate() + 1);

    const dates = [];

    dates.push(startDate.toLocaleString('ru-RU', ruDateConf));

    while (date < endDate) {
        dates.push((new Date(date).toLocaleString('ru-RU', ruDateConf)));
        date.setDate(date.getDate() + 1);
    }

    dates.push(endDate.toLocaleString('ru-RU', ruDateConf));

    return dates;
}

function clickableScales(chart, click) {
    const { ctx, canvas, scales : {x, y}} = chart;

    const top = y.top;
    const left = y.left;
    const right = y.right;
    const height = y.height / y.ticks.length;

    let rect = canvas.getBoundingClientRect();
    const xCoor = click.clientX - rect.left;
    const yCoor = click.clientY - rect.top;

    for(let i = 0; i < y.ticks.length; i++) {
        if(xCoor >= left && xCoor <= right && yCoor >= top + (height * i) &&
            yCoor <= top + height + (height * i)) {

            window.open(chart.data.datasets[0].labelLinks[i], '_blank');
        }
    }
}

$('#filter').on('click', (e) => {
    let startInpVal = $('#date_start_chart').val();
    let endInpVal = $('#date_end_chart').val();
    let labVal = $('#lab_chart').val();

    if(startInpVal) {
        sd = new Date(startInpVal);
    }

    if(endInpVal) {
        maxEndDate = new Date(endInpVal);
    }

    mainChart.destroy();
    dayChart.destroy();

    isFiltered = true;

    sendAjax(startInpVal, endInpVal, labVal);
});
