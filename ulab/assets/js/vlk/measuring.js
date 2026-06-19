$(function () {
    let $body = $('body'),
        $journal = $('#journalMeasuring');

    let currentUrl = window.location.href;
    let url = new URL(currentUrl);
    let pathParts = url.pathname.split('/');
    const measuringId = pathParts[pathParts.length - 1];

    let shewhartChart;

    $body.on('click', '.new-table', function () {
        const $measuringBlock = $('#measuringBlock'),
            $measuringWrapper = $measuringBlock.find('#measuringWrapper'),
            $measuringTable = $measuringWrapper.find('#measuringTable'),
            $headMeasuring0 = $measuringTable.find('.head-measuring-0'),
            $colMeasuring0 = $measuringTable.find('.col-measuring-0');

        let measuringCount = +$measuringBlock.find('#measuringCount').val();

        if (!measuringCount) {
            $('.alert-title').text('Внимание!');
            $('.alert-content').text('Не заполнено или не верно заполнено поле "Количество результатов параллельных измерений"');
            $('#alert_modal').removeClass('col-md-8');

            $.magnificPopup.open({
                items: {
                    src: $('#alert_modal'),
                    type: 'inline',
                    fixedContentPos: false
                },
                closeOnBgClick: false
            });

            if (!$measuringWrapper.hasClass('d-none')) {
                $measuringWrapper.addClass('d-none')
            }

            return false;
        }

        $measuringWrapper.removeClass('d-none');
        $measuringTable.find('.head-measuring').remove();
        $measuringTable.find('.col-measuring').remove();

        let headMeasuring = ``,
            colMeasuring = ``;
        if (measuringCount > 1) {
            for (let i = 1; i < measuringCount; i++) {
                headMeasuring += `<th scope="col" class="head-measuring">Результат ${i + 1}-го измерения</th>`;
                colMeasuring += `<td class="col-measuring">
                                    Результат измерения
                                </td>`;
            }
        }

        $headMeasuring0.after(headMeasuring);
        $colMeasuring0.after(colMeasuring);
    });

    $body.on('input', '#measuringCount', function () {
        const $measuringBlock = $('#measuringBlock'),
            $measuringWrapper = $measuringBlock.find('#measuringWrapper');

        if (!$measuringWrapper.hasClass('d-none')) {
            $measuringWrapper.addClass('d-none')
        }
        return false;
    });

    /** Сохранить "Количество результатов параллельных определений" (Сохранить структуру таблицы) */
    $('#measuringForm').on('submit', function() {
        let $btn = $(this).find('.save-new-table');

        $btn.find('i').addClass('spinner-animation');
        $btn.addClass('disabled');
    });

    /**
     * Добавить новое измерение
     */
    $body.on('click', '.add-measuring', function () {
        const measuringModalForm = $('#measuringModalForm');

        let d = new Date();
        let strMomth = d.getMonth()+1;
        let strDate = d.getFullYear() + "-" + (strMomth < 10 ? '0'+strMomth : strMomth) + "-" + d.getDate();

        measuringModalForm.find('.del-measuring').remove();
        measuringModalForm.find('#uvmId').val('');

        measuringModalForm.find('#date').val(strDate);
        measuringModalForm.find('.result').val('');

        $.magnificPopup.open({
            items: {
                src: measuringModalForm,
                type: 'inline',
                fixedContentPos: false
            },
            closeOnBgClick: false
        });
    });

    /** Сохранить результаты измерения */
    $('#measuringModalForm').on('submit', function() {
        let $btn = $(this).find('.save-measuring');

        $btn.find('i').addClass('spinner-animation');
        $btn.addClass('disabled');
    });

    /**
     * Обновить данные результатов измерений
     */
    $body.on('click', '.update-measuring', function () {
        let uvmId = $(this).data('uvm');

        $.ajax({
            method: 'POST',
            url: '/ulab/vlk/getVlkMeasuringByIdAjax',
            data: {
                uvm_id: uvmId
            },
            dataType: "json",
            success: function (data) {
                if (data['id']) {
                    const measuringModalForm = $('#measuringModalForm');

                    let $results = measuringModalForm.find('.result');

                    measuringModalForm.find('#uvmId').val(uvmId);
                    measuringModalForm.find('#date').val(data['date']);

                    for (let key in data['result']) {
                        $($results[key]).val(data['result'][key]);
                    }

                    measuringModalForm.find('.del-measuring').remove();
                    measuringModalForm.append(`<button type="button" class="btn btn-danger ms-2 del-measuring" 
                            data-uvm="${uvmId}">Удалить <i class="fa-solid fa-arrows-rotate"></i></button>`);

                    $.magnificPopup.open({
                        items: {
                            src: measuringModalForm,
                            type: 'inline',
                            fixedContentPos: false
                        },
                        closeOnBgClick: false
                    });
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
                    msg = '1 Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                console.log(msg)
            }
        });
    });

    /**
     * Удалить данные результатов измерений
     */
    $body.on('click', '.del-measuring', function () {
        const $btn = $(this);
        let uvmId = $(this).data('uvm');

        if (!confirm('Вы действительно хотите удалить данные результатов измерений? После удаления, данные нельзя будет востановить')) {
            return false;
        }

        $btn.find('i').addClass('spinner-animation');
        $btn.addClass('disabled');

        $.ajax({
            method: 'POST',
            url: '/ulab/vlk/delVlkMeasuringAjax',
            data: {
                uvm_id: uvmId
            },
            dataType: "json",
            success: function (data) {
                if (data['success']) {
                    location.reload();
                } else {
                    showErrorMessage(data['error']);
                    window.scrollTo(0, 0);

                    return false;
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
                    msg = '1 Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                console.log(msg)
            }
        });
    });

    // Функция для получения колонок
    function getColumns() {
        let measuringCount = +$('#measuringCount').val();

        let columns = [
            {
                data: 'history',
                orderable: false,
                render: function (data, type, item) {
                    return `<a href="#" data-id="${item.id}" class="measuring-history"><i class="fa-regular fa-clock"></i></a>`;
                }
            },
            {
                data: 'id',
                orderable: false,
                className: 'no-sort',
                render: function (data, type, item, meta) {
                    // Порядковый номер
                    let displayIndex = meta.row + meta.settings._iDisplayStart + 1;

                    if (item.is_can_edit) {
                        return `<span class="text-primary cursor-pointer update-measuring py-2 d-flex justify-content-center" 
                                title="Редактировать" data-uvm="${item.id}">
                                    ${displayIndex}
                            </span>`;
                    } else {
                        return item.num;
                    }
                }
            },
            {
                data: 'date',
                render: function (data, type, item) {
                    return item.ru_date;
                }
            }
        ];

        for (let i = 0; i < measuringCount; i++) {
            columns.push(
                {
                    data: `result_${i}`,
                    orderable: false,
                    render: function (data, type, item) {
                        return item.result[i];
                    }
                }
            )
        }

        return columns;
    }

    /**
     * Данные измерений
     * @type {jQuery}
     */
    let journalDataTable = $journal.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.umc_id = measuringId;
            },
            url : '/ulab/vlk/getVlkMeasuringAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: getColumns(),
        language:{
            processing: 'Подождите...',
            search: '',
            searchPlaceholder: "Поиск...",
            lengthMenu: 'Отображать _MENU_  ',
            info: 'Записи с _START_ до _END_ из _TOTAL_ записей',
            infoEmpty: 'Записи с 0 до 0 из 0 записей',
            infoFiltered: '(отфильтровано из _MAX_ записей)',
            infoPostFix: '',
            loadingRecords: 'Загрузка записей...',
            zeroRecords: 'Записи отсутствуют.',
            emptyTable: 'В таблице отсутствуют данные',
            paginate: {
                first: 'Первая',
                previous: 'Предыдущая',
                next: 'Следующая',
                last: 'Последняя'
            },
            buttons: {
                colvis: '',
                copy: '',
                excel: '',
                print: ''
            },
            aria: {
                sortAscending: ': активировать для сортировки столбца по возрастанию',
                sortDescending: ': активировать для сортировки столбца по убыванию'
            }
        },
        lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
        pageLength: 25,
        order: [[ 1, "desc" ]],
        dom: 'frtB<"bottom"lip>',
        buttons: [
            {
                extend: 'colvis',
                titleAttr: 'Выбрать'
            },
            {
                extend: 'copy',
                titleAttr: 'Копировать',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'excel',
                titleAttr: 'excel',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'print',
                titleAttr: 'Печать',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            }
        ],
        bSortCellsTop: true,
        scrollX:       true,
    });

    journalDataTable.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            journalDataTable
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        });
    });

    /*journal filters*/
    $('.filter').on('change', function () {
        journalDataTable.ajax.reload();
        journalDataTable.draw();
    });

    $body.on('click', '.measuring-history', function () {
        const id = $(this).data('id');
        const $form = $('#history-modal-form');

        $form.find('.title').empty();
        $form.find('.history-info').empty();

        $.ajax({
            url: "/ulab/vlk/getHistoryMeasuringAjax/",
            data: {"id": id},
            dataType: "json",
            method: "POST",
            success: function (data) {
                $form.find('.title').text(`История измерения`);

                let html = ``;

                $.each(data.history, function (i, item) {
                    html +=
                        `<div class="row">
                            <div class="col-auto">${item.date}</div>
                            <div class="col">${item.action}</div>
                            <div class="col-auto">${item.short_name}</div>
                        </div>`;
                })

                if ( html === '' ) {
                    html = `История отсутствует`;
                }

                $form.find('.history-info').html(html);

                $.magnificPopup.open({
                    items: {
                        src: '#history-modal-form',
                        type: 'inline',
                    },
                });
            }
        });

        return false;
    });

    /** Алгоритм проведения контрольных процедур */
    $body.on('click', '.control', function () {
        const controlWrapper = $('#controlWrapper'),
            createWrapper = controlWrapper.find('.create-wrapper'),
            $shukhertChart = controlWrapper.find('#shukhertChart'),
            $resultTable = controlWrapper.find('#resultTable');

        let selectedControls = controlWrapper.find('input.control:checked').length;

        $('#predictionStatus').empty();
        $resultTable.empty();

        if (shewhartChart) {
            shewhartChart.destroy();

            if (!$shukhertChart.hasClass('d-none')) {
                $shukhertChart.addClass('d-none');
            }
        }

        if (!selectedControls) {
            if (!createWrapper.hasClass('d-none')) {
                createWrapper.addClass('d-none');
            }
        } else {
            createWrapper.removeClass('d-none');
        }
    });

    $body.on('change', '.control-date', function () {
        const controlWrapper = $('#controlWrapper'),
            $shukhertChart = controlWrapper.find('#shukhertChart'),
            $resultTable = controlWrapper.find('#resultTable');

        $('#predictionStatus').empty();
        $resultTable.empty();

        if (shewhartChart) {
            shewhartChart.destroy();

            if (!$shukhertChart.hasClass('d-none')) {
                $shukhertChart.addClass('d-none');
            }
        }
    });

    /** Показать результаты расчетов */
    $body.on('click', '.show-result-table', function (e) {
        e.preventDefault();
        const calcTable = $('#calcTable');

        if (!calcTable.hasClass('d-none')) {
            calcTable.addClass('d-none');
        } else {
            calcTable.removeClass('d-none');
        }
    });

    /**
     * Сбросить значения
     * @param items
     */
    function resetValue(items) {
        items.each(function (index, item) {
            $(this).val("");
        });
    }

    function getMessageErrorContent(messageError = "") {
        if (!messageError) {
            return false;
        }

        return `<div class="messages">
              <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                  <div>
                      ${messageError}
                  </div>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
          </div>`;
    }

    /** Проверить заполненность полей */
    function checkEmptyFields(fieldsSelector, errorWrapperSelector, resetSelector, messageError) {
        const wrapper = $(errorWrapperSelector),
            resetElement = $(resetSelector);

        let valueEmpty = $(fieldsSelector).filter(function () {
            return $(this).val() === null || $(this).val() === '';
        })

        wrapper.find(".messages").remove();

        if (valueEmpty.length) {
            let messageErrorContent = getMessageErrorContent(messageError)

            wrapper.prepend(messageErrorContent);
            resetValue(resetElement);
            return false;
        }

        return true;
    }

    function updatePredictionStatus(predictability) {
        $('#predictionStatus').html('');

        if (!Object.keys(predictability).length) {
            return false;
        }

        let alertColor = predictability['isPredictable'] ? 'alert-success' : 'alert-danger';
        $('#predictionStatus').append(`<div class="alert ${alertColor} mt-4" role="alert">${predictability['message']}</div>`);
    }

    /**
     * Добавляет прямую линию с пользовательским стилем
     * @param chart
     * @param lineValue
     * @param color
     * @param label
     * @param borderDash
     */
    function addCustomLine(chart, lineValue, color, label, borderDash) {
        let dataset = {
            label: label,
            data: Array(chart.data.labels.length).fill(lineValue),
            borderColor: color,
            pointRadius: 0,  // Убираем точки
            borderWidth: 2,  // Толщина линии
            fill: false,     // Без заливки под линией
            borderDash: borderDash ? [5, 5] : [], // Пунктирная линия
        };

        chart.data.datasets.push(dataset);
    }

    /**
     * Рисует график (КК Шухарта)
     * @param data
     */
    function drawChart(data) {
        const ctx = $('#shukhertChart');

        const hasBottomWarning = data['warningLimit']['bottom'] !== null;
        const hasBottomAction = data['actionLimit']['bottom'] !== null;

        if (shewhartChart) {
            shewhartChart.destroy();
        }

        shewhartChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: data['xAxisLabel'],
                datasets: [{
                    label: 'Результат контрольной процедуры',
                    data: data['points'],
                    borderWidth: 1,
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        labels: {
                            generateLabels: function(chart) {
                                let labels = Chart.defaults.plugins.legend.labels.generateLabels(chart);
                                let filteredLabels = labels.filter(function(label) {
                                    // Оставляем только нужные лейблы, остальные будут скрыты
                                    return label.text === 'Результат контрольной процедуры';
                                });
                                return filteredLabels;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: data['chartLabel'],
                        font: {
                            size: 25
                        },
                        padding: {
                            bottom: 30
                        }
                    }
                },
            }
        });

        addCustomLine(shewhartChart, data['averageLine']['data'], data['averageLine']['color'], 'Средняя линия');
        addCustomLine(shewhartChart, data['warningLimit']['top'], data['warningLimit']['color'], 'UCL'); // Верхний предел предупреждения
        addCustomLine(shewhartChart, data['actionLimit']['top'], data['actionLimit']['color'], 'U'); // Верхний предел действия

        // Нижний предел предупреждения
        if (hasBottomWarning) {
            addCustomLine(shewhartChart, data['warningLimit']['bottom'], data['warningLimit']['color'], 'LCL');
        }
        // Нижний предел действия
        if (hasBottomAction) {
            addCustomLine(shewhartChart, data['actionLimit']['bottom'], data['actionLimit']['color'], 'L');
        }

        shewhartChart.update();
    }

    function fillTableData(data) {
        const $resultTable = $('#resultTable');

        $resultTable.empty(); // Очищаем таблицу перед заполнением новыми данными

        if (!data || typeof data !== 'object') {
            console.error('Неверные данные результатов расчётов');
            return;
        }

        let measuringCount = data['measuringCount'],
            points = data['points'],
            controlData = data['controlData'];

        let isMeasuringCount = (measuringCount && typeof measuringCount === 'number' && measuringCount > 0);

        let caption = '';
        switch(data['control']) {
            case 'repetition':
                caption = 'Контроль повторяемости';
                break;
            case 'precision':
                caption = 'Контроль прецизионности';
                break;
            case 'deviation':
                caption = 'Контроль погрешности с применением ОК';
                break;
            default:
                caption = '';
        }

        let $resHeadMeasuring = '';
        if (isMeasuringCount) {
            for (let i = 0; i < measuringCount; i++) {
                $resHeadMeasuring += `<th scope="col" class="head-measuring">Результат ${i + 1}-го измерения</th>`;
            }
        } else {
            console.error('Неверные или отсутствуют данные измерения');
            return;
        }

        let $tableBody = ``;
        if (Array.isArray(points)) {
            for (let key in points) {
                let $resColMeasuring = ``;
                if (isMeasuringCount) {
                    for (let i = 0; i < measuringCount; i++) {
                        let controlVal = (controlData && controlData[key] && Array.isArray(data['controlData'][key])) ? controlData[key][i] : '';
                        $resColMeasuring += `<td class="col-measuring">${controlVal}</td>`;
                    }
                }

                $tableBody +=
                    `<tr>
                    <th scope="row">${+key + 1}</th>
                    ${$resColMeasuring}
                    <td>${data['averages'][key]}</td>
                    <td>${points[key]}</td>
                    <td>${data['averageLine']['data']}</td>
                    <td>${data['warningLimit']['top']}</td>
                    <td>${data['warningLimit']['bottom'] ? data['warningLimit']['bottom'] : ''}</td>
                    <td>${data['actionLimit']['top']}</td>
                    <td>${data['actionLimit']['bottom'] ? data['actionLimit']['bottom'] : ''}</td>
                </tr>`;
            }
        } else {
            console.error('Неверные данные контрольных процедур');
            return;
        }

        let $table =
            `<div class="table-responsive my-3 calc-wrapper">
                <a href="javascript:void(0);" class="show-result-table">Показать результаты расчетов</a>
                <table class="table table-bordered text-center caption-top mt-3 d-none" id="calcTable">
                    <caption>${caption}</caption>
                    <thead>
                        <tr class="table-secondary align-middle">
                            <th scope="col">№</th>
                            ${$resHeadMeasuring}
                            <th scope="col">Средний результат из-я</th>
                            <th scope="col">Результат контрольной процедуры</th>
                            <th scope="col">Средняя линия</th>
                            <th scope="col">Предел предупр., в</th>
                            <th scope="col">Предел предупр., низ</th>
                            <th scope="col">Предел действия, в</th>
                            <th scope="col">Предел действия, низ</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${$tableBody}
                    </tbody>
                </table>
            </div>`;

        $resultTable.append($table);
    }

    /** Создать КК Шухарта */
    $('#controlForm').on('submit', function(e) {
        e.preventDefault();

        const btn = $(this).find('.create-shukhert-chart'),
            controlWrapper = $('#controlWrapper');

        let selectedControls = controlWrapper.find('input.control:checked');

        // Проверка выбора алгоритмов проведения контрольных процедур
        if (!selectedControls.length) {
            let messageErrorContent = getMessageErrorContent('Внимание! Не выбран алгоритм проведения контрольных процедур!')
            controlWrapper.prepend(messageErrorContent);
            return false;
        }

        // Проверка заполненность полей временного диапазона
        let checkDate =
            checkEmptyFields(
                `#dateStart, #dateEnd`,
                `#controlWrapper`,
                ``,
                'Внимание! Не все поля временного диапазона заполнены!'
            );
        if (!checkDate) {
            return false;
        }

        btn.find('i').addClass('spinner-animation');
        btn.addClass('disabled');

        let formData = $(this).serialize();

        $.ajax({
            method: 'POST',
            url: '/ulab/shewhart/processControlRMG762014Ajax',
            dataType: 'json',
            data: {
                form: formData,
            },
            success: function (data) {
                console.log('data', data);

                if (data['success']) {
                    drawChart(data);
                    updatePredictionStatus(data['predictability']);
                    // Заполнение данных в таблицу
                    fillTableData(data);

                    $('#shukhertChart').removeClass('d-none');
                } else {
                    let messageErrorContent = getMessageErrorContent(data['errors'])
                    controlWrapper.prepend(messageErrorContent);
                }

                btn.find('i').removeClass('spinner-animation');
                btn.removeClass('disabled');

                return false;
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
                console.error(msg);
            }
        });
    });

});