/**
 * Результаты испытаний
 */
$(function ($) {
    let $body = $('body')

    let $btnStart = $('.btn_start')
    let $btnPause = $('.btn_pause')
    let $btnStop = $('.btn_stop')
    let $btnCreateProtocol = $('.btn_create_protocol')
    let $btnMeasurementSheet = $('.btn_group_measurement_sheet')
    let $btnUnboundProtocol = $('.btn_unbound_protocol')

    const dealId = $('#deal_id').val()

    const $journalMethods = $('#journal_methods')

    $body.on('change', '.all-check', function () {
        $body.find(`.method-check`).prop("checked", $(this).prop("checked")).trigger('change')

        toggleProbe()
    })

    $body.on('change', '.method-check', function () {
        let methodId = $(this).data('method_id')

        let parent = $body.find(`.all-check`),
            all = true,
            curState = $(this).prop("checked")

        $body.find(`.method-check`).each(function () {
            return all = ($(this).prop("checked") === curState)
        })

        if (all) {
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

        $body.find(`.method-id-${methodId}`).prop("checked", $(this).prop("checked"))

        toggleProbe()
    })

    /**
     * фактическое значение
     */
    $body.on('click', '.add-value-actual', function () {
        let actualValueWrapper = $(this).parents('.actual-value-wrapper'),
            cloneActualValueWrapper = $(actualValueWrapper).clone(true),
            tdActualValue = actualValueWrapper.closest('.td-actual-value');

        cloneActualValueWrapper.find('.actual-value').val('');

        cloneActualValueWrapper.find('.add-value-actual').replaceWith(
            `<button type="button" class="btn btn-danger del-value-actual mt-0 btn-square">
                 <i class="fa-solid fa-minus icon-fix"></i>
            </button>`
        );

        tdActualValue.append(cloneActualValueWrapper);
    })

    $body.on('click', '.actual-value-wrapper button.del-value-actual', function () {
        let actualValueWrapper = $(this).closest('.actual-value-wrapper');

        actualValueWrapper.remove();
    })

    $body.on('change', '.probe-check', function () {
        let methodId = $(this).data('method_id')

        let parent = $body.find(`#method_name_${methodId}`),
            parentParent = $body.find(`.all-check`),
            all = true,
            curState = $(this).prop("checked")

        $body.find(`.method-id-${methodId}`).each(function () {
            return all = ($(this).prop("checked") === curState)
        })

        if (all) {
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

        $body.find(`.method-check`).each(function () {
            return all = ($(this).prop("checked") === curState)
        })

        if (all) {
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
        let probeIdList = $journalMethods.find(".probe-check:checked").map(function () {
            return $(this).val();
        }).get()

        let measurementIdList = $journalMethods.find(".probe-check:checked").map(function () {
            let tmp = $(this).data('measurement_id')
            if (tmp === `undefined`) { return }
            return $(this).data('measurement_id')
        }).get()

        if (probeIdList.length > 0) {
            $btnStart.removeClass('disabled')
            $btnPause.removeClass('disabled')
            $btnStop.removeClass('disabled')
            $btnCreateProtocol.removeClass('disabled')
            $btnUnboundProtocol.removeClass('disabled')
        } else {
            $btnStart.addClass('disabled')
            $btnPause.addClass('disabled')
            $btnStop.addClass('disabled')
            $btnCreateProtocol.addClass('disabled')
            $btnUnboundProtocol.addClass('disabled')
        }

        if (measurementIdList.length > 0) {
            $btnMeasurementSheet.removeClass('disabled')
        } else {
            $btnMeasurementSheet.addClass('disabled')
        }
    }

    let stickyEl2 = new Sticksy('.js-sticky-widget2', { topSpacing: 20, listen: true, })
    stickyEl2.onStateChanged = function (state) {
        if (state === 'fixed') {
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
        stateSave: true,
        stateDuration: -1,
        ajax: {
            type: 'POST',
            data: function (d) {
                d.deal_id = dealId
                d.material_id = $('#filter-material').val()
                d.method_id = $('#filter-methods').val()
                d.probe_id = $('#filter-probe').val()
                d.status = $('#filter-status').val()
                d.lab_id = $('#filter-lab').val()
                d.protocol_id = $('.selected-probe:checked').val()
                d.selected_protocol_id = $('.selected_protocol_id').val()
            },
            url: '/ulab/result/getMethodsProbeJournalAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'view_gost',
                orderable: false,
            },
            {
                data: 'ewq',
                orderable: false,
                width: "20px",
                className: 'cursor-pointer text-center',
                render: function (data, type, item) {
                    return `
                        <input 
                            class="form-check-input probe-check method-id-${item['method_id']}" 
                            type="checkbox" 
                            data-method_id="${item['method_id']}" 
                            data-measurement_id="${item.measurement.id}"
                            id="method_name_${item['method_id']}_${item['probe_number']}_${item['ugtp_id']}"
                            name="gost_check[${item['ugtp_id']}]"
                            value="${item['ugtp_id']}"
                        >`
                }
            },
            {
                data: 'material_name',
                orderable: false,
                render: function (data, type, item) {
                    let materialGroup = ``
                    if (item.material_group_name != null) {
                        materialGroup = `(${item.material_group_name})`
                    }

                    return `<label class="cursor-pointer" for="method_name_${item['method_id']}_${item['probe_number']}_${item['ugtp_id']}">${item.material_name} ${materialGroup}</label>`
                }
            },
            {
                data: 'cipher',
                orderable: false,
                render: function (data, type, item) {
                    return `<label class="cursor-pointer" for="method_name_${item['method_id']}_${item['probe_number']}_${item['ugtp_id']}">${item.cipher}</label>`
                }
            },
            {
                data: 'ewq2',
                orderable: false,
                className: 'text-center',
                render: function (data, type, item) {
                    if (item.state_action.state === 'start') {
                        return `<i class="fa-regular fa-circle-play icon-big" title="В работе"></i>`
                    }

                    if (item.state_action.state === 'pause') {
                        return `<i class="fa-solid fa-circle-pause icon-big" title="Приостановлено"></i>`
                    }

                    if (item.state_action.state === 'complete') {
                        return `<i class="fa-regular fa-circle-check icon-big" title="Завершено"></i>`
                    }

                    return `<i class="fa-regular fa-circle icon-big" title="Не начато"></i>`
                }
            },
            {
                data: 'ewq3',
                orderable: false,
                className: 'text-center',
                render: function (data, type, item) {
                    if (item.measurement.id === undefined) {
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
                data: 'ewq3',
                orderable: false,
                className: 'text-center',
                render: function (data, type, item) {
                    if (item.tech_condition_id > 0) {
                        return `<a href="/ulab/techCondition/edit/${item.tech_condition_id}" class="text-decoration-none">${item.tech.reg_doc} ${item.tech.measured_properties_name}</a>`
                    }

                    return `-`
                }
            },
            {
                data: 'ewq3',
                orderable: false,
                className: 'text-center',
                render: function (data, type, item) {
                    let html = `<div class="form-text text-start aa">${item.normative_text ?? ''}</div>`

                    if (item.readonly_normative_value) {
                        html += `<div class="normative-value w-100 border p-2 like-input text-start bg-light-secondary like-input"
                                         title="Доступно для редактирования если ТУ выбрано при формировании ТЗ и ТУ не нормируемое и нет номера протокола или есть номер но протокол разблокирован">
                                        ${item.normative_value}
                                    </div>`
                    } else {
                        html += `<textarea class="form-control normative-value bg-white w-100"
                                           name="normative_value[${item.mtr_id}][${item.ugtp_id}]">${item.normative_value ?? ''}</textarea>`
                    }
                    html += `<div class="form-text text-start">${item.normative_message ?? ''}</div>`

                    return html
                }
            },
            {
                data: 'ewq3',
                orderable: false,
                className: '',
                render: function (data, type, item) {
                    let html = `<div class="form-text text-danger text-start">${item.out_range ?? ''}</div>`
                    let readonly = item.confirm_oa_readonly ? 'readonly' : ''

                    if (item.readonly_actual_value) {
                        html += `<input ${item.actual_value_type}
                                    class="me-2 actual-value actual-value-${item.ugtp_id} w-100 border p-2 bg-light-secondary"
                                    name="actual_value[${item.mtr_id}][${item.ugtp_id}]"
                                    value="${item.actual_value}"
                                    title="Доступно для редактирования если нет номера протокола или есть номер но протокол на редактировании и у методики ф/значение текстом или у методики ф/значение не текстом и нет листа измерения"
                                    readonly>`
                    } else {
                        html += `<div class="d-flex actual-value-wrapper mb-1">
                                    <input ${item.actual_value_type}
                                            class="me-2 actual-value actual-value-${item.ugtp_id} w-100 border p-2 "
                                            name="actual_value[${item.mtr_id}][${item.ugtp_id}]"
                                            value="${item.actual_value}" ${readonly}>
                                </div>`
                    }
                    html += `<div class="form-text text-start">${item.actual_value_message ?? ''}</div>`

                    return html
                }
            },
            {
                data: 'ewq3',
                orderable: false,
                className: '',
                render: function (data, type, item) {
                    let html = `<div class="form-text text-danger text-start">${item.out_range ?? ''}</div>`
                    let readonly = item.confirm_oa_readonly ? 'readonly' : ''

                    if (item.readonly_actual_value) {
                        html += `<input ${item.actual_value_type}
                                    class="me-2 actual-value actual-value-${item.ugtp_id} w-100 border p-2 bg-light-secondary"
                                    name="uncertainty_value[${item.mtr_id}][${item.ugtp_id}]"
                                    value="${item.uncertainty == null ? '' : item.uncertainty}"
                                    title="Доступно для редактирования если нет номера протокола или есть номер но протокол на редактировании и у методики ф/значение текстом или у методики ф/значение не текстом и нет листа измерения"
                                    readonly>`
                    } else {
                        html += `<div class="d-flex actual-value-wrapper mb-1">
                                    <input ${item.actual_value_type}
                                            class="me-2 actual-value actual-value-${item.ugtp_id} w-100 border p-2 "
                                            name="uncertainty_value[${item.mtr_id}][${item.ugtp_id}]"
                                            value="${item.uncertainty}" ${readonly}>
                                </div>`
                    }

                    return html
                }
            },
            {
                data: 'ewq3',
                orderable: false,
                className: '',
                render: function (data, type, item) {
                    let html = ``
                    let isInvalid = item.match_message ? 'is-invalid' : ''

                    if (item.readonly_match) {
                        html += `<div class="form-control match w-100 p-2 text-start bg-light-secondary text-start like-input ${isInvalid}"
                                     aria-describedby="match_${item.mtr_id}_${item.ugtp_id}"
                                     title="Доступно для редактирования если нет номера протокола или есть номер но протокол на редактировании и ТУ не нормируемое или в ТУ ручное управление 'соотв/не соотв' или фактических значений более 1">
                                    ${item.match_view ?? ''}
                                </div>
                                <div id="match_${item.mtr_id}_${item.ugtp_id}"
                                     class="invalid-feedback">
                                    ${item.match_message ?? ''}
                                </div>`
                    } else {
                        html += `<select class="form-select match ${isInvalid}"
                                        name="match[${item.mtr_id}][${item.ugtp_id}]"
                                        aria-describedby="match_${item.mtr_id}_${item.ugtp_id}">
                                    <option value="0"
                                        ${item.match == 0 ? 'selected' : ''}>
                                        Не соответствует
                                    </option>
                                    <option value="1"
                                        ${item.match == 1 ? 'selected' : ''}>
                                        Соответствует
                                    </option>
                                    <option value="2"
                                        ${item.match == 2 ? 'selected' : ''}>
                                        -
                                    </option>
                                    <option value="3"
                                        ${item.match == 3 ? 'selected' : ''}>
                                        Не нормируется
                                    </option>
                                </select>
                                <div id="match_${item.mtr_id}_${item.ugtp_id}"
                                     class="invalid-feedback">
                                    ${item.match_message ?? ''}
                                </div>`
                    }
                    html += `<div class="form-text">${item.match_text ?? ''}</div>`

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

                    if (item.in_field) {
                        html += `<i class="fa-regular fa-circle-check icon-big" title="${title}"></i>`
                    } else {
                        html += `<i class="fa-regular fa-circle-xmark icon-big" title="${title}></i>`
                    }

                    return html
                }
            },
            {
                data: 'ewq3',
                orderable: false,
                className: '',
                render: function (data, type, item) {
                    if (item.protocol.ID && item.protocol.INVALID == 0) {

                        return `<a href="/ulab/result/card_oati/${item.deal_id}?protocol_id=${item.protocol.ID}"
                                   class="text-decoration-none text-nowrap fw-bold">
                                    ${item.protocol.NUMBER ?? 'Номер не присвоен'}
                                </a>`
                    }
                    return ``
                }
            },
        ],
        columnDefs: [{ visible: false, targets: 0 }],
        drawCallback: function (settings) {
            let api = this.api();
            let rows = api.rows({ page: 'current' }).nodes();
            let last = null;
            let data = rows.data()
            let disableMs = ''

            api.column(0, { page: 'current' })
                .data()
                .each(function (group, i) {
                    if (last !== group) {
                        if (data[i].measurement.id === undefined) {
                            disableMs = 'disabled'
                        }
                        $(rows)
                            .eq(i)
                            .before(
                                `<tr class="bg-sky-blue">
                                    <td colspan="4">
                                        <div class="form-check">
                                            <input class="form-check-input method-check" type="checkbox" data-method_id="${data[i].method_id}" id="method_name_${data[i].method_id}">
                                            <label class="form-check-label" for="method_name_${data[i].method_id}">
                                                <strong>${group}</strong>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="--text-center">
                                        <a class="text-black" href="/ulab/gost/method/${data[i].method_id}">
                                            <i class="fa-solid fa-arrow-right-to-bracket icon-big"></i>
                                        </a>
                                        <!--<button type="button" class="btn bg-transparent border-0 mt-0 p-0 group-measurement-sheet ${disableMs}" data-method_id="${data[i].method_id}" title="Лист измерения">
                                            <i class="fa-solid fa-calculator font-size-35"></i>
                                            <i class="fa-solid fa-calculator font-size-35"></i>
                                        </button>-->
                                    </td>
                                    <td colspan="8"></td>
                                </tr>`
                            );

                        last = group;
                    }
                });
        },
        createdRow: function (row, data, dataIndex) {
            if (data.selected_protocol_id !== null && data.selected_protocol_id == data.gtp_protocol_id) {
                $(row).addClass("bg-pele-green")
            }
        },
        language: dataTablesSettings.language,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        pageLength: 100,
        order: [[2, "asc"]],
        dom: 'frt<"bottom"lip>',
        buttons: dataTablesSettings.buttons,
    })

    journalDataTable.columns().every(function () {
        $(this.header()).closest('thead').find('.search:eq(' + this.index() + ')').on('keyup change clear', function () {
            journalDataTable
                .column($(this).parent().index())
                .search(this.value)
                .draw();
        })
    })

    journalDataTable.on('draw', function () {
        $btnStart.addClass('disabled')
        $btnPause.addClass('disabled')
        $btnStop.addClass('disabled')
        $btnCreateProtocol.addClass('disabled')
        $btnMeasurementSheet.addClass('disabled')
        $body.find('.all-check').prop({ indeterminate: false, checked: false })
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


    $body.on('click', '.btn_start', function () {
        let probeIdList = $(".probe-check:checked").map(function () {
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
                        beforeOpen: function () {
                            $contentBlock.empty()

                            let html = ``

                            if (json.length === 0) {
                                $contentBlock.append(`<p>Нечего стартовать</p>`)
                            } else {
                                $.each(json, function (i, probe) {

                                    if (probe[0].rooms.length === 0) { return true }

                                    html += `<div class="text-center mb-2"><strong>${probe[0].material_name} | ${probe[0].cipher}</strong></div>`

                                    $.each(probe, function (j, method) {
                                        if (method.rooms.length === 0) { return true }

                                        let roomHtml = ``
                                        $.each(method.rooms, function (k, room) {
                                            let textLabelTemp = ``
                                            let textLabelWet = ``
                                            let textLabelPress = ``

                                            if (method.is_not_cond_temp == 1) {
                                                textLabelTemp = `Не нормируется`
                                            } else {
                                                textLabelTemp = `От ${method.cond_temp_1} до ${method.cond_temp_2}`
                                            }
                                            if (method.is_not_cond_wet == 1) {
                                                textLabelWet = `Не нормируется`
                                            } else {
                                                textLabelWet = `От ${method.cond_wet_1} до ${method.cond_wet_2}`
                                            }
                                            if (method.is_not_cond_pressure == 1) {
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
                                                                <input type="number" step="any" class="form-control input_temp" data-room_id="${room.room_id}" name="form[${method.ugtp_id}][${room.room_id}][temp]" value="${room.temp ?? ''}">
                                                                <div class="form-text">${textLabelTemp}</div>
                                                            </div>
                                                            <div class="col">
                                                                <input type="number" step="any" class="form-control input_wet" data-room_id="${room.room_id}" name="form[${method.ugtp_id}][${room.room_id}][wet]" value="${room.wet ?? ''}">
                                                                <div class="form-text">${textLabelWet}</div>
                                                            </div>
                                                            <div class="col">
                                                                <input type="number" step="any" class="form-control input_press" data-room_id="${room.room_id}" name="form[${method.ugtp_id}][${room.room_id}][pressure]" value="${room.pressure ?? ''}">
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
        let probeIdList = $(".probe-check:checked").map(function () {
            return $(this).val();
        }).get()

        if (probeIdList.length === 0) {
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

    $body.on('click', '.edit-description', function (e) {
        let ugtp = $(this).data('ugtp')

        $.ajax({
            method: 'POST',
            url: '/ulab/result/getUgtpDescription',
            data: {
                ugtp_id: ugtp,
            },
            success: function (data) {
                let dataArray = JSON.parse(data)

                $('#description_ugtp_id').val(ugtp)
                $('#description_text').val(dataArray['description'])

                $.magnificPopup.open({
                    items: {
                        src: '#descriptionModalForm',
                        type: 'inline',
                        fixedContentPos: false,
                    },
                    closeOnBgClick: false,
                    showCloseBtn: true
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

    /** Получить лист измерения */
    $body.on('click', '.measurement-sheet', function (e) {
        const btn = $(this)
        const $form = $('#measurementModalForm')
        const $contentBlock = $form.find('.measurement_content')

        let measurementId = $(this).data('measurement'),
            ugtpId = $(this).data('ugtp'),
            methodId = $(this).data('method');

        let measurementObject = []
        measurementObject.push({ ugtpId: ugtpId, measurement_id: measurementId, methodId: methodId })

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
                        beforeOpen: function () {
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

        let measurementObject = $journalMethods.find(".probe-check:checked").map(function () {
            let measurementId = $(this).data('measurement_id')
            if (measurementId === `undefined`) { return }
            let ugtpId = $(this).val()
            let methodId = $(this).data('method_id')

            return { ugtpId: ugtpId, measurement_id: measurementId, methodId: methodId }
        }).get()

        if (measurementObject.length === 0) { return false }

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
                        beforeOpen: function () {
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


    $body.on('click', '.btn_unbound_protocol', function () {
        let probeIdList = $(".probe-check:checked").map(function () {
            return $(this).val();
        }).get()

        $('.count-selected-probe').text(probeIdList.length)
        $('.probe-id-list').val(probeIdList)
    })


    $body.on('click', '.change-trials-date', function () {
        $('.date-trials').prop('readonly', !$(this).prop('checked')).toggleClass('bg-light-secondary')
    })

    $body.on('click', '.change-trials-conditions', function () {
        $('.condition_input').prop('readonly', !$(this).prop('checked')).toggleClass('bg-light-secondary')
    })

    /**
     * протокол недействителен
     */
    $body.on('change', '.protocol-is-invalid', function (e) {
        if (!confirm('Вы действительно хотите признать протокол недействительным? После признания протокола недействительным, данные нельзя будет восстановить')) {
            e.preventDefault();
            $(this).prop('checked', false);
            return false;
        }

        $(this).closest('form').submit();
    })

    /**
     * добавить оборудование
     */
    $body.on('change', '.equipment', function () {
        const $root = $(this).closest('.js-equipment-info');   // общий контейнер
        const $equipmentUsed = $root.find('.equipment-used');
        const $selected = $(this).find('option:selected');
        const selectedId = Number($(this).val());
        if (!selectedId) return;
        const selectedText = $selected.text();
        let ids = [];
        try {
            ids = JSON.parse($root.find('#equipmentIds').val() || '[]');
        } catch (e) { ids = []; }
        if (!ids.includes(selectedId)) {
            ids.push(selectedId);
            $root.find('#equipmentIds').val(JSON.stringify(ids));
        }
        // if (!$equipmentUsed.find(`option[value="${selectedId}"]`).length) {
        const opt = new Option(selectedText, selectedId, false, false);
        $equipmentUsed.append(opt);
        // }
        console.log(dealId);
        console.log(selectedId);
        $.ajax({
            method: 'POST',
            url: '/ulab/result/getProtocolInfoCertificateAjax',
            data: {
                deal_id: dealId,
                oborud_id: selectedId,
            },
            dataType: "json",
            success: function (data) {
                const container = $('#equipmentList');

                const src = data['protocol_equipment'] || {};
                // новая структура: объект { "261": {...}, "300": {...} }
                const equipmentList = Array.isArray(src) ? src : Object.values(src);

                for (const eq of equipmentList) {
                    const eqId = eq.b_o_id ?? eq.original_b_o_id;
                    if (container.find(`.eq-block[data-eq="${eqId}"]`).length) {
                        continue;
                    }
                    const titleParts = [
                        `${eq.TYPE_OBORUD ?? ''} ${eq.OBJECT ?? ''}`.trim(),
                        eq.REG_NUM ? `инв. номер ${eq.REG_NUM}` : null,
                    ].filter(Boolean);

                    const eqBlock = $(`
                            <div class="eq-block" data-eq="${eqId}" data-gost="${eq.g_id ?? ''}" style="display: none;">
                              <div class="eq-close" style="text-align: right; color: red; cursor: pointer;">×</div>
                              <div class="eq-title"><b>${titleParts.join(', ')}</b></div>
                              <div class="eq-certs"></div>
                              <hr>
                            </div>
                          `);

                    const certsWrap = eqBlock.find('.eq-certs');

                    // сертификаты теперь лежат массивом в eq.certificates
                    const certs = Array.isArray(eq.certificates) ? eq.certificates : [];

                    if (certs.length === 0) {
                        certsWrap.append(`<div class="text-muted">Сертификатов нет</div>`);
                    } else {
                        // защита от дублей по cert_id
                        const uniq = new Map();
                        for (const c of certs) {
                            if (c?.cert_id) uniq.set(String(c.cert_id), c);
                        }

                        for (const [certId, c] of uniq.entries()) {
                            certsWrap.append(`
                                    <label style="display:block; margin-left:18px;">
                                      <input type="checkbox"
                                             name="certs[]"
                                             value="${certId}"
                                             ${c?.in_protocol == 1 ? 'checked' : ''}>
                                      ${c?.cert_name ?? ''} ${c?.date_start ?? ''} ${c?.date_end ?? ''}
                                    </label>
                                  `);
                        }
                    }

                    container.append(eqBlock);
                }
            }
        });
        renderUsedEquipmentRemoveList($root);
    });

    /**
     * вернуть оборудование по умолчанию
     */
    $body.on('click', '.revert-default', function () {
        let protocolId = $('.selected_protocol_id').val()

        $.ajax({
            method: 'POST',
            url: '/ulab/result/revertDefaultAjax',
            data: {
                protocol_id: protocolId
            },
            dataType: 'json',
            success: function (data) {
                if (data.success || data.error) {
                    location.reload();
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
     * удалить оборудование
     */
    // $body.on('dblclick', '.equipment-used option', function () {
    //     let aldOborudGost = JSON.parse($('#equipmentIds').val());
    //     let iEquipmentUsed = aldOborudGost.indexOf(+this.value);
    //
    //     if (iEquipmentUsed !== -1) {
    //         aldOborudGost.splice(iEquipmentUsed, 1);
    //     } else {
    //         return false
    //     }
    //
    //     $('#equipmentIds').val(JSON.stringify(aldOborudGost));
    //     $('.equipment-used option').eq(iEquipmentUsed).remove()
    // })
    function renderUsedEquipmentRemoveList($root) {
        const $select = $root.find('.equipment-used');
        const $list = $root.find('.equipment-used-remove-list');

        if (!$list.length) return; // некуда рисовать

        $list.empty();

        $select.find('option').each(function () {
            const id = this.value;
            const text = $(this).text();
            const title = $(this).attr('title') || '';
            const cls = this.className || '';

            $list.append(`
          <div class="d-flex justify-content-between used-eq-item" data-id="${id}">
            <div class="me-2 ${cls}" title="${title}"></div>
            <div style="  display: flex;
  align-items: center;
  justify-content: center;">
                <button type="button" class="remove-used-eq" data-id="${id}"
                        style="border:0;background:transparent;color:#dc3545;    height: 22px;
    display: flex;
    justify-content: center;
    align-items: center;padding:0 6px;"
                        title="Удалить">&times;</button>
            </div>
          </div>
        `);
        });

        $list.toggle($select.find('option').length > 0);
    }
    $body.on('click', '.remove-used-eq', function (e) {
        e.preventDefault();

        const $root = $(this).closest('.js-equipment-info'); // ВАЖНО
        const id = Number($(this).data('id'));

        // 1) hidden JSON
        let arr = [];
        try {
            arr = JSON.parse($root.find('#equipmentIds').val() || '[]');
        } catch (e) { }

        arr = arr.filter(x => Number(x) !== id);
        $root.find('#equipmentIds').val(JSON.stringify(arr));

        // 2) select option
        $root.find('.equipment-used').find(`option[value="${id}"]`).remove();

        // 3) перерисовать список с крестиками
        renderUsedEquipmentRemoveList($root);
    });
    $body.on('dblclick', '.equipment-used', function () {
        var selectedValue = $(this).val();
        $('.eq-block').hide();
        //$('.equipment-used').css('width', '50%');
        $('.certs-wrapper').show();
        $('.eq-block[data-eq="' + selectedValue + '"]').show();
    });
    $body.on('click', '.equipment-used', function () {
        $('.remove-used-eq').css('background-color', 'transparent');
        var selectedId = $(this).val();
        var $targetButton = $('.remove-used-eq[data-id="' + selectedId + '"]');
        $targetButton.css('background-color', '#1967d2');
    });
    $body.on('click', '.eq-close', function () {
        $('.eq-block').hide();
        $('.equipment-used').css('width', '100%');
        $('.certs-wrapper').hide();
    });
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
        protocolInformation.find('.user-create-protocol').html('');
        protocolInformation.find('.user-norm-protocol').html('');
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
                console.log(data)

                let count = Object.keys(data).length

                if (count) {
                    // $('#additionalInformation').summernote({
                    //     lang: 'ru-RU',
                    //     height: 100,
                    //     toolbar: [
                    //         // Минимальный набор инструментов, без Picture, Video, Toolbar
                    //         ['para', ['ul']],
                    //     ]
                    // });

                    let verify = protocolInformation.find('.verify'),
                        equipmentUsed = protocolInformation.find('.equipment-used'),
                        equipment = protocolInformation.find('#equipment')

                    $.each(data['assigned'], function (key, value) {
                        verify.append(`<option value="${value['user_id']}">${value['user_name']}</option>`);
                    });
                    verify.append(`<option value="263">Алена Ширлянова</option>`);
                    verify.append(`<option value="266">Евгений Панарин</option>`);
                    verify.append(`<option value="272">Сергей Трусов</option>`);

                    let userCreateProtocol = protocolInformation.find('.user-create-protocol')
                    userCreateProtocol.append(`<option value=""></option>`);
                    $.each(data['create_users'], function (key, value) {
                        userCreateProtocol.append(`<option value="${value['ID']}">${value['user_name']}</option>`);
                    });
                    userCreateProtocol.append(`<option value="291">Александр Минченков</option>`);
                    userCreateProtocol.append(`<option value="274">Михаил Глекин</option>`);
                    userCreateProtocol.append(`<option value="275">Раиль Ижбулатов</option>`);

                    let userNormProtocol = protocolInformation.find('.user-norm-protocol')
                    userNormProtocol.append(`<option value=""></option>`);
                    $.each(data['norm_users'], function (key, value) {
                        userNormProtocol.append(`<option value="${value['ID']}">${value['user_name']}</option>`);
                    });
                    userNormProtocol.append(`<option value="9">Алия Габдрахманова</option>`);
                    userNormProtocol.append(`<option value="10">Елена Апанович</option>`);
                    userNormProtocol.append(`<option value="222">Ирина Алексеева</option>`);

                    equipmentUsed.empty();
                    const ids = [];

                    $.each(data['protocol_equipment'], function (key, value) {
                        const text = `${value['TYPE_OBORUD']} ${value['OBJECT']}, инв. номер ${value['REG_NUM']}`;
                        const opt = new Option(text, value['b_o_id'], true, true);

                        $(opt).addClass(value['bg_color']).attr('title', text);
                        equipmentUsed.append(opt);

                        ids.push(+value['b_o_id']);
                    });

                    $('#equipmentIds').val(JSON.stringify(ids));

                    const $root = protocolInformation.find('.js-equipment-info'); // добавьте этот класс на общий блок
                    renderUsedEquipmentRemoveList($root);

                    $.each(data['oboruds'], function (key, value) {
                        equipment.append(
                            `<option value="${value['o_id']}" data-gost="${value['g_id']}">
                                ${value['TYPE_OBORUD']} ${value['OBJECT']}
                                , инв. номер ${value['REG_NUM']} ${value['reg_doc']}, ${value['clause']}
                            </option>`
                        );
                    });

                    const container = $('#equipmentList');
                    container.empty();

                    const src = data['protocol_equipment'] || {};
                    // новая структура: объект { "261": {...}, "300": {...} }
                    const equipmentList = Array.isArray(src) ? src : Object.values(src);

                    for (const eq of equipmentList) {
                        const eqId = eq.b_o_id ?? eq.original_b_o_id;

                        const titleParts = [
                            `${eq.TYPE_OBORUD ?? ''} ${eq.OBJECT ?? ''}`.trim(),
                            eq.REG_NUM ? `инв. номер ${eq.REG_NUM}` : null,
                        ].filter(Boolean);

                        const eqBlock = $(`
                            <div class="eq-block" data-eq="${eqId}" data-gost="${eq.g_id ?? ''}" style="display: none;">
                              <div class="eq-close" style="text-align: right; color: red; cursor: pointer;">×</div>
                              <div class="eq-title"><b>${titleParts.join(', ')}</b></div>
                              <div class="eq-certs"></div>
                              <hr>
                            </div>
                          `);

                        const certsWrap = eqBlock.find('.eq-certs');

                        // сертификаты теперь лежат массивом в eq.certificates
                        const certs = Array.isArray(eq.certificates) ? eq.certificates : [];

                        if (certs.length === 0) {
                            certsWrap.append(`<div class="text-muted">Сертификатов нет</div>`);
                        } else {
                            // защита от дублей по cert_id
                            const uniq = new Map();
                            for (const c of certs) {
                                if (c?.cert_id) uniq.set(String(c.cert_id), c);
                            }

                            for (const [certId, c] of uniq.entries()) {
                                certsWrap.append(`
                                    <label style="display:block; margin-left:18px;">
                                      <input type="checkbox"
                                             name="certs[]"
                                             value="${certId}"
                                             ${c?.in_protocol == 1 ? 'checked' : ''}>
                                      ${c?.cert_name ?? ''} ${c?.date_start ?? ''} ${c?.date_end ?? ''}
                                    </label>
                                  `);
                            }
                        }

                        container.append(eqBlock);
                    }
                    let protocolNum = data['protocol']['NUMBER'] ? data['protocol']['NUMBER'] : 'Номер не присвоен';

                    protocolInformation.find('.title').text(`Информация по протоколу № ${protocolNum}`);
                    protocolInformation.find('input[name="protocol_id"]').val(protocolId);

                    // Общая информация
                    let protocolType = data['protocol']['PROTOCOL_TYPE'] ? data['protocol']['PROTOCOL_TYPE'] : 0;
                    protocolInformation.find('.protocol-type').val(protocolType);
                    let protocolTemplate = data['protocol']['id_template'] ? data['protocol']['id_template'] : 0;
                    protocolInformation.find('.protocol-template').val(protocolTemplate);
                    protocolInformation.find('.verify').val(data['protocol']['verify']);
                    if (protocolType == 0 || protocolType == 43) {
                        protocolInformation.find('.norm-control').show()
                    }
                    if (protocolType == 57) {
                        protocolInformation.find('.osk1').show();
                        protocolInformation.find('.osk-1-name').val(data['protocol']['osk_1_name']);
                        protocolInformation.find('.osk-1-norm').val(data['protocol']['osk_1_norm']);
                        var valuepl = data['protocol']['osk_1_place'] ? data['protocol']['osk_1_place'] : 'г. Челябинск, Промзона, ул. Монтажников 11';
                        protocolInformation.find('.osk-1-place').val(valuepl);
                    }
                    if (protocolType == 58) {
                        console.log(data)
                        protocolInformation.find('.osk2').show();
                        var value2norm = data['protocol']['osk_2_norm'] ? data['protocol']['osk_2_norm'] : '49.2';
                        protocolInformation.find('.osk-2-norm').val(value2norm);
                        var value2fact = data['object_data']['ACTUAL_VALUE'];
                        protocolInformation.find('.osk-2-fact').val(value2fact);
                    }
                    protocolInformation.find('.user-create-protocol').val(data['protocol']['user_create_protocol']);
                    protocolInformation.find('.user-norm-protocol').val(data['protocol']['user_norm_protocol']);
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
                    console.log(['protocol']['CHANGE_TRIALS_DATE']);
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

                    console.log(data)

                    //Данные объекта испытаний
                    if (protocolType == 58) {
                        let text = 'Бетонные образцы-кубы с размером ребра 100 мм в количестве двух штук.\nПроектный класс бетона по прочности на сжатие – В45.\n' +
                            'Требуемая прочность бетона на сжатие в проектном возрасте – ... МПа.\nДокументы о качестве бетонной смеси, партии № от ... ООО "БЕТОТЕК БЕТОН"\n' +
                            'Возраст образцов-кубов – 7/28 сутки. Акт отбора проб (образцов) б/н от ...';
                        text = String(text).replace(/\\n/g, "\n"); // важно: \\n -> \n
                        text = data['object_data']['DESCRIPTION'] ? data['object_data']['DESCRIPTION'] : text;
                        protocolInformation.find('.object-description').val(text);
                    } else {
                        protocolInformation.find('.object-description').val(data['object_data']['DESCRIPTION']);
                    }
                    if (protocolType == 57) {
                        let text = data['object_data']['OBJECT'] ? data['object_data']['OBJECT'] : 'Производство строительных Ж/Б изделий';
                        protocolInformation.find('.object').val(text);
                    } else {
                        protocolInformation.find('.object').val(data['object_data']['OBJECT']);
                    }
                    protocolInformation.find('.place-probe').val(data['object_data']['PLACE_PROBE']);
                    protocolInformation.find('.date-probe').val(data['object_data']['DATE_PROBE']);
                    protocolInformation.find('.additional-information').val(data['protocol']['DOP_INFO']);
                    protocolInformation.find('.additional-information2').val(data['protocol']['DOP_INFO2']);
                    protocolInformation.find('.protocol-outside-lis').prop('checked', +data['protocol']['PROTOCOL_OUTSIDE_LIS']);
                    protocolInformation.find('.automatic-comments').prop('checked', +data['protocol']['automatic_comments']);
                    protocolInformation.find('.determined-characteristics').prop('checked', +data['protocol']['determined_characteristics']);
                    attestatInProtocol.prop('checked', +data['protocol']['ATTESTAT_IN_PROTOCOL']);
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
                    placeholder: '',
                    allowClear: true,
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

    $body.on('change', '.protocol-type', function () {
        let selectedValue = $(this).val();
        if (selectedValue == '0' || selectedValue == '43') {
            $('.norm-control').show()
        } else {
            $('.norm-control').hide()
        }
        if (selectedValue == '57') {
            $('.osk1').show()
            $('.object').val('Производство строительных Ж/Б изделий')
            $('.osk-1-place').val('г. Челябинск, Промзона, ул. Монтажников 11.')
        } else {
            $('.osk1').hide()
        }
        if (selectedValue == '58') {
            $('.osk2').show()
            let text = 'Бетонные образцы-кубы с размером ребра 100 мм в количестве двух штук.\nПроектный класс бетона по прочности на сжатие – В45.\n' +
                'Требуемая прочность бетона на сжатие в проектном возрасте – ... МПа.\nДокументы о качестве бетонной смеси, партии № от ... ООО "БЕТОТЕК БЕТОН"\n' +
                'Возраст образцов-кубов – 7/28 сутки. Акт отбора проб (образцов) б/н от ...';
            text = String(text).replace(/\\n/g, "\n"); // важно: \\n -> \n
            $('.object-description').val(text);
            $('.osk-2-norm').val('49.2');
        } else {
            $('.osk2').hide()
        }
        if (selectedValue == '57' || selectedValue == '58') {
            $('.osk12').show()
        } else {
            $('.osk12').hide()
        }
    })

    $(document).on('change', 'input[name="placeProbePreset"]', function () {
        let text = $(this).data('full') || '';
        text = String(text).replace(/\\n/g, "\n"); // важно: \\n -> \n
        $('#placeProbe').val(text);
    });

    $(document).on('change', 'input[name="objectProbePreset"]', function () {
        let text = $(this).data('full') || '';
        text = String(text).replace(/\\n/g, "\n"); // важно: \\n -> \n
        $('#objectDescription').val(text);
    });
    $(document).on('change', 'input[name="descObjectProbePreset"]', function () {
        let text = $(this).data('full') || '';
        text = String(text).replace(/\\n/g, "\n"); // важно: \\n -> \n
        $('#objectDescription').val(text);
    });
    $(document).on('change', 'input[name="dayObjectProbePreset"]', function () {
        const full = String($(this).data('full') ?? '');
        const $desc = $('#objectDescription');
        let template = $desc.data('template');
        if (template == null) {
            template = $desc.val() || '';
            $desc.data('template', template);
        }
        const result = template.replace(/7\/28/g, full);
        $desc.val(result);
    });
    $(document).on('change', 'input[name="OSK1NamePreset"]', function () {
        const text = $(this).data('full') || '';
        $('#osk1Name').val(text);
    });
    $(document).on('change', 'input[name="OSK1NormPreset"]', function () {
        const text = $(this).data('full') || '';
        $('#osk1Norm').val(text);
    });
    // если нужно применить выбранное при загрузке:
    $('input[name="placeProbePreset"]:checked').trigger('change');
    $('input[name="objectProbePreset"]:checked').trigger('change');
    $('input[name="descObjectProbePreset"]:checked').trigger('change');
    $('input[name="dayObjectProbePreset"]:checked').trigger('change');
    $('input[name="OSK1NamePreset"]:checked').trigger('change');
    $('input[name="OSK1NormPreset"]:checked').trigger('change');
})


function setFilterFromCookie($select, keyCookie) {
    let value = $.cookie(keyCookie)
    if (value !== '' && value !== undefined) {
        $.each(value.split(','), function (i, e) {
            $select.find(`option[value='${e}']`).prop("selected", true)
        })
        $select.trigger('change')
    }
}
