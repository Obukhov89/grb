/**
 * Результаты испытаний
 */
$(function ($) {
    const currUserId = $('#curr_user').val()

    let $body = $('body')

    let $btnStart = $('.btn_start')
    let $btnPause = $('.btn_pause')
    let $btnStop = $('.btn_stop')
    let $btnCreateProtocol = $('.btn_create_protocol')
    let $btnMeasurementSheet = $('.btn_group_measurement_sheet')

    if ( $('#deal_id').val() > 0 ) {
        $('#filter-deal').val($('#deal_id').val())
    }

    const $journalMethods = $('#journal_methods')

    $body.on('change', '.all-check', function () {
        $body.find(`.method-check`).not(".disabled").prop("checked", $(this).prop("checked")).trigger('change')

        toggleProbe()
    })

    $body.on('change', '.method-check', function () {
        let umtrId = $(this).data('umtr_id')

        let parent = $body.find(`.all-check`),
            all = true,
            curState = $(this).prop("checked")

        $body.find(`.method-check`).not(".disabled").each(function () {
            return all = ($(this).prop("checked") === curState)
        })

        if ( all ) {
            parent.prop({
                indeterminate: false,
                checked: curState
            })
        } else {
            parent.prop({
                indeterminate: true,
                checked: false
            })
        }

        $body.find(`.umtr-id-${umtrId}`).not(".disabled").prop("checked", $(this).prop("checked"))

        toggleProbe()
    })

    $body.on('change', '.probe-check', function () {
        let ugtpId = $(this).val()
        let umtrId = $(this).data('umtr_id')

        let parent = $body.find(`#method_name_${umtrId}`).not(".disabled"),
            parentParent = $body.find(`.all-check`),
            all = true,
            curState = $(this).prop("checked")

        $body.find(`.method-id-${umtrId}`).not(".disabled").each(function () {
            return all = ($(this).prop("checked") === curState)
        })

        if ( all ) {
            parent.prop({
                indeterminate: false,
                checked: curState
            })
        } else {
            parent.prop({
                indeterminate: true,
                checked: false
            })
        }

        $body.find(`.method-check`).not(".disabled").each(function () {
            return all = ($(this).prop("checked") === curState)
        })

        if ( all ) {
            parentParent.prop({
                indeterminate: false,
                checked: curState
            })
        } else {
            parentParent.prop({
                indeterminate: true,
                checked: false
            })
        }

        toggleProbe()
    })

    function toggleProbe() {
        let probeIdList = $journalMethods.find(".probe-check:checked").not(".disabled").map(function() {
            return $(this).val();
        }).get()

        let measurementIdList = $journalMethods.find(".probe-check:checked").not(".disabled").map(function() {
            let tmp = $(this).data('measurement_id')
            if ( tmp === `undefined` ) { return }
            return $(this).data('measurement_id')
        }).get()

        if ( probeIdList.length > 0 ) {
            $btnStart.removeClass('disabled')
            $btnPause.removeClass('disabled')
            $btnStop.removeClass('disabled')
            $btnCreateProtocol.removeClass('disabled')
        } else {
            $btnStart.addClass('disabled')
            $btnPause.addClass('disabled')
            $btnStop.addClass('disabled')
            $btnCreateProtocol.addClass('disabled')
        }

        if ( measurementIdList.length > 0 ) {
            $btnMeasurementSheet.removeClass('disabled')
        } else {
            $btnMeasurementSheet.addClass('disabled')
        }
    }

    let stickyEl2 = new Sticksy('.js-sticky-widget2', {topSpacing: 20, listen: true,})
    stickyEl2.onStateChanged = function (state) {
        if(state === 'fixed') {
            stickyEl2.nodeRef.classList.add('widget--sticky')
        } else {
            stickyEl2.nodeRef.classList.remove('widget--sticky')
        }
    }

    $('.select2').select2({
        theme: 'bootstrap-5',
    })

    let journalDataTable = $journalMethods.DataTable({
        bAutoWidth: false,
        autoWidth: false,
        fixedColumns: false,
        processing: true,
        serverSide: true,
        bSortCellsTop: true,
        scrollX: true,
        fixedHeader: false,
        colReorder: false,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.deal_id = $('#filter-deal').val()
                d.deal_title = $('#filter-deal_title').val()
                d.cipher = $('#filter-cipher').val()
                d.material_id = $('#filter-material').val()
                d.method_id = $('#filter-methods').val()
                d.probe_id = $('#filter-probe').val()
                d.status = $('#filter-status').val()
                d.assigned_id = $('#filter-user').val()
                d.overall = 1
            },
            url : '/ulab/result/getMethodsProbeJournalAjax/',
            dataSrc: function (json) {
                $('.count_deal').text(json.count_deal)
                $('.count_method').text(json.recordsFiltered)
                return json.data
            }
        },
        columns: [
            {
                data: 'REQUEST_TITLE',
                orderable: false
            },
            {
                data: 'material_cipher',
                orderable: false,
            },
            {
                data: 'checkbox',
                orderable: false,
                width: "20px",
                className: 'cursor-pointer text-center',
                render: function (data, type, item) {
                    let disabled = currUserId != item.assigned_id ? 'disabled' : ''

                    return `
                        <input 
                            class="form-check-input probe-check method-id-${item['umtr_id']} umtr-id-${item['umtr_id']} ${disabled}" 
                            type="checkbox" 
                            data-method_id="${item['method_id']}" 
                            data-umtr_id="${item['umtr_id']}" 
                            data-measurement_id="${item.measurement.id}"
                            id="method_name_${item['method_id']}_${item['probe_number']}_${item['ugtp_id']}"
                            name="gost_check[${item['ugtp_id']}]"
                            value="${item['ugtp_id']}"
                        >`
                }
            },
            {
                data: 'view_gost',
                orderable: false,
                render: function (data, type, item) {
                    let strFor = currUserId != item.assigned_id ? '' : `method_name_${item['method_id']}_${item['probe_number']}_${item['ugtp_id']}`
                    return `<label class="form-check-label cusor-pointer" for="${strFor}">${item.view_gost}</label>`
                }
            },
            {
                data: 'comment',
                orderable: false,
                className: 'text-center',
                render: function (data, type, item) {
                    console.log(item)
                    if ( item.comment !== '' && item.comment !== null && item.comment !== undefined ) {
                        return `<button type="button" class="btn btn-link cursor-pointer" data-bs-container="body" data-bs-trigger="focus" data-bs-toggle="popover" data-bs-placement="top" title="Комментарий" data-bs-content="${item.comment}">
                                <i class="text-red fa-regular fa-message-dots icon-big"></i>
                            </button>`
                    }

                    return `<a href="#" class="disabled cursor-pointer"><i class="text-black fa-regular fa-message icon-big"></i></a>`
                }
            },
            {
                data: 'status',
                orderable: false,
                className: 'text-center',
                render: function (data, type, item) {
                    if ( item.state_action.state === 'start' ) {
                        return `<i class="fa-regular fa-circle-play icon-big" title="В работе"></i>`
                    }

                    if ( item.state_action.state === 'pause' ) {
                        return `<i class="fa-solid fa-circle-pause icon-big" title="Приостановлено"></i>`
                    }

                    if ( item.state_action.state === 'complete' ) {
                        return `<i class="fa-regular fa-circle-check icon-big" title="Завершено"></i>`
                    }

                    return `<i class="fa-regular fa-circle icon-big" title="Не начато"></i>`
                }
            },
            {
                data: 'li',
                orderable: false,
                className: 'text-center',
                render: function (data, type, item) {
                    if ( item.measurement.id === undefined || currUserId != item.assigned_id ) {
                        return `<button type="button" class="btn bg-transparent border-0 mt-0 p-0 measurement-sheet disabled" title="Лист измерения">
                                    <i class="fa-solid fa-calculator font-size-35"></i>
                                </button>`
                    }
                    return `<button type="button" class="btn bg-transparent border-0 mt-0 p-0 measurement-sheet" data-measurement="${item.measurement.id}" data-ugtp="${item.ugtp_id}" data-method="${item.method_id}" title="Лист измерения">
                                <i class="fa-solid fa-calculator font-size-35"></i>
                            </button>`
                }
            },
            {
                data: 'units',
                orderable: false,
                className: 'text-center'
            },
            {
                data: 'tu',
                orderable: false,
                className: 'text-center',
                render: function (data, type, item) {
                    if ( item.tech_condition_id > 0 ) {
                        return `<a href="/ulab/techCondition/edit/${item.tech_condition_id}" class="text-decoration-none">${item.tech.reg_doc} ${item.tech.measured_properties_name}</a>`
                    }

                    return `-`
                }
            },
            {
                data: 'ewq3',
                orderable: false,
                className: 'save_ajax',
                render: function (data, type, item) {
                    let html = `<div class="form-text text-start aa">${item.normative_text?? ''}</div>`

                    if ( item.readonly_normative_value || currUserId != item.assigned_id ) {
                        html += `<div class="normative-value w-100 border p-2 like-input text-start bg-light-secondary like-input"
                                         title="Доступно для редактирования если ТУ выбрано при формировании ТЗ и ТУ не нормируемое и нет номера протокола или есть номер но протокол разблокирован">
                                        ${item.normative_value}
                                    </div>`
                    } else {
                        html += `<input data-cell="normative_val" type="text"
                                           class="form-control normative-value bg-white"
                                           name="normative_value[${item.mtr_id}][${item.ugtp_id}]"
                                           title="${item.normative_message?? ''}"
                                           value="${item.normative_value?? ''}">`
                    }

                    return html
                }
            },
            {
                data: 'ewq3',
                orderable: false,
                className: 'save_ajax',
                render: function (data, type, item) {
                    let html = `<div class="form-text text-danger text-start">${item.out_range?? ''}</div>`
                    let readonly = item.confirm_oa_readonly ? 'readonly' : ''

                    if ( item.readonly_actual_value || currUserId != item.assigned_id  ) {
                        html += `<input ${item.actual_value_type}
                                    class="me-2 actual-value actual-value-${item.ugtp_id} w-100 border p-2 bg-light-secondary"
                                    name="actual_value[${item.mtr_id}][${item.ugtp_id}]"
                                    value="${item.actual_value}"
                                    data-cell="actual_value"
                                    title="Доступно для редактирования если нет номера протокола или есть номер но протокол на редактировании и у методики ф/значение текстом или у методики ф/значение не текстом и нет листа измерения"
                                    readonly>`
                    } else {
                        html += `<div class="d-flex actual-value-wrapper mb-1">
                                    <input ${item.actual_value_type}
                                            class="me-2 actual-value actual-value-${item.ugtp_id} w-100 border p-2 "
                                            name="actual_value[${item.mtr_id}][${item.ugtp_id}]"
                                            title="${item.actual_value_message?? ''}"
                                            value="${item.actual_value}" ${readonly}
                                            data-cell="actual_value"
                                        >
                                </div>`
                    }

                    return html
                }
            },
            {
                data: 'ewq3',
                orderable: false,
                className: 'save_ajax',
                render: function (data, type, item) {
                    let html = ``
                    let isInvalid = item.match_message? 'is-invalid' : ''

                    if ( item.readonly_match || currUserId != item.assigned_id ) {
                        html += `<div class="form-control match w-100 p-2 text-start bg-light-secondary text-start like-input ${isInvalid}"
                                     aria-describedby="match_${item.mtr_id}_${item.ugtp_id}"
                                     title="Доступно для редактирования если нет номера протокола или есть номер но протокол на редактировании и ТУ не нормируемое или в ТУ ручное управление 'соотв/не соотв' или фактических значений более 1">
                                    ${item.match_view?? ''}
                                </div>
                                <div id="match_${item.mtr_id}_${item.ugtp_id}"
                                     class="invalid-feedback">
                                    ${item.match_message?? ''}
                                </div>`
                    } else {
                        html += `<select data-cell="match" class="form-select match ${isInvalid}"
                                        name="match[${item.mtr_id}][${item.ugtp_id}]"
                                        aria-describedby="match_${item.mtr_id}_${item.ugtp_id}">
                                    <option value="0"
                                        ${item.match == 0? 'selected' : ''}>
                                        Не соответствует
                                    </option>
                                    <option value="1"
                                        ${item.match == 1? 'selected' : ''}>
                                        Соответствует
                                    </option>
                                    <option value="2"
                                        ${item.match == 2? 'selected' : ''}>
                                        -
                                    </option>
                                    <option value="3"
                                        ${item.match == 3? 'selected' : ''}>
                                        Не нормируется
                                    </option>
                                </select>
                                <div id="match_${item.mtr_id}_${item.ugtp_id}"
                                     class="invalid-feedback" title="${item.match_message?? ''}">
                                    
                                </div>`
                    }


                    return html
                }
            },
            {
                data: 'ewq3',
                orderable: false,
                className: 'text-center cursor-pointer',
                render: function (data, type, item) {
                    let html = ``
                    let title = `Диапазон: ${item.range_ao}`

                    if ( item.in_field ) {
                        html += `<i class="fa-regular fa-circle-check icon-big" title="${title}"></i>`
                    } else {
                        html += `<i class="fa-regular fa-circle-xmark icon-big" title="${title}></i>`
                    }

                    return html
                }
            },
            {
                data: 'assigned_user',
                orderable: false,
                className: ''
            },
        ],
        columnDefs: [{ visible: false, targets: 0 },{ visible: false, targets: 1 }],
        drawCallback: function (settings) {
            $('[data-bs-toggle="popover"]').popover()

            let api = this.api();
            let rows = api.rows({ page: 'current' }).nodes();
            let last = null;
            let last2 = null;
            let data = rows.data()

            api.column(0, { page: 'current' })
                .data()
                .each(function (group, i) {
                    if (last2 !== group) {
                        $(rows)
                            .eq(i)
                            .before(
                                `<tr class="bg-green">
                                    <td colspan="12">
                                        <strong>${group}</strong>
                                    </td>
                                </tr>`
                            );

                        last2 = group
                    }
                })

            api.column(1, { page: 'current' })
                .data()
                .each(function (group, i) {
                    if (last !== group) {
                        $(rows)
                            .eq(i)
                            .before(
                                `<tr class="bg-sky-blue">
                                    <td colspan="12">
                                        <div class="form-check">
                                            <input 
                                                class="form-check-input method-check" 
                                                type="checkbox" 
                                                data-umtr_id="${data[i].umtr_id}" 
                                                data-ugtp_id="${data[i].ugtp_id}" 
                                                data-method_id="${data[i].method_id}" 
                                                id="method_name_${data[i].umtr_id}"
                                            >
                                            <label class="form-check-label cusor-pointer" for="method_name_${data[i].umtr_id}">
                                                <strong>${group}</strong>
                                            </label>
                                        </div>
                                    </td>
                                </tr>`
                            );

                        last = group
                    }
                })
        },
        language: dataTablesSettings.language,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        pageLength: 0,
        order: [[ 2, "asc" ]],
        dom: 'frt<"bottom"lip>',
        buttons: dataTablesSettings.buttons,
    })

    journalDataTable.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            journalDataTable
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        })
    })

    journalDataTable.on('draw', function () {
        $btnStart.addClass('disabled')
        $btnPause.addClass('disabled')
        $btnStop.addClass('disabled')
        $btnCreateProtocol.addClass('disabled')
        $btnMeasurementSheet.addClass('disabled')
        $body.find('.all-check').prop({indeterminate: false,checked: false})
    })

    $('.filter, .selected-probe').on('input', function () {
        journalDataTable.ajax.reload()
        journalDataTable.draw()
    })

    // сохраняем фильтры
    $(window).bind('unload', function () {
        let filterMethod = $('#filter-methods').val()
        let filterMaterial = $('#filter-material').val()
        let filterProbe = $('#filter-probe').val()

        // $.cookie('filters', {filter_method : filterMethod, filter_material: filterMaterial, filter_probe: filterProbe})
        $.cookie('filter_method', filterMethod)
        $.cookie('filter_material', filterMaterial)
        $.cookie('filter_probe', filterProbe)
    })

    setFilterFromCookie($('#filter-methods'), 'filter_method')
    setFilterFromCookie($('#filter-material'), 'filter_material')
    setFilterFromCookie($('#filter-probe'), 'filter_probe')


    $body.on('change', '.save_ajax input, .save_ajax select', function () {
        const $thisCell = $(this)

        let data = journalDataTable.row($thisCell.closest('tr')).data()
        let cell = $thisCell.data('cell')
        let val = $thisCell.val()

        $.ajax({
            url: `/ulab/result/getSaveCellDataAjax/`,
            method: "POST",
            // dataType: "json",
            data: {
                ugtp_id: data.ugtp_id,
                cell: cell,
                val: val,
            },
            success: function (json) {
            }
        })
    })


    $body.on('click', '.btn_start', function () {
        let probeIdList = $(".probe-check:checked").map(function() {
            return $(this).val();
        }).get()

        let $contentBlock = $('#gost_room_form .gost_room_container')

        $.ajax({
            url: `/ulab/result/getRoomsConditionsAjax/`,
            method: "POST",
            dataType: "json",
            data: {
                probe_id_list: probeIdList,
            },
            success: function (json) {
                $.magnificPopup.open({
                    items: {
                        src: '#gost_room_form',
                        type: 'inline',
                        fixedContentPos: false,
                    },
                    closeOnBgClick: false,
                    showCloseBtn: true,
                    callbacks: {
                        beforeOpen: function() {
                            $contentBlock.empty()

                            let html = ``

                            if ( json.length === 0 ) {
                                $contentBlock.append(`<p>Нечего стартовать</p>`)
                            } else {
                                $.each(json, function (i, probe) {

                                    if ( probe[0].rooms.length === 0 ) { return true }

                                    html += `<div class="text-center mb-2"><strong>${probe[0].material_name} | ${probe[0].cipher}</strong></div>`

                                    $.each(probe, function (j, method) {
                                        if ( method.rooms.length === 0 ) { return true }

                                        let roomHtml = ``
                                        $.each(method.rooms, function (k, room) {
                                            let textLabelTemp = ``
                                            let textLabelWet = ``
                                            let textLabelPress = ``

                                            if ( method.is_not_cond_temp == 1 ) {
                                                textLabelTemp = `Не нормируется`
                                            } else {
                                                textLabelTemp = `От ${method.cond_temp_1} до ${method.cond_temp_2}`
                                            }
                                            if ( method.is_not_cond_wet == 1 ) {
                                                textLabelWet = `Не нормируется`
                                            } else {
                                                textLabelWet = `От ${method.cond_wet_1} до ${method.cond_wet_2}`
                                            }
                                            if ( method.is_not_cond_pressure == 1 ) {
                                                textLabelPress = `Не нормируется`
                                            } else {
                                                textLabelPress = `От ${method.cond_pressure_1} до ${method.cond_pressure_2}`
                                            }
                                            roomHtml += `<div class="row mb-2 room_block">
                                                            <div class="col-5">
                                                                <div class="form-check pt-2">
                                                                    <input type="checkbox" class="form-check-input check_room" id="check_room_${method.ugtp_id}_${room.room_id}" name="form[${method.ugtp_id}][room_id]" value="${room.room_id}" checked>
                                                                    <label class="form-check-label" for="check_room_${method.ugtp_id}_${room.room_id}">${room.name}</label>
                                                                </div>
                                                            </div>
                                                            <div class="col">
                                                                <input type="number" step="any" class="form-control input_temp" data-room_id="${room.room_id}" name="form[${method.ugtp_id}][${room.room_id}][temp]" value="${room.temp?? ''}">
                                                                <div class="form-text">${textLabelTemp}</div>
                                                            </div>
                                                            <div class="col">
                                                                <input type="number" step="any" class="form-control input_wet" data-room_id="${room.room_id}" name="form[${method.ugtp_id}][${room.room_id}][wet]" value="${room.wet?? ''}">
                                                                <div class="form-text">${textLabelWet}</div>
                                                            </div>
                                                            <div class="col">
                                                                <input type="number" step="any" class="form-control input_press" data-room_id="${room.room_id}" name="form[${method.ugtp_id}][${room.room_id}][pressure]" value="${room.pressure?? ''}">
                                                                <div class="form-text">${textLabelPress}</div>
                                                            </div>
                                                        </div>`
                                        })

                                        html += `<div class="gost_room_block mb-3">
                                                    <div class="row mb-1">
                                                        <div class="col-5"><strong>${method.name}</strong></div>
                                                        <div class="col">Температура</div>
                                                        <div class="col">Влажность</div>
                                                        <div class="col">Давление</div>
                                                    </div>
                                                    ${roomHtml}
                                                </div>
                                                <div class="line-dashed-small"></div>`
                                    })
                                })

                                $contentBlock.append(html)
                            }
                        },
                    }
                })
            }
        })

        return false
    })

    $body.on('change', '.check_room', function () {
        let $block = $(this).closest('.room_block')

        $block.find('input.form-control').prop('disabled', !$(this).prop('checked'))
    })

    $body.on('input', '.input_temp', function () {
        let value = $(this).val()
        let roomId = $(this).data('room_id')

        $(`.input_temp[data-room_id="${roomId}"]`).val(value)
    })
    $body.on('input', '.input_wet', function () {
        let value = $(this).val()
        let roomId = $(this).data('room_id')

        $(`.input_wet[data-room_id="${roomId}"]`).val(value)
    })
    $body.on('input', '.input_press', function () {
        let value = $(this).val()
        let roomId = $(this).data('room_id')

        $(`.input_press[data-room_id="${roomId}"]`).val(value)
    })

    $body.on('click', '.btn_pause', function () {
        startStop($(this), 'newPauseTrialAjax')

        return false
    })

    $body.on('click', '.btn_stop', function () {
        startStop($(this), 'newStopTrialAjax')

        return false
    })

    function startStop($button, method) {
        let probeIdList = $(".probe-check:checked").map(function(){
            return $(this).val();
        }).get()

        if ( probeIdList.length === 0 ) {
            return false
        }

        let btnHtml = $button.html()
        $button.html(`<i class="fa-solid fa-arrows-rotate spinner-animation"></i>`)
        $button.addClass('disabled')

        $.ajax({
            url: `/ulab/result/${method}/`,
            method: "POST",
            dataType: "text",
            data: {
                probe_id_list: probeIdList,
            },
            success: function () {
                journalDataTable.on('draw', function () {
                    $button.html(btnHtml)
                    $button.removeClass('disabled')
                })

                journalDataTable.ajax.reload()
                journalDataTable.draw()
            }
        })
    }


    /** Получить лист измерения */
    $body.on('click', '.measurement-sheet', function (e) {
        const btn = $(this)
        const $form = $('#measurementModalForm')
        const $contentBlock = $form.find('.measurement_content')

        let measurementId = $(this).data('measurement'),
            ugtpId = $(this).data('ugtp'),
            methodId = $(this).data('method');

        let measurementObject = []
        measurementObject.push({ugtpId : ugtpId, measurement_id : measurementId, methodId : methodId})

        btn.find('i').addClass('spinner-animation');
        btn.addClass('disabled');

        if (btn.find('.fa-calculator').length > 0) {
            btn.prop('disabled', true);
            btn.find('.fa-calculator').addClass('d-none');
            btn.append(
                `<div class="spinner-border" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>`
            )
        }


        $.ajax({
            method: 'POST',
            url: '/ulab/result/getMeasurementSheetAjax',
            data: {
                measurement_object: measurementObject,
            },
            success: function (data) {
                $.magnificPopup.open({
                    items: {
                        src: '#measurementModalForm',
                        type: 'inline',
                        fixedContentPos: false,
                    },
                    closeOnBgClick: false,
                    showCloseBtn: true,
                    callbacks: {
                        beforeOpen: function() {
                            $contentBlock.empty()

                            $contentBlock.append(data)
                        },
                    }
                })

                btn.find('i').removeClass('spinner-animation')
                btn.removeClass('disabled')

                if (btn.find('.fa-calculator').length > 0) {
                    btn.prop('disabled', false);
                    btn.find('.fa-calculator').removeClass('d-none');
                    btn.find('.spinner-border').remove();
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


    /** Получить групп лист измерения */
    $body.on('click', '.btn_group_measurement_sheet', function (e) {
        const $btn = $(this)
        const $form = $('#measurementModalForm')
        const $contentBlock = $form.find('.measurement_content')

        let measurementObject = $journalMethods.find(".probe-check:checked").map(function() {
            let measurementId = $(this).data('measurement_id')
            if ( measurementId === `undefined` ) { return }
            let ugtpId = $(this).val()
            let methodId = $(this).data('method_id')

            return {ugtpId : ugtpId, measurement_id : measurementId, methodId : methodId}
        }).get()

        if ( measurementObject.length === 0 ) { return false }

        let btnHtml = $btn.html()
        $btn.html(`<i class="fa-solid fa-arrows-rotate spinner-animation"></i>`)
        $btn.addClass('disabled')

        $.ajax({
            method: 'POST',
            url: '/ulab/result/getMeasurementSheetAjax',
            data: {
                measurement_object: measurementObject,
            },
            success: function (data) {
                $.magnificPopup.open({
                    items: {
                        src: '#measurementModalForm',
                        type: 'inline',
                        fixedContentPos: false,
                    },
                    closeOnBgClick: false,
                    showCloseBtn: true,
                    callbacks: {
                        beforeOpen: function() {
                            $contentBlock.empty()

                            $contentBlock.append(data)
                        },
                    }
                })

                $btn.html(btnHtml)
                $btn.removeClass('disabled')
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
     * добавить оборудование
     */
    $body.on('change', '.equipment', function () {
        const equipmentUsed = $('.equipment-used'),
            selectedOption = $(this).find('option:selected');

        let equipmentUsedCount = equipmentUsed.length,
            selectedText = selectedOption.text(),
            selectedGost = selectedOption.data('gost'),
            selectedOborud = $(this).val(),
            aldOborudGost = JSON.parse($('#equipmentIds').val());

        aldOborudGost.push(JSON.parse(selectedOborud));
        $('#equipmentIds').val(JSON.stringify(aldOborudGost));

        let newOption = `<option value="${selectedOborud}">${selectedText}</option>`

        equipmentUsed.append(newOption)
    })


    /**
     * удалить оборудование
     */
    $body.on('dblclick', '.equipment-used option', function () {
        let aldOborudGost = JSON.parse($('#equipmentIds').val());
        let iEquipmentUsed = aldOborudGost.indexOf(+this.value);

        if (iEquipmentUsed !== -1) {
            aldOborudGost.splice(iEquipmentUsed, 1);
        } else {
            return false
        }

        $('#equipmentIds').val(JSON.stringify(aldOborudGost));
        $('.equipment-used option').eq(iEquipmentUsed).remove()
    })


    $body.on('click', '.protocol-information', function () {
        const dealId = $('#deal_id').val()

        const _this = $(this),
            protocolInformation = $('#protocolInformation')

        let protocolId = $(this).data('protocol')

        _this.prop('disabled', true);
        _this.find('.icon').addClass('d-none');
        _this.append(
            `<div class="spinner-border" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>`
        );

        // Сбрасываем значения формы
        protocolInformation.find('.verify').html('');
        protocolInformation.find('.equipment-used').html('');
        protocolInformation.find('.equipment').html('');
        protocolInformation.find('#equipmentIds').val('');
        protocolInformation[0].reset()

        $.ajax({
            method: 'POST',
            url: '/ulab/result/getProtocolInfoAjax',
            data: {
                deal_id: dealId,
                protocol_id: protocolId,
            },
            dataType: "json",
            success: function (data) {
                let count = Object.keys(data).length

                if (count) {
                    let verify = protocolInformation.find('.verify'),
                        equipmentUsed = protocolInformation.find('.equipment-used'),
                        equipment = protocolInformation.find('#equipment');

                    $.each(data['assigned'], function(key, value) {
                        verify.append(`<option value="${value['user_id']}">${value['user_name']}</option>`);
                    });

                    $.each(data['protocol_equipment'], function(key, value) {
                        equipmentUsed.append(
                            `<option value="${value['b_o_id']}" class="${value['bg_color']}">
                                ${value['TYPE_OBORUD']} ${value['OBJECT']}, инв. номер ${value['REG_NUM']}
                            </option>`
                        );
                    });

                    $.each(data['oboruds'], function(key, value) {
                        equipment.append(
                            `<option value="${value['o_id']}" data-gost="${value['g_id']}">
                                ${value['TYPE_OBORUD']} ${value['OBJECT']}
                                , инв. номер ${value['REG_NUM']} ${value['reg_doc']}, ${value['clause']}
                            </option>`
                        );
                    });

                    let protocolNum = data['protocol']['NUMBER'] ? data['protocol']['NUMBER'] : 'Номер не присвоен';

                    protocolInformation.find('.title').text(`Информация по протоколу № ${protocolNum}`);
                    protocolInformation.find('input[name="protocol_id"]').val(protocolId);

                    // Общая информация
                    let protocolType = data['protocol']['PROTOCOL_TYPE'] ? data['protocol']['PROTOCOL_TYPE'] : 0;
                    protocolInformation.find('.protocol-type').val(protocolType);
                    let protocolTemplate = data['protocol']['id_template'] ? data['protocol']['id_template'] : 0;
                    protocolInformation.find('.protocol-template').val(protocolTemplate);
                    protocolInformation.find('.verify').val(data['protocol']['verify']);
                    protocolInformation.find('.no-evaluate').prop('checked', +data['protocol']['NO_COMPLIANCE']);

                    //Информация об испытаниях
                    let dateBegin = protocolInformation.find('.date-begin'),
                        dateEnd = protocolInformation.find('.date-end'),
                        temp1 = protocolInformation.find('.temp1'),
                        temp2 = protocolInformation.find('.temp2'),
                        wet1 = protocolInformation.find('.wet1'),
                        wet2 = protocolInformation.find('.wet2'),
                        changeTrialsConditions = protocolInformation.find('.change-trials-conditions'),
                        attestatInProtocol = protocolInformation.find('.attestat-in-protocol'),
                        switchAttestat = attestatInProtocol.closest('.switch'),
                        isChangeTrialsConditions = +data['protocol']['CHANGE_TRIALS_CONDITIONS'] || data['is_deal_nk'],
                        changeTrialsDate = +data['protocol']['CHANGE_TRIALS_DATE'] || data['is_deal_osk'] || data['is_deal_nk'];

                    dateBegin.val(data['dates_trials']['date_begin']);
                    dateBegin.prop('readonly', !changeTrialsDate);
                    dateBegin.data('protocol', protocolId);
                    changeTrialsDate ? dateBegin.removeClass('bg-light-secondary').addClass('bg-white') :
                        dateBegin.removeClass('bg-white').addClass('bg-light-secondary');

                    dateEnd.val(data['dates_trials']['date_end']);
                    dateEnd.prop('readonly', !changeTrialsDate);
                    dateEnd.data('protocol', protocolId);
                    changeTrialsDate ? dateEnd.removeClass('bg-light-secondary').addClass('bg-white') :
                        dateEnd.removeClass('bg-white').addClass('bg-light-secondary');

                    temp1.val(data['conditions']['TEMP_O']);
                    temp1.prop('readonly', !isChangeTrialsConditions);
                    isChangeTrialsConditions ? temp1.removeClass('bg-light-secondary').addClass('bg-white') :
                        temp1.removeClass('bg-white').addClass('bg-light-secondary');

                    temp2.val(data['conditions']['TEMP_TO_O']);
                    temp2.prop('readonly', !isChangeTrialsConditions);
                    isChangeTrialsConditions ? temp2.removeClass('bg-light-secondary').addClass('bg-white') :
                        temp2.removeClass('bg-white').addClass('bg-light-secondary');

                    wet1.val(data['conditions']['VLAG_O']);
                    wet1.prop('readonly', !isChangeTrialsConditions);
                    isChangeTrialsConditions ? wet1.removeClass('bg-light-secondary').addClass('bg-white') :
                        wet1.removeClass('bg-white').addClass('bg-light-secondary');

                    wet2.val(data['conditions']['VLAG_TO_O']);
                    wet2.prop('readonly', !isChangeTrialsConditions);
                    isChangeTrialsConditions ? wet2.removeClass('bg-light-secondary').addClass('bg-white') :
                        wet2.removeClass('bg-white').addClass('bg-light-secondary');

                    protocolInformation.find('.change-trials-date').prop('checked', +data['protocol']['CHANGE_TRIALS_DATE']);
                    changeTrialsConditions.data('protocol', protocolId);
                    changeTrialsConditions.prop('checked', +data['protocol']['CHANGE_TRIALS_CONDITIONS']);
                    protocolInformation.find('.output-in-protocol').prop('checked', +data['protocol']['OUTPUT_IN_PROTOCOL']);

                    //Информация об оборудовании
                    protocolInformation.find('.revert-default').data('protocolId', protocolId);
                    protocolInformation.find('#equipmentIds').val(data['equipment_ids_json']);

                    //Данные объекта испытаний
                    protocolInformation.find('.object-description').val(data['object_data']['DESCRIPTION']);
                    protocolInformation.find('.object').val(data['object_data']['OBJECT']);
                    protocolInformation.find('.place-probe').val(data['object_data']['PLACE_PROBE']);
                    protocolInformation.find('.date-probe').val(data['object_data']['DATE_PROBE']);
                    protocolInformation.find('.additional-information').val(data['protocol']['DOP_INFO']);
                    protocolInformation.find('.protocol-outside-lis').prop('checked', +data['protocol']['PROTOCOL_OUTSIDE_LIS']);
                    attestatInProtocol.prop('checked', +data['protocol']['ATTESTAT_IN_PROTOCOL']);
                    attestatInProtocol.prop('disabled', !+data['protocol']['IN_ATTESTAT_DIAPASON']); // Если значение или методика у протокола НЕ в диапазоне аттестата(не соответствуют условиям аттестации), не в ОА, то выдать(выбрать) протокол "C аттестатом аккредитации" нельзя

                    if (!+data['protocol']['IN_ATTESTAT_DIAPASON']) {
                        if (!switchAttestat.hasClass('opacity-30')) {
                            switchAttestat.addClass('opacity-30');
                            switchAttestat.prop('title', 'Значение или методика у протокола не в ОА или в ОА но не подтверждено');
                        }
                    } else {
                        switchAttestat.removeClass('opacity-30');
                        switchAttestat.prop('title', '');
                    }
                }

                $.magnificPopup.open({
                    items: {
                        src: protocolInformation,
                        type: 'inline',
                        fixedContentPos: false
                    },
                    closeOnBgClick: false
                });

                _this.prop('disabled', false);
                _this.find('.icon').removeClass('d-none');
                _this.find('.spinner-border').remove();

                protocolInformation.find('.select2').select2({
                    theme: 'bootstrap-5',
                    width: 'resolve',
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
        })
    })
})


function setFilterFromCookie($select, keyCookie) {
    let value = $.cookie(keyCookie)
    if ( value !== '' && value !== undefined ) {
        $.each(value.split(','), function(i, e) {
            $select.find(`option[value='${e}']`).prop("selected", true)
        })
        $select.trigger('change')
    }
}
