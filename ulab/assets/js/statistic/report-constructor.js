$(function () {
    const $body = $('body')
    const $chartBarBlock = $("#chart-bar-block")

    let chartBar
    let monthChartBar

    let globalId = 0
    let globalEntity = ''
    let userIds = ['1', '8', '9', '33', '61']

    $('.select2').select2({
        theme: 'bootstrap-5',
    })

    const userId = $('#user-id').val()

    const $journal = $('#journal_all')
    const $trHeaderTitle = $journal.find('.header-title')
    const $trHeaderSearch = $journal.find('.header-search')

    $('#select_entities').change(function () {
        const entity = $(this).val()
        const $selectColumnBlock = $('#select_columns_block')

        $selectColumnBlock.empty()
        $('.chart-donut').empty()
        destroyChartBar()

        if(monthChartBar) {
            removeMonthBar()
        }

        $.ajax({
            method: 'POST',
            url: "/ulab/statistic/getStatisticColumnAjax/",
            data: {
                entity: entity,
            },
            dataType: "json",
            success: function (data) {
                $.each(data, function (i, item) {
                    $selectColumnBlock.append(`
                        <div class="col-4">
                            <div class="form-check">
                                <input class="form-check-input select_columns" type="checkbox" value="${i}" id="flexCheckDefault${i}" checked>
                                <label class="form-check-label" for="flexCheckDefault${i}">
                                    ${item.title}
                                </label>
                            </div>
                        </div>
                    `)
                })

                if ($selectColumnBlock.find('.form-check-input').length > 0 ) {
                    $('#generate_journal').prop('disabled', false);
                } else {
                    $('#generate_journal').prop('disabled', true);
                }
            }
        })
    })


    $('#generate_journal').click(function () {
        const entity = $('#select_entities').val()
        const column = $('.select_columns:checked').map(function () {
            return $(this).val()
        }).get()

        let columns = [],
            chartData = []

        if ( $.fn.DataTable.isDataTable('#journal_all') ) {
            $journal.DataTable().clear().destroy();
        }

        $journal.find('.header-title').empty()
        $journal.find('.header-search').empty()

        let defaultOrder = [ 0, 'asc' ]

        $.ajax({
            method: 'POST',
            url: "/ulab/statistic/getStatisticColumnAjax/",
            data: {
                entity: entity,
                column: column,
            },
            dataType: "json",
            success: function (data) {
                let index = 0
                $.each(data, function (key, val) {
                    $trHeaderTitle.append(`<th scope="col">${val.title}</th>`)

                    if ( val.filter === false ) {
                        $trHeaderSearch.append(
                            `<th scope="col"></th>`
                        )
                    } else {
                        $trHeaderSearch.append(
                            `<th scope="col">
                                <input type="text" class="form-control search">
                            </th>`
                        )
                    }

                    if ( val.default_order !== undefined ) {
                        defaultOrder = [ index, val.default_order ]
                    }

                    columns.push(
                        {
                            data: key,
                            orderable: val.order !== false,
                            render: function (data, type, item) {
                                if ( val.link !== undefined ) {
                                    let link = val.link
                                    let tmp = Array.from(val.link.matchAll(/.*?{([\w\d]+)}/g))
                                    tmp.map(function (m) {
                                        link = link.replace(`{${m[1]}}`, item[m[1]])
                                    })

                                    return link
                                } else {
                                    return item[key]
                                }
                            }
                        }
                    )

                    index++
                })

                /*journal requests*/
                let journalDataTable = $journal.DataTable({
                    destroy : true,
                    retrieve: true,
                    bAutoWidth: false,
                    autoWidth: false,
                    fixedColumns: false,
                    processing: true,
                    serverSide: true,
                    bSortCellsTop: true,
                    scrollX: true,
                    fixedHeader: false,
                    colReorder: true,
                    ajax: {
                        type : 'POST',
                        data: function ( d ) {
                            d.dateStart = $('#inputDateStart').val()
                            d.dateEnd = $('#inputDateEnd').val()
                            d.entity = entity
                            d.column = column
                        },
                        url : '/ulab/statistic/getStatisticConstructorJournal/',
                        dataSrc: function (json) {
                            $('.chart-donut').empty()
                            chartData = []

                            if (Object.keys(json['chart']).length &&
                                Object.keys(json['chart']['donut']).length) {
                                let donuts = json['chart']['donut']

                                $.each(donuts, function (k, donut) {
                                    $.each(json.data, function (key, val) {
                                        if (donut['formatted'] === undefined ||
                                            val[donut['value']] == 0 ||
                                            val[donut['value']]== null) {
                                            return
                                        }

                                        let formatted = donut['formatted'].replace('{value}', val[donut['value']])

                                        if (!chartData[k]) {
                                            chartData[k] = []
                                        }

                                        chartData[k].push(
                                            {
                                                value: val[donut['value']],
                                                label: val[donut['label']],
                                                formatted: formatted
                                            }
                                        )
                                    })
                                })

                                $.each(chartData, function (key, val) {
                                    Morris.Donut({
                                        element: `chart-donut-${key+1}`,
                                        data: val,
                                        backgroundColor: false,
                                        colors: [
                                            '#4acacb', '#fe8676', '#6a8bc0', '#808080',
                                            '#ff8c00', '#ffd700', '#ba55d3', '#008000',
                                            '#ff69b4', '#4682b4', '#ff7f50', '#bdb76b',
                                            '#000080', '#800080', '#bc8f8f', '#d2691e',
                                            '#ff0000', '#adff2f', '#5ab6df', '#9400d3',
                                            '#2f4f4f', '#00ffff', '#708090', '#ffff00',
                                            '#ff0000',
                                        ],
                                        formatter: function (x, data) { return data.formatted }
                                    }).select(0)
                                })
                            }

                            return json.data
                        },
                    },
                    columns: columns,
                    language: dataTablesSettings.language,
                    lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
                    pageLength: 25,
                    order: [defaultOrder],
                    dom: 'frtB<"bottom"lip>',
                    buttons: dataTablesSettings.buttons,
                });

                journalDataTable.columns().every( function () {
                    $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
                        journalDataTable
                            .column( $(this).parent().index() )
                            .search( this.value )
                            .draw();
                    })
                })

                $('.filter').on('change', function () {
                    journalDataTable.ajax.reload()
                    journalDataTable.draw()

                    destroyChartBar()
                })
            }
        })
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

    function removeMonthBar() {
        monthChartBar.destroy();
        $('#chart-bar-block-month').addClass('d-none');
    }

    /**
     * Отобразить график bar
     */
    $body.on('click', '.chart_link', function (e) {
        e.preventDefault()

        const ctx = $('#chart-bar')

        let entity = $(this).data('entity'),
            id = $(this).data('id')

        globalId = id
        globalEntity = entity

        destroyChartBar()

        if(monthChartBar) {
            removeMonthBar()
        }

        $.ajax({
            method: 'POST',
            url: '/ulab/statistic/getStatisticEntityAjax',
            data: {
                entity: entity,
                id: id,
            },
            dataType: 'json',
            success: function (data) {
                console.log('data', data);
                $chartBarBlock.removeClass('d-none')

                $([document.documentElement, document.body]).animate({
                    scrollTop: $chartBarBlock.offset().top - 20
                }, 500)

                if (Object.keys(data).length) {
                    const colors = ['54, 162, 235', '255, 99, 132']
                    let formattedY = data['formatted'].length > 1 ? '' : data['formatted']

                    let months = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', ]

                    let datasets = [];

                    if(data.double && userIds.includes(userId)) {
                        datasets = [{
                            label: data['formatted'][0],
                            data: data['value'][0],
                            borderColor: `rgb(${colors[0]})`,
                            backgroundColor: `rgba(${colors[0]}, 0.2)`,
                            borderWidth: 1,
                            yAxisID: 'y',
                            months
                        },
                        {
                            label: data['formatted'][1],
                            data: data['value'][1],
                            borderColor: `rgb(${colors[1]})`,
                            backgroundColor: `rgba(${colors[1]}, 0.2)`,
                            borderWidth: 1,
                            yAxisID: 'y1',
                            months
                        }];
                    }else {
                        datasets = [{
                            label: data['formatted'][0],
                            data: data['value'][0],
                            borderColor: `rgb(${colors[0]})`,
                            backgroundColor: `rgba(${colors[0]}, 0.2)`,
                            borderWidth: 1,
                            yAxisID: 'y',
                            months
                        }];
                    }

                    chartBar = new Chart(ctx, {
                        type: "bar",
                        data: {
                            labels: ['Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'],
                            datasets: datasets
                        },
                        options: {
                            plugins: {
                                legend: {
                                    display: true,
                                },
                                title: {
                                    display: true,
                                    text: data['label'],
                                    font: {
                                        size: 25
                                    },
                                },
                            },
                            scales: {
                                y: {
                                    title: {
                                        display: true,
                                        text: [formattedY],
                                        font: {
                                            size: 18
                                        },
                                    },
                                    type: 'linear',
                                    display: true,
                                    position: 'left'
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: ['За текущий год'],
                                        font: {
                                            size: 18
                                        }
                                    },
                                }
                            },
                        },
                    });

                    // Функция для добавления или удаления горизонтальной линии в зависимости от значения ID
                    function updateHorizontalLine(toCheck) {
                        let annotationPlugin = chartBar.options.plugins.annotation

                        // Удаление всех текущих аннотаций
                        annotationPlugin.annotations = []

                        // Проверка условия по ID и добавление аннотации, если условие выполняется
                        if (data.label === toCheck) {
                            //let maxY = Math.max.apply(Math,data['value'][0])

                            annotationPlugin.annotations.push({
                                type: 'line',
                                mode: 'horizontal',
                                scaleID: 'y',
                                value: 210, // Значение y, на котором будет нарисована линия
                                borderColor: 'red', // Цвет линии
                                borderWidth: 2, // Ширина линии
                                label: {
                                    content: 'Предельная линия',
                                    enabled: true,
                                    position: 'right',
                                },
                                drawTime: 'beforeDatasetsDraw',
                            });
                        }

                        // Обновление графика
                        chartBar.update()
                    }
                    // updateHorizontalLine("Спектрофотометр  № 17.1-25")
                }
            },
            error: function (jqXHR, exception) {
                let msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status === 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status === 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                console.error(msg)
            }
        })
    })

    /**
     * Очистить график bar
     */
    function destroyChartBar() {
        if (chartBar) {
            $chartBarBlock.addClass('d-none')
            chartBar.destroy();
        }
    }

    const ruDateConf = { year: 'numeric', month: 'numeric', day: 'numeric' };

    $('#chart-bar').on('click', (e) => {
        let activePoint = chartBar.getElementsAtEventForMode(e, 'nearest', { intersect: true }, true);

        if(activePoint.length > 0) {
            let clickedDatasetIndex = activePoint[0].datasetIndex;
            let clickedElementIndex = activePoint[0].index;

            let month = chartBar.data.datasets[clickedDatasetIndex].months[clickedElementIndex];

            const ctxMonth = $('#chart-bar-month')

            if(monthChartBar) {
                removeMonthBar()
            }

            $('#chart-bar-block-month').removeClass('d-none');

            $.ajax({
                method: 'POST',
                url: '/ulab/statistic/getStatisticEntityForMonthAjax',
                data: {
                    id: globalId,
                    month: month,
                    entity: globalEntity
                },
                dataType: 'json',
                success: function (data)
                {
                    const colors = ['54, 162, 235', '255, 99, 132']
                    let formattedY = data['formatted'].length > 1 ? '' : data['formatted']
                    let datasets = [];

                    if(data.double && userIds.includes(userId)) {
                        datasets = [{
                            label: data['formatted'][0],
                            data: data['value'][0],
                            borderColor: `rgb(${colors[0]})`,
                            backgroundColor: `rgba(${colors[0]}, 0.2)`,
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: data['formatted'][1],
                            data: data['value'][1],
                            borderColor: `rgb(${colors[1]})`,
                            backgroundColor: `rgba(${colors[1]}, 0.2)`,
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }];
                    }else {
                        datasets = [{
                            label: data['formatted'][0],
                            data: data['value'][0],
                            borderColor: `rgb(${colors[0]})`,
                            backgroundColor: `rgba(${colors[0]}, 0.2)`,
                            borderWidth: 1,
                            yAxisID: 'y'
                        }];
                    }

                    let dates = getDatesInRange(new Date(data.date_start), new Date(data.date_end));

                    monthChartBar = new Chart(ctxMonth, {
                        type: "bar",
                        data: {
                            labels: dates,
                            datasets: datasets
                        },
                        options: {
                            plugins: {
                                legend: {
                                    display: true,
                                },
                                title: {
                                    display: true,
                                    text: data['label'],
                                    font: {
                                        size: 25
                                    },
                                },
                            },
                            scales: {
                                y: {
                                    title: {
                                        display: true,
                                        text: [formattedY],
                                        font: {
                                            size: 18
                                        },
                                    },
                                    type: 'linear',
                                    display: true,
                                    position: 'left'
                                },
                                y1: {
                                    type: 'linear',
                                    display: true,
                                    position: 'right',
                                    grid: {
                                        drawOnChartArea: false,
                                    },
                                },
                                x: {
                                    title: {
                                        display: true,
                                        text: [`За ${data.month}`],
                                        font: {
                                            size: 18
                                        }
                                    },
                                }
                            },
                        },
                    });
                },
                error: function (jqXHR, exception) {
                    let msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status === 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status === 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    console.error(msg)
                }
            })
        }

    })

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
})
