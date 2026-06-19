$(function ($) {
    const body = $('body'),
        journal = $('#journalCondition');

    function currentDatetime() {
        let d = new Date();

        let month = d.getMonth()+1,
            day = d.getDate(),
            h = d.getHours(),
            m = d.getMinutes(),
            s = d.getSeconds();

        return d.getFullYear() + '-' + (month<10 ? '0' : '') + month + '-' + (day<10 ? '0' : '') + day +
            " " + (h<10 ? '0' : '') + h + ":" + (m<10 ? '0' : '') + m + ":" + (s<10 ? '0' : '') + s;
    }

    /**
     * добавить условия
     */
    body.on('click', '.add-conditions', function () {
        const conditionsModalForm = $('#conditionsModalForm');

        let roomId = +$('#selectRoom').val();
            roomId = roomId > 0 ? roomId : '';

        conditionsModalForm.find('.remove-condition').remove();
        conditionsModalForm.find('#room').val(roomId);

        let date = currentDatetime().slice(0, 16);
        conditionsModalForm.find('#date').val(date);
        conditionsModalForm.find('#conditionsId').val('');

        $.magnificPopup.open({
            items: {
                src: conditionsModalForm,
                type: 'inline',
                fixedContentPos: false
            },
            closeOnBgClick: false
        });
    });

    /**
     * добавить атмосферное давление
     */
    body.on('click', '.add-pressure', function (e) {
        e.preventDefault();
        const pressureModalForm = $('#pressureModalForm'),
            listGroupWrapper = pressureModalForm.find('.list-group-wrapper');
        let data = [];

        // Очищаем данные
        let curDatetime = currentDatetime().slice(0, 16);
        pressureModalForm.find('#date').val(curDatetime);
        pressureModalForm.find('#pressure').val('');
        pressureModalForm.find('#pressureId').val('');
        pressureModalForm.find('.list-group-item').remove();

        if ( !listGroupWrapper.hasClass('d-none') ) {
            listGroupWrapper.addClass('d-none');
        }

        // Получаем данные за выбранную дату
        $.ajax({
            method: 'POST',
            url: '/ulab/lab/getPressureByDateAjax',
            data: {
                datetime: curDatetime
            },
            dataType: "json",
            async: false,
            success: function (result) {
                data = result;
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

        // Если есть данные за текущий день (сохранённые ранее), выводим их
        if (data.length) {
            listGroupWrapper.removeClass('d-none');

            let listGroupItems = '';
            $.each(data, function( index, value ) {
                listGroupItems +=
                    `<button type="button" class="list-group-item list-group-item-action" 
                        data-pressure="${value['pressure']}" data-date="${value['date']}" data-id="${value['id']}">
                        ${value['datetime_ru']} - давление: ${value['pressure']}
                    </button>`
            });

            pressureModalForm.find('.list-group').append(listGroupItems);
        }

        // Отображаем модальное окно, со всеми полученными данными
        $.magnificPopup.open({
            items: {
                src: pressureModalForm,
                type: 'inline',
                fixedContentPos: false
            },
            closeOnBgClick: false,
            callbacks: {
                open: function () {
                    let $content = $(this.content);

                    $content.on('click', '#addPressure', function() {
                        const _this = $(this),
                            inputPressure = pressureModalForm.find('#pressure'),
                            inputDate = pressureModalForm.find('#date'),
                            inputPressureId = pressureModalForm.find('#pressureId'),
                            titlePressure = pressureModalForm.find('.title-pressure');

                        pressureModalForm.find('.list-group-item').removeClass('active');
                        titlePressure.text('Новое атмосферное давление');
                        inputPressure.val('');
                        inputDate.val(curDatetime);
                        inputPressureId.val('');

                        if ( !_this.hasClass('d-none') ) {
                            _this.addClass('d-none');
                        }
                        inputDate.prop('readonly', false);
                    })

                    $content.on('click', '.list-group-item', function() {console.log($(this));
                        const _this = $(this),
                            inputPressure = pressureModalForm.find('#pressure'),
                            inputDate = pressureModalForm.find('#date'),
                            inputPressureId = pressureModalForm.find('#pressureId'),
                            titlePressure = pressureModalForm.find('.title-pressure'),
                            addPressure = pressureModalForm.find('#addPressure');

                        let pressure = _this.data('pressure'),
                            date = _this.data('date');
                        id = _this.data('id');

                        pressureModalForm.find('.list-group-item').removeClass('active');
                        _this.addClass('active');

                        titlePressure.text('Редактирование атмосферного давления');
                        inputPressure.val(pressure);
                        inputDate.val(date);
                        inputPressureId.val(id);

                        addPressure.removeClass('d-none');
                        inputDate.prop('readonly', true);
                    })
                }
            }
        });

    });

    /**
     * Изменение даты атмосферного давления
     */
    body.on('change', '#pressureModalForm #date', function (e) {
        const pressureModalForm = $('#pressureModalForm'),
            listGroupWrapper = pressureModalForm.find('.list-group-wrapper');
        let data = [];

        // Очищаем данные
        let curDatetime = $(this).val();
        pressureModalForm.find('#pressure').val('');
        pressureModalForm.find('#pressureId').val('');
        pressureModalForm.find('.list-group-item').remove();

        if ( !listGroupWrapper.hasClass('d-none') ) {
            listGroupWrapper.addClass('d-none');
        }

        // Получаем данные за выбранную дату
        $.ajax({
            method: 'POST',
            url: '/ulab/lab/getPressureByDateAjax',
            data: {
                datetime: curDatetime
            },
            dataType: "json",
            async: false,
            success: function (result) {
                data = result;
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

        // Если есть данные за текущий день (сохранённые ранее), выводим их
        if (data.length) {
            listGroupWrapper.removeClass('d-none');

            let listGroupItems = '';
            $.each(data, function( index, value ) {
                listGroupItems +=
                    `<button type="button" class="list-group-item list-group-item-action" 
                        data-pressure="${value['pressure']}" data-date="${value['date']}" data-id="${value['id']}">
                        ${value['datetime_ru']} - давление: ${value['pressure']}
                    </button>`
            });

            pressureModalForm.find('.list-group').append(listGroupItems);
        }

        // Отображаем модальное окно, со всеми полученными данными
        $.magnificPopup.open({
            items: {
                src: pressureModalForm,
                type: 'inline',
                fixedContentPos: false
            },
            closeOnBgClick: false,
            callbacks: {
                open: function () {
                    let $content = $(this.content);

                    $content.on('click', '#addPressure', function() {
                        const _this = $(this),
                            inputPressure = pressureModalForm.find('#pressure'),
                            inputDate = pressureModalForm.find('#date'),
                            inputPressureId = pressureModalForm.find('#pressureId'),
                            titlePressure = pressureModalForm.find('.title-pressure');

                        pressureModalForm.find('.list-group-item').removeClass('active');
                        titlePressure.text('Новое атмосферное давление');
                        inputPressure.val('');
                        inputDate.val(curDatetime);
                        inputPressureId.val('');

                        if ( !_this.hasClass('d-none') ) {
                            _this.addClass('d-none');
                        }
                        inputDate.prop('readonly', false);
                    })

                    $content.on('click', '.list-group-item', function() {console.log($(this));
                        const _this = $(this),
                            inputPressure = pressureModalForm.find('#pressure'),
                            inputDate = pressureModalForm.find('#date'),
                            inputPressureId = pressureModalForm.find('#pressureId'),
                            titlePressure = pressureModalForm.find('.title-pressure'),
                            addPressure = pressureModalForm.find('#addPressure');

                        let pressure = _this.data('pressure'),
                            date = _this.data('date');
                        id = _this.data('id');

                        pressureModalForm.find('.list-group-item').removeClass('active');
                        _this.addClass('active');

                        titlePressure.text('Редактирование атмосферного давления');
                        inputPressure.val(pressure);
                        inputDate.val(date);
                        inputPressureId.val(id);

                        addPressure.removeClass('d-none');
                        inputDate.prop('readonly', true);
                    })
                }
            }
        });

    });

    /**
     * Изменение даты условий окружающей среды
     */
    body.on('change', '#conditionsModalForm #date', function (e) {
        const conditionsModalForm = $('#conditionsModalForm');
        let data = [];

        // Очищаем данные
        let curDatetime = $(this).val();
        conditionsModalForm.find('#pressure').val('');

        // Получаем данные за выбранную дату
        $.ajax({
            method: 'POST',
            url: '/ulab/lab/getPressureByDateAjax',
            data: {
                datetime: curDatetime
            },
            dataType: "json",
            async: false,
            success: function (result) {
                data = result;
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

        if ( data.length ) {
            let pressureData = data[data.length-1];
            conditionsModalForm.find('#pressure').val(pressureData['pressure']);
        }

    });

    /**
     * журнал условий
     */
    let journalDataTable = journal.DataTable({
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
            type: 'POST',
            data: function (d) {
                d.dateStart = $('#inputDateStart').val();
                d.dateEnd = $('#inputDateEnd').val();
                d.room = $('#selectRoom option:selected').val();
                d.everywhere = $('#filter_everywhere').val();
            },
            url: '/ulab/lab/getJournalConditionAjax/',
            dataSrc: function (json) {
                return json.data;
            }
        },
        columns: [
            {
                data: 'is_match',
                orderable: false,
                render: function (data, type, item) {
                    if (item.is_method_match == 1 && item.is_oborud_match == 1) {
                        return `<span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 text-success" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                    </svg>
                                </span>`;
                    } else if (item.is_method_match == 0 || item.is_oborud_match == 0) {
                        return `<span class="cursor-pointer not-conformity" data-condition-id="${item.u_c_id}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 text-danger" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                                    </svg>
                                </span>`;
                    }

                    return '';
                }
            },
            // {
            //     data: 'updated_at',
            //     render: function (data, type, item) {
            //         if (item.is_can_edit) {
            //             return `<span class="text-primary cursor-pointer update-conditions"
            //                     title="Редактировать показатели измерений" data-condition-id="${item.u_c_id}">
            //                         ${item.ru_updated_at}
            //                 </span>`;
            //         } else {
            //             return `<span>${item.ru_updated_at}</span>`;
            //         }
            //     }
            // },
            {
                data: 'created_at',
                render: function (data, type, item) {
                    if (item.is_can_edit) {
                        return `<span class="text-primary cursor-pointer update-conditions" 
                                title="Редактировать показатели измерений" data-condition-id="${item.u_c_id}">
                                    ${item.ru_created_at}
                            </span>`;
                    } else if(item.is_can_dop_edit) {
                        return `<span class="text-primary cursor-pointer update-dop-conditions" 
                                title="Редактировать показатели измерений" data-condition-id="${item.u_c_id}">
                                    ${item.ru_created_at}
                            </span>`;
                    }
                    else {
                        return `<span>${item.ru_created_at}</span>`;
                    }
                }
            },
            {
                data: 'temp',
            },
            {
                data: 'humidity',
                render: $.fn.dataTable.render.ellipsis(32, true),
            },
            {
                data: 'pressure',
                render: function (data, type, item) {
                    return `<a class="barForm">${item.pressure}</a>`
                }
            },
            {
                data: 'voltage',
                render: $.fn.dataTable.render.ellipsis(32, true)
            },
            {
                data: 'frequency',
                render: $.fn.dataTable.render.ellipsis(32, true)
            },
            {
                data: 'room',
                orderable: false,
            },
        ],
        columnDefs: [{ visible: false, targets: 0 }],
        order: [[ 1, "desc" ]],
        language: dataTablesSettings.language,
        dom: 'frtB<"bottom"lip>',
        buttons: dataTablesSettings.buttons,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        pageLength: 25,
    });

    journalDataTable.columns().every(function () {
        $(this.header()).closest('thead').find('.search:eq(' + this.index() + ')').on('keyup change clear', function () {
            journalDataTable
                .column($(this).parent().index())
                .search(this.value)
                .draw();
        })
    });

    /**
     * фильтры журнала
     */
    $('.filter').on('change', function () {
        journalDataTable.ajax.reload()
        journalDataTable.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.assign(location.pathname)
    })

    /**
     * обновить данные условий
     */
    body.on('click', '.update-conditions', function () {
        let conditionId = $(this).data('conditionId');

        $.ajax({
            method: 'POST',
            url: '/ulab/lab/getConditionDataAjax',
            data: {
                id: conditionId
            },
            dataType: "json",
            success: function (data) {
                if (data['id']) {
                    const conditionsModalForm = $('#conditionsModalForm');

                    conditionsModalForm.find('#conditionsId').val(conditionId);
                    conditionsModalForm.find('#room').val(data['room_id']);
                    conditionsModalForm.find('#temp').val(data['temp']);
                    conditionsModalForm.find('#humidity').val(data['humidity']);
                    conditionsModalForm.find('#pressure').val(data['pressure']);
                    conditionsModalForm.find('#date').val(data['date']);
                    conditionsModalForm.find('#voltage').val(data['voltage']);
                    conditionsModalForm.find('#frequency').val(data['frequency']);

                    conditionsModalForm.find('.remove-condition').remove();
                    conditionsModalForm.append(`<button type="button" class="btn btn-danger ms-2 remove-condition" 
                        data-condition-id="${conditionId}">Удалить</button>`);

                    $.magnificPopup.open({
                        items: {
                            src: conditionsModalForm,
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
     * обновить данные условий
     */
    body.on('click', '.update-dop-conditions', function () {
        let conditionId = $(this).data('conditionId');

        $.ajax({
            method: 'POST',
            url: '/ulab/lab/getConditionDataAjax',
            data: {
                id: conditionId
            },
            dataType: "json",
            success: function (data) {
                if (data['id']) {
                    const conditionsModalForm = $('#conditionsDopModalForm');

                    conditionsModalForm.find('#conditionsId').val(conditionId);
                    conditionsModalForm.find('#voltage').val(data['voltage']);
                    conditionsModalForm.find('#frequency').val(data['frequency']);

                    $.magnificPopup.open({
                        items: {
                            src: conditionsDopModalForm,
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
     * удалить данные условий
     */
    body.on('click', '.remove-condition', function () {
        let conditionId = $(this).data('conditionId');

        if (!confirm('Вы действительно хотите удалить данные условий? После удаления, данные нельзя будет востановить')) {
            return false;
        }

        $.ajax({
            method: 'POST',
            url: '/ulab/lab/removeConditionAjax',
            data: {
                id: conditionId
            },
            dataType: "json",
            success: function (data) {
                $.magnificPopup.close()

                if (data['success']) {
                    location.reload();
                } else {
                    $('.alert-title').text('Внимание!')
                    $('.alert-content').text(data['error']['message'])
                    $('#alert_modal').removeClass('col-md-8')

                    $.magnificPopup.open({
                        items: {
                            src: $('#alert_modal'),
                            type: 'inline',
                            fixedContentPos: false
                        },
                        closeOnBgClick: false
                    })

                    return false
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
     * вывести не соответсвие темпиратуры, влажности уловию методики и оборудования
     */
    body.on('click', '.not-conformity', function () {
        let conditionId = $(this).data('conditionId');

        $.ajax({
            method: 'POST',
            url: '/ulab/lab/getNotConformityAjax',
            data: {
                id: conditionId
            },
            dataType: "json",
            success: function (data) {
                if (data['success']) {
                    location.reload();
                } else {
                    $('.alert-title').text('Внимание')
                    $('.alert-content').html(data['error']['message'])
                    $('#alert_modal').addClass('col-md-8')

                    $.magnificPopup.open({
                        items: {
                            src: $('#alert_modal'),
                            type: 'inline',
                            fixedContentPos: false
                        },
                        closeOnBgClick: false
                    })

                    return false
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
     * отобразить скачать отчёт
     */
    body.on('click', '#selectRoom', function () {
        const navItemDropdown = $('#navItemDropdown');
        let room = +$('#selectRoom').val();
        let roomId = +$(this).val() - 100;

        if (room <= 0) {
            if (!navItemDropdown.hasClass('d-none')) {
                navItemDropdown.removeClass('d-block');
                navItemDropdown.addClass('d-none');
            }
        } else {
            if (!navItemDropdown.hasClass('d-block')) {
                navItemDropdown.removeClass('d-none');
                navItemDropdown.addClass('d-block');
            }
        }

        $.ajax({
            method: 'POST',
            url: '/ulab/lab/getMeanConditionsAjax',
            data: {
                room_id: roomId
            },
            dataType: "json",
            success: function (data) {
                if (data['success']) {
                    const conditionsModalForm = $('#conditionsModalForm');

                    conditionsModalForm.find('#temp').val(data['data']['random_temp']);
                    conditionsModalForm.find('#humidity').val(data['data']['random_wet']);
                    //conditionsModalForm.find('#pressure').val(data['data']['random_pressure']);
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
     * скачать отчёт
     */
    body.on('click', '.download-report li a', function (e) {
        e.preventDefault();

        let roomId = $('#selectRoom').val(),
            yearId = $(this).data('yearId'),
            monthId = $(this).data('monthId');

        if (roomId && yearId) {
            window.open(`/Condition/condition_doc_new.php?ID=${roomId}&year=${yearId}&month=${monthId}`);
        }
    });

    /**
     * получить средние значение условий
     */
    body.on('change', '#room', function (e) {
        let roomId = +$(this).val() - 100;

        $.ajax({
            method: 'POST',
            url: '/ulab/lab/getMeanConditionsAjax',
            data: {
                room_id: roomId
            },
            dataType: "json",
            success: function (data) {
                console.log('data', data)
                if (data['success']) {
                    const conditionsModalForm = $('#conditionsModalForm');

                    conditionsModalForm.find('#temp').val(data['data']['random_temp']);
                    conditionsModalForm.find('#humidity').val(data['data']['random_wet']);
                    //conditionsModalForm.find('#pressure').val(data['data']['random_pressure']);
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

    body.on('click', '.barForm', function (e) {
        let data = journalDataTable.row(e.target.closest('tr')).data()

        $('#barForm').find('.temp').text(data.temp)
        $('#barForm').find('.press').text(data.inf_pressure)
        $('#barForm').find('.first_pressure').text(data.first_pressure)
        $('#barForm').find('.pressure').text(data.pressure)
        $('#barForm').find('.bar').text(data.bar)
        $('#barForm').find('.f1').text(data.f1)
        $('#barForm').find('.af').text(data.af)
        $('#barForm').find('.f2').text(data.f2)
        $('#barForm').find('.fb').text(data.fb)
        $('#barForm').find('.f3').text(data.f3.toFixed(2))
        $('#barForm').find('.formula').text(data.formula)
        $('#barForm').find('.formula_bar').text(data.formula_bar)
        $('#barForm').find('.formula_result').text(data.formula_result)

        $.magnificPopup.open({
            items: {
                src: '#barForm'
            },
            type: 'inline',
            closeBtnInside: true,
            fixedContentPos: false,
            closeOnBgClick: false,
        })
    });

    body.on('click', '.edit-scale', function () {

        $.ajax({
            method: 'POST',
            url: '/ulab/lab/getScaleBarAjax',
            dataType: "json",
            success: function (data) {
                let tr = ''
                let r = []
                let table = $('#scaleBar').find('.scale-body')

                table.empty()

                $.each(data, function (i, v) {
                    if (i < 14) {
                        r.push(`<tr>
                            <td class="text-center">${v.scale_mark}</td>
                            <td><input type="number" step="0.01" class="form-control save-scale w-100" data-id="${v.id}" 
                                value="${v.scale_correction}"></td>`)
                    } else {
                        r[i-14] += `
                            <td class="text-center">${v.scale_mark}</td>
                            <td><input type="number" step="0.01" class="form-control save-scale w-100" data-id="${v.id}" 
                                value="${v.scale_correction}"></td>`
                    }
                })

                $.each(r, function (i, v) {
                    r[i] += `</tr>`

                    tr += r[i]
                })

                table.append(tr)


                $.magnificPopup.open({
                    items: {
                        src: '#scaleBar'
                    },
                    type: 'inline',
                    closeBtnInside: true,
                    fixedContentPos: false,
                    closeOnBgClick: false,
                })
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

    body.on('change', '.save-scale', function () {
        let id = $(this).data('id'),
            value = $(this).val()

        $.ajax({
            method: 'POST',
            url: '/ulab/lab/setScaleBarAjax',
            data: {
                id: id,
                value: value,
            },
            dataType: "json",
            success: function (data) {

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
    })
});
