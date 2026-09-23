let methodList = null
let conditionList = null
let quarryList = null

let dnsMulti = null
let $strTotal = null
let $strTotalDns = null
let $inputTotal = null
let $inputDiscount = null

let methodInfo = null

// Все пробы, представленные на странице
let methodsAlreadySet = false;
const tz_id = $('#tz_id').val();

$.ajax({
    method: 'POST',
    url: '/ulab/requirement/getMethodsAjax',
    dataType: 'json',
    success: function (data) {
        methodList = data
    }
})

$.ajax({
    method: 'POST',
    url: '/ulab/requirement/getTechCondListAjax',
    data: {tz_id: tz_id},
    dataType: 'json',
    success: function (data) {
        conditionList = data
    }
})

$.ajax({
    method: 'POST',
    url: '/ulab/requirement/getQuarryAjax',
    dataType: 'json',
    success: function (data) {
        quarryList = data
    }
})

/**
 * Обновляет select#filter-group на странице, заполняя его методиками из массива
 * @param {Array} probes - массив объектов с пробами (например, [{id: 1, name: '...', ...}, ...])
 */
function updateMethodFilterSelect(probes) {
    const $select = $('#filter-group');
    const prevValue = $select.val();

    // Очищаем все опции, кроме первой ("Все методики")
    $select.find('option:not(:first)').remove();

    // Добавляем новые опции
    if (Array.isArray(probes) && probes.length > 0) {
        probes.forEach(function (probe) {
            $select.append(
                $('<option>', {
                    value: probe.group,
                    text: probe.material_group_name ?? '',
                })
            );
        });
    }

    // Восстанавливаем выбранное значение, если оно есть в новом списке
    if ($select.find('option[value="' + prevValue + '"]').length) {
        $select.val(prevValue);
    } else {
        $select.val('');
    }

    // Обновляем select2
    if ($select.hasClass('select2')) {
        $select.trigger('change.select2');
    } else {
        $select.trigger('change');
    }
}

/**
 * Данный метод получает все группы, задействованные в пробах, которые представлены на странице.
 * Метод используется для формирования фильтра.
 *
 * @param {Array} probeArray - массив объектов проб (например, [{id: 1, ...}, ...])
 */
function setGroups(probeArray) {
    // Методы уже установлены
    if (methodsAlreadySet) {
        return;
    }

    updateMethodFilterSelect(probeArray);
    methodsAlreadySet = true;
}

function getHtmlCharForm(dealTypeId = '', userGroups = []) {
    let optionCondition = getHtmlOptionsTu(conditionList);
    let priceReadonly = ''

    if (dealTypeId === 'COMPLEX') {
        priceReadonly = ''
    }

    if (dealTypeId === 'SALE' && !userGroups.includes('23')) {
        priceReadonly = 'readonly'
    }


    return `<div class="row justify-content-between method-block mb-2">
                <div class="col">
                    <label class="form-label mb-1">НД</label>
                    <input type = "text"/>
                </div>
            </div>
            <div class="row justify-content-between method-block mb-2">
                <div class="col">
                    <label class="form-label mb-1">Исполнитель</label>
                    <select class="form-control group-user-select select2" name="method[assigned_id]">
                        <option value="">Исполнитель</option>
                    </select>
                </div>
                <div class="col-4">
                    <label class="form-label mb-1">Цена</label>
                    <div class="input-group">
                        <input class="form-control group-price-input" name="method[price]" type="number" min="0" step="0.01" value="0" ${priceReadonly}>
                        <span class="input-group-text">₽</span>
                    </div>
                </div>
            </div>`
}

/**
 * Проверяет, остались ли на странице пробы с каждой из методик, и обновляет фильтр select#filter-group
 */
function refreshMethodFilterAfterProbeDelete() {
    // Получаем все данные, которые сейчас отображаются в таблице
    let allProbes = $('#journal_material_2').DataTable().rows({search: 'applied'}).data().toArray();

    // Собираем id всех проб
    let allProbeIds = allProbes.map(item => item.id);

    if (allProbeIds.length === 0) {
        updateMethodFilterSelect([]);
        return;
    }

    // Запрашиваем актуальный список методик для этих проб
    $.ajax({
        method: 'GET',
        url: '/ulab/requirement/getMethodsByProbeListAjax',
        data: {probe_id_list: allProbeIds},
        dataType: 'json',
        success: function (data) {
            updateMethodFilterSelect(data);
        },
        error: function () {
            updateMethodFilterSelect([]);
        }
    });
}

function loadCharBlock() {
    const $charBlock = $('.method-characteristics-block');
    const $charForm = $charBlock.find('.method-characteristics-form');

    $charBlock.removeClass('d-none');
    const dealTypeId = $('#deal_type_id').val();
    const userGroups = JSON.parse($('#user_groups').val());

    $charForm.html(getHtmlCharForm(dealTypeId, userGroups));

    $charForm.find('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%' // или width: '100%'
    });

    $charForm.find('.method-block:last-child').find('.select2').select2({
        theme: 'bootstrap-5',
        templateResult: formatState,
        templateSelection: formatState,
    })
}

$('#form_requirement').on('submit', function (e) {
    e.preventDefault();
    var $form = $(this);
    if ($('#filter-group').val() !== '') {
        let probeIdList = $(".filter-probe-id:checked").map(function () {
            return $(this).val();
        }).get();
        console.log(probeIdList); // например ['125357', '125358']

        // Для каждого probeId ищем соответствующую группу по data-id
        probeIdList.forEach(function (probeId) {
            // Ищем элемент группы, у которого data-id совпадает с probeId
            let $group = $(`.select_group2[data-id="${probeId}"]`);

            if ($group.length) {
                const id = $group.data('id');
                const name = $('#filter-group option:selected').text();
                const materialId = $group.data('material-id');  // material_id группы

                console.log(`Найдена группа для пробы ${probeId}:`, {
                    id: id,
                    name: name,
                    materialId: materialId
                });

                // Добавляем скрытые поля для этой группы
                $form.append($('<input>', {
                    type: 'hidden',
                    name: `groups[${id}][name]`,
                    value: name
                }));

                $form.append($('<input>', {
                    type: 'hidden',
                    name: `groups[${id}][materialid]`,
                    value: materialId
                }));
            } else {
                console.log(`Группа с data-id ${probeId} не найдена`);
            }
        });
    } else {
        $('.name_for_protocol2').each(function () {
            var $this = $(this);
            var protocolName = $this.attr('id');
            var value = $this.val();
            $form.append($('<input>', {
                type: 'hidden',
                name: 'probes[' + protocolName + ']',
                value: value
            }));
        });
        $('.select_group2').each(function () {
            const $el = $(this);
            const id = $el.data('id');

            const name = $el.val();                      // "тест"
            const materialId = $el.data('material-id');  // item.material_id

            $form.append($('<input>', {
                type: 'hidden',
                name: `groups[${id}][name]`,
                value: name
            }));

            $form.append($('<input>', {
                type: 'hidden',
                name: `groups[${id}][materialid]`,
                value: materialId
            }));
        });
    }
    $form.off('submit').submit();
});

function formatState(state) {
    const option = $(state.element)
    const color = option.data("color")

    if (!color) {
        return state.text
    }

    return $(`<span style="color: ${color}">${state.text}</span>`)
}

$(function ($) {
    // подгружаем методики для проб в селект формы замены
    $('.btn-replace-method').on('click', function () {
        let probeIdList = $(".filter-probe-id:checked").map(function () {
            return $(this).val();
        }).get()

        probeIdList.forEach(function (id) {
            $('#probe-ids-container').append(
                $('<input>', {
                    type: 'hidden',
                    name: 'probe_id[]',
                    value: id
                })
            );
        });

        // Делаем AJAX-запрос на сервер
        $.ajax({
            method: 'GET',
            url: '/ulab/requirement/getMethodsByProbeListAjax',
            data: {probe_id_list: probeIdList}, // Передаём массив id проб
            dataType: 'json',
            success: function (data) {
                // Очищаем селект перед добавлением новых опций
                var $select = $('#old-method');
                $select.empty();

                console.log(data);

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function (item) {
                        var $option = $('<option>', {
                            value: item.id,
                            text: item.name
                        });
                        $select.append($option);
                    });
                } else {
                    // Если данных нет, используем опцию-заглушку
                    $select.append($('<option>', {
                        value: '',
                        text: 'Нет доступных методик'
                    }));
                }
            },
            error: function (xhr, status, error) {
                console.error('Ошибка при получении методов:', error);
            }
        });
    })

    $('.btn-delete-method').on('click', function () {
        let probeIdList = $(".filter-probe-id:checked").map(function () {
            return $(this).val();
        }).get()

        probeIdList.forEach(function (id) {
            $('#delete-ids-container').append(
                $('<input>', {
                    type: 'hidden',
                    name: 'probe_id[]',
                    value: id
                })
            );
        });

        // Делаем AJAX-запрос на сервер
        $.ajax({
            method: 'GET',
            url: '/ulab/requirement/getMethodsByProbeListAjax',
            data: {probe_id_list: probeIdList}, // Передаём массив id проб
            dataType: 'json',
            success: function (data) {
                // Очищаем селект перед добавлением новых опций
                var $select = $('#method-to-delete');
                $select.empty();

                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(function (item) {
                        var $option = $('<option>', {
                            value: item.id,
                            text: item.name
                        });
                        $select.append($option);
                    });
                } else {
                    // Если данных нет, используем опцию-заглушку
                    $select.append($('<option>', {
                        value: '',
                        text: 'Нет доступных методик'
                    }));
                }
            },
            error: function (xhr, status, error) {
                console.error('Ошибка при получении методов:', error);
            }
        });
    })

    // При выборе методики — показать форму характеристик
    $('#new-method').on('change', function () {
        const $charBlock = $('.method-characteristics-block');
        const $charForm = $charBlock.find('.method-characteristics-form');
        const methodId = $(this).val();

        if (methodId && methodId !== "") {
            loadCharBlock();

            const id = $(this).val();
            const $parent = $('.method-characteristics-block');

            $.ajax({
                method: 'POST',
                async: false,
                data: {
                    id: id
                },
                url: '/ulab/requirement/getMethodDataAjax',
                dataType: 'json',
                success: function (data) {
                    $parent.find('.group-price-input').val(parseFloat(data.price))

                    let options = `<option value="">Исполнитель</option>`
                    $.each(data.assigned, function (i, item) {
                        options += `<option value="${item.user_id}">${item.short_name}</option>`
                    })

                    $parent.find('.group-user-select').html(options)
                }
            })
        } else {
            $charBlock.addClass('d-none');
            $charForm.html('');
        }
    });

    const $body = $('body')

    const $journalMaterial = $('#journal_material_2')

    const dealId = $('#deal_id').val()

    $strTotal = $('#str_total')
    $strTotalDns = $('#str_total_nds')
    $inputTotal = $('#price-total')
    $inputDiscount = $('#price_discount')
    dnsMulti = parseFloat($strTotalDns.data('nds-multi'))

    $body.on('change', '.material-check', function () {
        let materialId = $(this).data('material_id')
        $body.find(`.material-id-${materialId}`).prop("checked", $(this).prop("checked"))

        toggleProbe()
    })

    $body.on('change', '.nds', function () {
        if ($(this).prop('checked')) {
            $('.is_nds_block').removeClass('d-none').addClass('d-flex')
        } else {
            $('.is_nds_block').removeClass('d-flex').addClass('d-none')
        }
    })

    // Добавляем обработчик события для select НДС
    $body.on('change', '.nds-select', function () {
        const ndsValue = $(this).val();
        updateTotalWithNds(ndsValue);
    });

    $(document).ready(() => {
        updateTotalWithNds();
    });

    $body.on('change', '.nds-percent-select, .nds', function () {
        updateTotalWithNds();
    });

    // Функция для обновления суммы с НДС
    function updateTotalWithNds() {
        const isNdsChecked = $('.nds').prop('checked');
        const ndsPercent = $('.nds-percent-select').val();

        // Берем базовую цену БЕЗ НДС
        const basePriceWithoutNds = parseFloat($('#base-price-without-nds').val());

        let ndsMultiplier = 0;
        let isNds = '0';
        let isNds7 = '0';
        let displayPrice = basePriceWithoutNds;
        let ndsAmount = 0;
        console.log(isNdsChecked);
        if (isNdsChecked) {
            switch (ndsPercent) {
                case '5':
                    ndsMultiplier = 0.05;
                    isNds = '1';
                    isNds7 = '0';
                    break;
                case '7':
                    ndsMultiplier = 0.07;
                    isNds = '1';
                    isNds7 = '1';
                    break;
            }
            ndsAmount = basePriceWithoutNds * ndsMultiplier;
            displayPrice = basePriceWithoutNds + ndsAmount;
            $('.is_nds_block').removeClass('d-none').addClass('d-flex');
        } else {
            // При выключении НДС показываем цену без НДС
            displayPrice = basePriceWithoutNds;
            ndsAmount = 0;
            $('.is_nds_block').removeClass('d-flex').addClass('d-none');
        }

        // Обновляем отображение итоговой цены
        //$strTotal.text(displayPrice.toFixed(2) + ' руб.');
        $strTotalDns.text(round(ndsAmount, 2));

        // Сохраняем итоговую цену (с НДС или без) в основное поле для сохранения в БД
        //$('#price-total').val(displayPrice.toFixed(2));
        //$('#price_discount').val(displayPrice.toFixed(2));

        // Обновляем скрытые поля
        $('input[name="tz[is_nds]"]').val(isNds);
        $('input[name="tz[is_nds_7]"]').val(isNds7);
    }

    $body.on('change', '.probe-check', function () {
        let materialId = $(this).data('material_id')

        let parent = $body.find(`#material_name_${materialId}`),
            all = true,
            curState = $(this).prop("checked")

        $body.find(`.material-id-${materialId}`).each(function () {
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

        toggleProbe()
    })

    let stickyEl2 = new Sticksy('.js-sticky-widget2', {topSpacing: 20, listen: true,})
    stickyEl2.onStateChanged = function (state) {
        if (state === 'fixed') {
            stickyEl2.nodeRef.classList.add('widget--sticky')
        } else {
            stickyEl2.nodeRef.classList.remove('widget--sticky')
        }
    }

    $('.select2').select2({
        theme: 'bootstrap-5',
        templateResult: formatState,
        templateSelection: formatState,
    })

    $('#journal_material_2 tbody').on('click', 'td.act-details-control-2', function () {
        $(this).closest('tr').find('td.act-details-control').trigger('click')
    })

    if ($('#inlineRadio33').prop('checked')) {
        $('#inlineRadio50').prop('checked', false);
        $('#inlineRadio50').prop('disabled', true);
        $('#inlineRadio51').prop('checked', false);
        $('#inlineRadio51').prop('disabled', true);
    } else {
        $('#inlineRadio50').prop('disabled', false);
        $('#inlineRadio51').prop('disabled', false);
    }
    $('#inlineRadio31, #inlineRadio32, #inlineRadio33').change(function () {
        if ($('#inlineRadio33').prop('checked')) {
            $('#inlineRadio50').prop('checked', false);
            $('#inlineRadio50').prop('disabled', true);
            $('#inlineRadio51').prop('checked', false);
            $('#inlineRadio51').prop('disabled', true);
        } else {
            $('#inlineRadio50').prop('disabled', false);
            $('#inlineRadio51').prop('disabled', false);
        }
    });

    $('#journal_material_2 tbody').on('click', 'td.act-details-control', function () {
        let tr = $(this).closest('tr')
        let row = journalDataTable.row(tr)

        if (row.child.isShown()) {
            let table = $("table", row.child())
            table.DataTable().destroy()
            table.detach()

            // Эта строка уже открыта - закрываем
            row.child.hide()
            tr.removeClass('shown')

            $(this).html('<i class="fa-solid fa-chevron-right icon-big"></i>')
        } else {
            // Открыть эту строку
            createChild(row)
            tr.addClass('shown')

            $(this).html('<i class="fa-solid fa-chevron-down opacity-30 icon-big"></i>')
        }
    })

    let journalDataTable = $journalMaterial.DataTable({
        bAutoWidth: false,
        autoWidth: false,
        fixedColumns: false,
        processing: true,
        serverSide: true,
        bSortCellsTop: true,
        fixedHeader: false,
        colReorder: true,
        responsive: true,
        stateSave: true,
        stateDuration: -1,
        ajax: {
            type: 'POST',
            data: function (d) {
                d.deal_id = dealId
                d.material_id = $('#filter-material option:selected').val()
                d.cipher = $('#filter-cipher').val()
                d.group_id = $('#filter-group').val(); // <-- Добавлен новый фильтр по методике
            },
            url: '/ulab/requirement/getMaterialProbeJournalAjax/',
            dataSrc: function (json) {
                setGroups(json.data);

                console.log(json.data)

                return json.data
            }
        },
        columns: [
            {
                data: 'material_name',
                orderable: false,
                render: function (data, type, item) {
                    return `
                    <div class="form-check">
                        <input class="form-check-input material-check" type="checkbox" data-material_id="${item['material_id']}" id="material_name_${item['material_id']}">
                        <label class="form-check-label" for="material_name_${item['material_id']}">
                            ${item['material_name']}
                        </label>
                    </div>`
                }
            },
            {
                data: 'ewq',
                orderable: false,
                className: 'act-details-control cursor-pointer',
                render: function (data, type, item) {
                    return `<i class="fa-solid fa-chevron-right icon-big"></i>`
                }
            },
            {
                data: 'cipher',
                orderable: false,
                className: 'ps-4',
                render: function (data, type, item) {
                    let str = ``
                    if (item.child_deal_id > 0) {
                        str = `(проба перенесена в дочернюю <a target="_blank" href="/ulab/request/card/${item.child_deal_id}">заявку</a>)`
                    }

                    return `
                    <div class="form-check">
                        <input class="form-check-input filter-method filter-probe-id probe-check material-id-${item['material_id']}" data-material_id="${item['material_id']}" type="checkbox" value="${item['id']}" id="proba_name_${item['id']}">
                        <label class="form-check-label" for="proba_name_${item['id']}">
                            ${item['cipher']} ${str}
                        </label>
                    </div>`
                }
            },
            {
                data: 'material_group_name',
                className: 'act-details-control-2',
                render: $.fn.dataTable.render.ellipsis(30, true),
                orderable: false,
            },
            {
                data: 'name_for_protocol',
                className: 'act-details-control-2',
                orderable: false,
                render: function (data, type, item) {
                    const escapeAttr = (v) => String(v ?? '')
                        .replace(/&/g, '&amp;')
                        .replace(/"/g, '&quot;')
                        .replace(/'/g, '&#39;')
                        .replace(/</g, '&lt;')
                        .replace(/>/g, '&gt;');

                    return `
    <input type="text"
           name="form[name_for_protocol2]"
           class="form-control name_for_protocol2"
           id="${item.id}"
           value="${escapeAttr(data)}">
`;
                }
            },
            {
                data: 'count_methods',
                width: '40px',
                className: 'text-center',
                orderable: false,
                render: function (data, type, item) {
                    return `<span class="count_methods_probe_${item.id}">${item.count_methods}</span>`
                }
            },
            {
                data: 'edit',
                width: '70px',
                orderable: false,
                className: 'text-end',
                render: function (data, type, item) {
                    return `<a class="btn btn-primary btn-sm popup-edit-probe-form me-2 btn-square-sm" title="Редактировать"><i class="fa-solid fa-pencil"></i></a>
                            <a class="btn btn-success btn-sm copy_probe btn-square-sm" title="Скопировать" data-umtr_id="${item.id}"><i class="fa-regular fa-copy icon-fix"></i></a>`
                }
            },
        ],
        columnDefs: [{visible: false, targets: 0}],
        drawCallback: function (settings) {
            let api = this.api();
            let rows = api.rows({page: 'current'}).nodes();
            let last = null;
            let data = rows.data()

            api.column(0, {page: 'current'})
                .data()
                .each(function (group, i) {
                    if (last !== group) {
                        $(rows)
                            .eq(i)
                            .before(
                                `<tr>
                                    <td></td>
                                    <td colspan="4">
                                        <div class="form-check">
                                            <input class="form-check-input material-check" type="checkbox" data-material_id="${data[i].material_id}" id="material_name_${data[i].material_id}">
                                            <label class="form-check-label" for="material_name_${data[i].material_id}">
                                                <strong>${group}</strong>
                                            </label>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <a class="btn btn-primary btn-sm popup-edit-material-form" data-material_id="${data[i].material_id}">
                                            <i class="fa-solid fa-gear"></i>
                                        </a>
                                    </td>
                                </tr>`
                            );

                        last = group;
                    }
                });
        },
        createdRow: function (row, data, dataIndex) {
            // $(row).addClass("bg-pele-green")
        },
        language: dataTablesSettings.language,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        pageLength: 25,
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

    $('.filter').on('input', function () {
        journalDataTable.ajax.reload()
        journalDataTable.draw()

        let $btnGroupEdit = $('.btn-group-edit')
        let $btnAddMethods = $('.btn-add-methods')
        let $btnReplaceMethod = $('.btn-replace-method')
        let $btnDeleteMethods = $('.btn-delete-method')
        let $btnImportProbes = $('.btn-import-probes')

        $btnGroupEdit.addClass('disabled')
        $btnAddMethods.addClass('disabled')
        $btnReplaceMethod.addClass('disabled')
        $btnDeleteMethods.addClass('disabled')
        $btnImportProbes.addClass('disabled')
    })


    journalDataTable.on('click', '.copy_probe', function (e) {
        const $btn = $(this)
        const umtrId = $btn.data('umtr_id')

        $('.count_umtr_id').val(umtrId)

        $.magnificPopup.open({
            items: {
                src: '#copy-count-modal-form'
            },
            type: 'inline',
            closeBtnInside: true,
            fixedContentPos: false,
            closeOnBgClick: false,
        })

        // $btn.find('i').removeClass().addClass('fa-solid fa-arrows-rotate spinner-animation')
        // $btn.addClass('disable')
        //
        // $.ajax({
        //     url: "/ulab/requirement/copyProbeAjax/",
        //     method: "POST",
        //     cache: false,
        //     async: false,
        //     dataType: "json",
        //     data: {
        //         umtr_id: umtrId,
        //     },
        //     success: function (json) {
        //         journalDataTable.ajax.reload()
        //         journalDataTable.draw()
        //
        //         $strTotal.text(json.price_discount + ' руб.')
        //         $strTotalDns.text(round(parseFloat(json.price_discount) * dnsMulti, 2))
        //         $inputTotal.val(json.price)
        //         $inputDiscount.val(json.price_discount)
        //     }
        // })


    })


    // применить скидку
    $body.on('click', '.discount-apply', function () {
        let totalPrice = parseFloat($('#price-total').val());
        let discountVal = parseFloat($('.discount-input').val());
        let discountType = $('.discount-type').val();

        let discountPrice = 0;

        if (discountType == 'percent') {
            if (discountVal < 0) {
                discountVal = 0;
            } else if (discountVal > 100) {
                discountVal = 100;
            }
            discountPrice = totalPrice - totalPrice * discountVal / 100;
        } else if (discountType == 'rub' && discountVal >= 0) {
            discountPrice = totalPrice - discountVal;
        }

        if (discountPrice < 0) {
            discountPrice = 0;
        }

        // Сохраняем базовую цену БЕЗ НДС в скрытое поле
        $('#base-price-without-nds').val(discountPrice.toFixed(2));

        // Пересчитываем с учетом НДС
        updateTotalWithNds();
    });

    $body.on('click', '.check_fix', function () {
        console.log('ok')
        const id = $(this).data('id')
        const tzId = $(this).data('tz_id')
        const leaderId = $(this).data('leader')
        const $container = $(this).closest('.check_fix_container')

        $container.html(`<i class="fa-solid fa-arrows-rotate spinner-animation"></i>`)

        $.ajax({
            method: 'POST',
            url: '/ulab/requirement/setCheckFixedAjax',
            data: {
                id: id,
                tz_id: tzId,
                leader: leaderId,
            },
            dataType: 'json',
            success: function (json) {
                $container.html(`${json.data.date_fixed} ${json.data.user_name}`)
            }
        })

        return false
    })


    $body.on('click', '.popup-edit-probe-form', function (e) {
        let data = journalDataTable.row(e.target.closest('tr')).data()

        $('#edit-probe-modal-form').find('.probe_id').val(data.id)
        $('#edit-probe-modal-form').find('.name_for_protocol').val(data.name_for_protocol)
        $('#edit-probe-modal-form').find('.is_only_name_for_protocol').prop('checked', (data.is_only_name_for_protocol == 1))
        $('#edit-probe-modal-form').find('.quarry_id').val(data.quarry_id).trigger('change')

        let $selectGroup = $('#edit-probe-modal-form').find('.select_group')

        let htmlOptionsGroup = `<option value="">Без группы</option>`
        $.ajax({
            url: "/ulab/material/getGroupByMaterialAjax/",
            method: "POST",
            cache: false,
            async: false,
            dataType: "json",
            data: {
                material_id: data.material_id,
            },
            success: function (json) {
                htmlOptionsGroup += getHtmlOptions(json, data.group)

                $selectGroup.html(htmlOptionsGroup)

                $selectGroup.select2({
                    theme: 'bootstrap-5'
                })
            }
        })

        $.magnificPopup.open({
            items: {
                src: '#edit-probe-modal-form'
            },
            type: 'inline',
            closeBtnInside: true,
            fixedContentPos: false,
            closeOnBgClick: false,
            focus: '#focus-blur-loop-select',
            midClick: true
        })
    })

    $body.on('click', '.popup-edit-material-form', function (e) {
        let data = journalDataTable.row(e.target.closest('tr')).data()

        let materialId = $(this).data('material_id')

        $('#edit-material-modal-form').find('.material_id').val(materialId).trigger('change')
        $('#edit-material-modal-form').find('.old_material_id').val(materialId)

        $.magnificPopup.open({
            items: {
                src: '#edit-material-modal-form'
            },
            type: 'inline',
            closeBtnInside: true,
            fixedContentPos: false,
            closeOnBgClick: false,
            focus: '#focus-blur-loop-select',
            midClick: true
        })
    })

    $body.on('click', '.delete-probe-btn', function () {
        return confirm(
            "Подтвердите удаление пробы и всех прикрепленных методик.\n" +
            "Пробы, по которым сформирован акт, невозможно удалить.\n" +
            "Пробы, у которых есть результаты испытаний, невозможно удалить.\n" +
            "Удаление последней пробы у материала, удалит материал."
        )
    })

    $body.on('click', '.delete-canceled-lab', function () {
        let tzId = $('#tz_id').val()
        let leaderId = $(this).data('userId')

        $.ajax({
            url: "/ulab/requirement/deleteCanceledLabAjax/",
            method: "POST",
            cache: false,
            async: false,
            dataType: "json",
            data: {
                tzId: tzId,
                leaderId: leaderId
            },
            success: function (json) {
                location.reload()
            },
            error: function (e) {
                location.reload()
            }
        })
    })

    function toggleProbe() {
        let probeIdList = $(".filter-probe-id:checked").map(function () {
            return $(this).val();
        }).get()

        let $btnGroupEdit = $('.btn-group-edit')
        let $btnAddMethods = $('.btn-add-methods')
        let $btnLinkDeal = $('.btn-link-deal')
        let $btnReplaceMethod = $('.btn-replace-method')
        let $btnDeleteMethods = $('.btn-delete-method')
        let $btnDeleteProbes = $('.btn-delete-probes')
        let $btnImportProbes = $('.btn-import-probes')

        if (probeIdList.length > 0) {
            $btnGroupEdit.removeClass('disabled')
            $btnAddMethods.removeClass('disabled')
            $btnLinkDeal.removeClass('disabled')
            $btnReplaceMethod.removeClass('disabled')
            $btnDeleteProbes.removeClass('disabled')
            $btnDeleteMethods.removeClass('disabled')
            $btnImportProbes.removeClass('disabled')
        } else {
            $btnGroupEdit.addClass('disabled')
            $btnAddMethods.addClass('disabled')
            $btnLinkDeal.addClass('disabled')
            $btnReplaceMethod.addClass('disabled')
            $btnDeleteProbes.addClass('disabled')
            $btnDeleteMethods.addClass('disabled')
            $btnImportProbes.addClass('disabled')
        }
    }

    $body.on('click', '.btn-import-probes', function () {
        let form = $('#import-probes-from-xlsx')

        $(".filter-probe-id:checked").map(function () {
            form.append(
                `
                <input type="hidden" name="ugtp[]" value="${$(this).val()}">
                `
            )
        })
    })

    $body.on('click', '.btn-delete-probes', function () {
        let probeIdList = $(".filter-probe-id:checked").map(function () {
            return $(this).val();
        }).get()

        $.ajax({
            url: "/ulab/requirement/deleteMassiveProbesAjax/",
            method: "POST",
            cache: false,
            async: false,
            dataType: "json",
            data: {
                umtr: JSON.stringify(probeIdList),
            },
            success: function (json) {
                location.reload()
            }
        })
    })

    $body.on('click', '.btn-link-deal', function () {
        let probeIdList = $(".filter-probe-id:checked").map(function () {
            return $(this).val();
        }).get()

        $('.count-selected-probe').text(probeIdList.length)
        $('.probe-id-list').val(probeIdList)
    })

    $body.on('click', '.btn-add-methods', function () {
        let probeIdList = $(".filter-probe-id:checked").map(function () {
            return $(this).val();
        }).get()
        let materialId = $(".filter-probe-id:checked").data('material_id')

        $('.count-selected-probe').text(probeIdList.length)
        $('.probe-id-list').val(probeIdList)

        let $selectScheme = $('#add-methods-modal-form').find('#select-scheme')

        let htmlOptionsScheme = `<option value="">Нет схемы / ручной ввод</option>`
        $.ajax({
            url: "/ulab/material/getSchemeByMaterialAjax/",
            method: "POST",
            cache: false,
            async: false,
            dataType: "json",
            data: {
                material_id: materialId,
            },
            success: function (json) {
                htmlOptionsScheme += getHtmlOptions(json)

                $selectScheme.html(htmlOptionsScheme)

                $selectScheme.select2({
                    theme: 'bootstrap-5',
                })
            }
        })
    })


    $body.on('click', '.btn-group-edit', function () {
        let $form = $('#edit-group-probe-modal-form') //block-group-probe-method

        let probeIdList = $(".filter-probe-id:checked").map(function () {
            return $(this).val();
        }).get()

        $('.count-selected-probe').text(probeIdList.length)
        $('.probe-id-list').val(probeIdList)

        $.ajax({
            url: "/ulab/requirement/getGroupProbeMethodAjax/",
            method: "POST",
            cache: false,
            async: false,
            dataType: "json",
            data: {
                probe_id_list: probeIdList,
            },
            success: function (json) {
                let html = ``
                $.each(json, function (i, item) {
                    let options = ``
                    $.each(item.method.assigned, function (j, user) {
                        let selected = ``
                        if (user.user_id == item.assigned_id) {
                            selected = 'selected'
                        }
                        options += `<option value="${user.user_id}" ${selected}>${user.user_name}</option>`
                    })

                    html +=
                        `<div class="row mb-2">
                            <div class="col-8">
                                ${item.method.view_gost}
                            </div>
                            <div class="col-4">
                                <select class="form-control" name="ga[${item.method.id}]">
                                    <option value="0">Не назначен</option>
                                    ${options}
                                </select>
                            </div>
                        </div>`
                })

                $form.find('.block-group-probe-method').html(html)
            }
        })

        $.ajax({
            url: "/ulab/material/getGroupByMaterialByUmtrIdAjax/",
            method: "POST",
            cache: false,
            async: false,
            dataType: "json",
            data: {
                probe_id_list: probeIdList,
            },
            success: function (json) {
                let html = ``
                $.each(json, function (i, item) {
                    let options = ``

                    $.each(item, function (j, group) {
                        if (typeof group === 'object') {
                            options += `<option value="${group.id}" >${group.name}</option>`
                        }
                    })


                    html +=
                        `<div class="row mb-2">
                            <div class="col-8">
                                ${item.material_name}
                            </div>
                            <div class="col-4">
                                <select class="form-control" name="mg[${i}]">
                                    <option value="0">Без группы</option>
                                    ${options}
                                </select>
                            </div>
                        </div>`
                })

                $form.find('.block-group-probe-group').html(html)
            }
        })
    })

    $body.on('click', '#apply-scheme', function () {
        let schemeId = $("#select-scheme").val()
        const $methodContainer = $('.method-container')
        const $methodBlock = $methodContainer.find('.method-block')
        let countMethod = 0
        const dealTypeId = $('#deal_type_id').val()
        const userGroups = JSON.parse($('#user_groups').val())

        if ($methodBlock.length > 0) {
            let num = $methodContainer.find('.method-block').map(function () {
                return $(this).data('gost_number');
            }).get();

            countMethod = Math.max.apply(Math, num) + 1;
        }

        if (schemeId !== undefined && schemeId > 0) {
            $.ajax({
                url: "/ulab/material/getSchemeParamAjax/",
                method: "POST",
                cache: false,
                async: false,
                dataType: "json",
                data: {
                    scheme_id: schemeId,
                },
                success: function (json) {

                    $.each(json, function (i, item) {
                        $methodContainer.append(getHtmlMethod(methodList, conditionList, i + countMethod, item.method_id, item.tu_id, dealTypeId, userGroups))

                        $methodContainer.find('.method-block:last-child').find('.method-select').trigger('change')
                        $methodContainer.find('.method-block:last-child').find('.select2').select2({
                            theme: 'bootstrap-5',
                            templateResult: formatState,
                            templateSelection: formatState,
                        })
                    })

                }
            })
        }
    })

    $body.on('click', '.paginate_button', function () {
        let $btnGroupEdit = $('.btn-group-edit')
        let $btnAddMethods = $('.btn-add-methods')

        $btnGroupEdit.addClass('disabled')
        $btnAddMethods.addClass('disabled')
    })

    // добавляем строчку с методикой в модальном окне добавление методик
    $body.on('click', '.add-new-method', function () {
        const $methodContainer = $('.method-container')
        const $methodBlock = $methodContainer.find('.method-block')
        let countMethod = 0
        const dealTypeId = $('#deal_type_id').val()
        const userGroups = JSON.parse($('#user_groups').val())


        if ($methodBlock.length > 0) {
            let num = $methodContainer.find('.method-block').map(function () {
                return $(this).data('gost_number');
            }).get();

            countMethod = Math.max.apply(Math, num) + 1;
        }

        $methodContainer.append(getHtmlMethod(methodList, conditionList, countMethod, 0, 0, dealTypeId, userGroups))

        $methodContainer.find('.method-block:last-child').find('.select2').select2({
            theme: 'bootstrap-5',
            templateResult: formatState,
            templateSelection: formatState,
        })
    })

    $body.on('click', '.disabled_name_for_protocol', function () {
        $(this).closest('.input-group').find('.name_for_protocol').prop('disabled', !$(this).prop('checked'))
    })
    $body.on('click', '.disabled_quarry_id', function () {
        $(this).closest('.input-group').find('.quarry_id').prop('disabled', !$(this).prop('checked'))
    })

    // удаляем строчку с методикой в модальном окне добавление методик
    $body.on('click', '.del-new-method', function () {
        $(this).parents('.method-block').remove()
    })

    // выбираем методику в модальном окне добавление методик
    $body.on('change', '.method-select', function () {
        const id = $(this).val();
        const $parent = $(this).closest('.method-block'); // Используем .closest() для надёжности

        $.ajax({
            method: 'POST',
            async: true,
            data: {id: id},
            url: '/ulab/requirement/getMethodDataAjax',
            dataType: 'json',
            success: function (data) {
                // Вставляем строку в нужный инпут

                let ndString = `от ${data.definition_range_1} до ${data.definition_range_2}`;
                $parent.find('.nd_string_input').val(ndString);

                // Обновляем поле цены
                $parent.find('.price-input').val(parseFloat(data.price));

                // Обновляем список исполнителей
                let options = `<option value="">Исполнитель</option>`;
                $.each(data.assigned, function (i, item) {
                    options += `<option value="${item.user_id}">${item.short_name}</option>`;
                });
                $parent.find('.user-select').html(options);

                // Переинициализируем Select2 для .user-select
                $parent.find('.user-select').select2({
                    theme: 'bootstrap-5',
                    templateResult: formatState,
                    templateSelection: formatState,
                });
            }
        });

        $parent.find('.method-link')
            .removeClass('disabled')
            .attr('href', `/ulab/gost/method/${id}`);
    });
    // выбираем ТУ в модальном окне добавление методик
    $body.on('change', '.tu-select', function () {
        const id = $(this).val()
        $(this).parents('.method-block').find('.tu-link')
            .removeClass('disabled')
            .attr('href', `/ulab/techCondition/edit/${id}`)
    })


    $body.on('change', '.clear_confirm_change', function () {
        $('#clear_confirm').val('1')
    })

    // отправить тз на проверку
    $body.on('click', '.sent_tz', function () {
        const delaId = $('#deal_id').val()
        const $block = $('.head-user-block')
        const $btn = $(this)

        $btn.find('i').removeClass().addClass('fa-solid fa-arrows-rotate spinner-animation')
        $btn.addClass('disable')

        $.ajax({
            method: 'POST',
            data: {
                deal_id: delaId
            },
            url: '/ulab/requirement/confirmTzSentAjax',
            dataType: 'json',
        }).always(function (data) {
            $block.find('i').removeClass().addClass('fa-solid fa-hourglass-half').attr('title', 'Ожидание проверки')
            $btn.html(`<i class="fa-regular fa-paper-plane"></i> Передано`)
        })
    })

    // одобрить тз
    $body.on('click', '.approve_tz', function () {
        const tzId = $('#tz_id').val()
        const $block = $('.curr_user')
        const $btn = $(this)
        const dealId = $('#deal_id').val()

        let dateLeader = $('#date-start-leader').val()
        let daysLeader = $('#days-leader').val()
        let leaderId = $('#leader-id').val()

        if (!dateLeader || !daysLeader) {
            showErrorMessage("Заполните ориентировочную дату начала и кол-во рабочий дней!")

            window.scrollTo(0, 0)
        } else {
            $btn.find('i').removeClass().addClass('fa-solid fa-arrows-rotate spinner-animation')
            $btn.addClass('disable')
            $('.not_approve_tz').addClass('disable')

            $.ajax({
                method: 'POST',
                data: {
                    tz_id: tzId,
                    deal_id: dealId,
                    date_leader: dateLeader,
                    days_leader: daysLeader,
                    leader_id: leaderId
                },
                url: '/ulab/requirement/confirmTzApproveAjax',
                dataType: 'json',
            }).always(function (data) {
                $block.find('.icon').addClass('text-green').find('i').removeClass().addClass('fa-regular fa-circle-check').attr('title', 'ТЗ потверждено')
                $btn.find('i').removeClass().addClass('fa-regular fa-circle-check')

                location.reload()
            })
        }
    })

    // отправить и одобрить тз
    $body.on('click', '.sent_approve_tz', function () {
        const tzId = $('#tz_id').val()
        const delaId = $('#deal_id').val()
        const $headBlock = $('.head-user-block')
        const $block = $('.curr_user')
        const $btn = $(this)

        $btn.find('i').removeClass().addClass('fa-solid fa-arrows-rotate spinner-animation')
        $btn.addClass('disable')
        $('.not_approve_tz').addClass('disable')

        $.ajax({
            method: 'POST',
            data: {
                tz_id: tzId,
                deal_id: delaId,
            },
            url: '/ulab/requirement/confirmTzSentApproveAjax',
            dataType: 'json',
        }).always(function (data) {
            $headBlock.find('i').removeClass().addClass('fa-solid fa-hourglass-half').attr('title', 'Ожидание проверки')
            $block.find('.icon').addClass('text-green').find('i').removeClass().addClass('fa-regular fa-circle-check').attr('title', 'ТЗ потверждено')
            $btn.find('i').removeClass().addClass('fa-regular fa-circle-check')
        })
    })

    // не одобрить тз
    $body.on('click', '.not_approve_tz_btn', function () {
        const tzId = $('#tz_id').val()
        const $block = $('.curr_user')
        const $btn = $(this)
        const desc = $('#desc_return').val()

        $btn.find('i').removeClass().addClass('fa-solid fa-arrows-rotate spinner-animation')
        $('.not_approve_tz').addClass('disable')
        $('.approve_tz').addClass('disable')

        $.ajax({
            method: 'POST',
            data: {
                tz_id: tzId,
                desc: desc
            },
            url: '/ulab/requirement/confirmTzNotApproveAjax',
            dataType: 'json',
        }).always(function (data) {
            $block.find('.icon').addClass('text-red').find('i').removeClass().addClass('fa-regular fa-circle-xmark')
            $btn.find('i').removeClass().addClass('fa-regular fa-circle-xmark')
            $.magnificPopup.close()
            location.reload()
        })
    })
    //замена инпута тип окончания срока
    $body.on('change', '.type_of_day', function () {
        let type = $(this).val(),
            inputDTT = $body.find('.day-to-test')

        if (type == 'custom') {
            inputDTT.val('')
            inputDTT.removeClass('number-only')
            inputDTT.prop('type', 'date')
        } else {
            inputDTT.removeClass('number-only')
            inputDTT.addClass('number-only')
            inputDTT.prop('type', 'number')
            inputDTT.val(20)
        }
    })


    $body.on('submit', '#add-comment-modal-form', function () {
        let $form = $(this)
        let ugtpId = $form.find('.ugtp_id').val()
        let formData = $form.serialize()

        $.ajax({
            method: 'POST',
            url: '/ulab/requirement/setCommentGtpAjax/',
            data: {
                ugtp_id: ugtpId,
                form: formData
            },
            success: function (data) {

            },
            complete: function (data) {
                $form.find('textarea').text('')
                $form.find('.ugtp_id').val('')

                $.magnificPopup.close()

                journalDataTable.ajax.reload()
                journalDataTable.draw()
            }
        })

        return false
    })

    $body.on('change', '#group-type', function () {
        const tzId = $('#tz_id').val()
        let group_type = $(this).val()

        console.log(tzId)
        console.log(group_type)

        $.ajax({
            url: "/ulab/requirement/updateGroupType/",
            data: {
                "tz_id": tzId,
                "group_type": group_type,
            },
            dataType: "json",
            method: "POST",
            success: function (json) {
            }
        })
    })

    $body.on('click', '.act-details-control', function () {
        console.log($(this))
    })
})


/**
 *
 * @param methodList
 * @param conditionList
 * @param gostNumber
 * @param defaultMethod
 * @param defaultTu
 * @param dealTypeId
 * @returns {string}
 */
function getHtmlMethod(methodList, conditionList, gostNumber = 0, defaultMethod = 0, defaultTu = 0, dealTypeId = '', userGroups = []) {
    let optionMethod = getHtmlOptionsMethod(methodList, defaultMethod)
    let optionCondition = getHtmlOptionsTu(conditionList, defaultTu)
    let priceReadonly = ''

    if (dealTypeId === 'COMPLEX') {
        priceReadonly = ''
    }

    if (dealTypeId === 'SALE' && !userGroups.includes('23')) {
        priceReadonly = 'readonly'
    }


    return `<div class="row justify-content-between method-block mb-2" data-gost_number="${gostNumber}">
                <div class="col-3">
                    <div class="input-group">
                        <select class="form-control select2 method-select" name="form[${gostNumber}][new_method_id]" required>
                            <option value=""></option>
                            ${optionMethod}
                        </select>
                        <a class="btn btn-outline-secondary method-link disabled" target="_blank" title="Перейти в методику" href="">
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </a>
                    </div>
                </div>
                <div class="col-2">
                    <div class="input-group">
                        <select class="form-control select2 method-select" name="form[${gostNumber}][name_nd]" required>
                            <option value=""></option>
                            ${optionCondition}
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <div class="input-group">
                        <input type = "text" class="form-control nd_string_input" name="form[${gostNumber}][nd_string_input]">
                    </div>
                </div>
                <div class="col-2">
                    <select class="form-control user-select select2" name="form[${gostNumber}][assigned_id]">
                        <option value="">Исполнитель</option>
                    </select>
                </div>
                <div class="col">
                    <div class="input-group">
                        <input class="form-control price-input" name="form[${gostNumber}][price]" type="number" min="0" step="0.01" value="0" ${priceReadonly}>
                        <span class="input-group-text">₽</span>
                    </div>
                </div>
                <div class="col-auto">
                    <button
                            class="btn btn-danger mt-0 del-new-method btn-square float-end"
                            type="button"
                    >
                        <i class="fa-solid fa-minus icon-fix"></i>
                    </button>
                </div>
            </div>`
}


/**
 *
 * @param conditionList
 * @param defaultTu
 * @returns {string}
 */
function getHtmlOptionsTu(conditionList, defaultTu = '') {
    let optionCondition = '<option value="">--/--</option>';

    $.each(conditionList, function (i, item) {
        let selected = '';

        if (item === defaultTu) {
            selected = 'selected';
        }

        optionCondition += `<option value="${item}" ${selected}>${item}</option>`;
    });

    return optionCondition;
}


/**
 *
 * @param methodList
 * @param defaultMethod
 * @returns {string}
 */
function getHtmlOptionsMethod(methodList, defaultMethod = 0) {
    let optionMethod = ''

    $.each(methodList, function (i, item) {

        let dataColor = ''
        let selected = ``

        if (item.is_actual == 0) {
            dataColor = `data-color="#F00"`
        } else if (item.in_field == 0) {
            dataColor = `data-color="#060060"`
        } else if (item.is_confirm == 0) {
            dataColor = `data-color="#dfdf11"`
        }

        if (item.id == defaultMethod) {
            selected = 'selected'
        }

        optionMethod += `<option value="${item.id}" ${dataColor} ${selected}>${item.view_gost}</option>`
    })

    return optionMethod
}


/**
 *
 * @param list
 * @param id
 * @returns {string}
 */
function getHtmlOptions(list, id = 0) {
    let optionMethod = ''

    $.each(list, function (i, item) {
        let selected = ``

        if (item.id == id) {
            selected = 'selected'
        }

        optionMethod += `<option value="${item.id}" ${selected}>${item.name}</option>`
    })

    return optionMethod
}


/**
 *
 * @param methodId
 * @param defaultUserId
 * @returns {string}
 */
function getHtmlOptionAssignedByMethod(methodId, defaultUserId = 0) {
    let htmlOption = ''

    $.ajax({
        method: 'POST',
        async: false,
        data: {
            id: methodId
        },
        url: '/ulab/requirement/getMethodDataAjax',
        dataType: 'json',
        success: function (data) {

            htmlOption += `<option value="">Не назначен</option>`
            $.each(data.assigned, function (i, item) {
                let selected = ``
                if (item.user_id == defaultUserId) {
                    selected = 'selected'
                }
                htmlOption += `<option value="${item.user_id}" ${selected}>${item.short_name}</option>`
            })
        }
    })

    return htmlOption
}


/**
 * @param gostList
 * @param defaultId
 * @returns {string}
 */
function getHtmlOptionGost(gostList, defaultId) {
    let option = ''

    $.each(gostList, function (i, item) {
        let selected = ``

        if (item.id == defaultId) {
            selected = 'selected'
        }

        option += `<option value="${item.id}" ${selected}>${item.reg_doc}</option>`
    })

    return option
}


function createChild(row) {
    // row — исходный объект данных
    let rowData = row.data()

    let probeId = []
    const dealId = rowData.deal_id
    const $spanMethodCount = $('body').find(`.count_methods_probe_${rowData.id}`)
    probeId.push(rowData.id)

    // Таблица, которую мы преобразуем в DataTable
    let table = $('<table class="table table-striped journal text-start table-hover table-sm" />')

    let thead = `
        <thead>
            <tr class="table-light">
                <th scope="col" style="width: 2%">№</th>
                <th scope="col" style="width: 61%">Методика испытаний</th>
                <th scope="col" style="width: 2%"></th>
                <th scope="col" style="width: 16%">НД</th>
                <th scope="col" style="width: 9%">Исполнитель</th>
                <th scope="col" style="width: 10%">Цена</th>
                <th scope="col" style="width: 3%"></th>
            </tr>
        </thead>
    `

    table.append(thead)

    // Отобразить дочернюю строку
    row.child(table).show()

    let journal = table.DataTable({
        destroy: true,
        ordering: false,
        serverSide: true,
        rowReorder: true,
        initialValue: true,
        dom: 't<"bottom"lip>',
        bAutoWidth: false,
        autoWidth: false,
        responsive: true,
        pageLength: -1,
        ajax: {
            type: 'POST',
            data: function (d) {
                d.deal_id = dealId
                d.probe_id = probeId
            },
            url: '/ulab/requirement/getMethodJournalAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'gost_number',
                orderable: false,
                className: 'text-center gost_number grabable'
            },
            {
                data: 'method_view_name',
                orderable: false,
                className: 'method_view_name edit-cell cursor-pointer',
                render: function (data, type, item) {
                    if (item.is_actual == 0) {
                        return `<div class="text-danger is-invalid">${item.method_view_name}</div>
                                <div class="invalid-feedback">
                                    <i>Методика не актуальна</i>
                                </div>`
                    } else if (item.in_field == 0 && item.is_extended_field == 0) {
                        return `<div class="text-dark-blue is-invalid">${item.method_view_name}</div>
                                <div class="text-dark-blue invalid-feedback">
                                    <i>Методика не в области аккредитации</i>
                                </div>`
                    } else if (item.is_confirm == 0) {
                        return `<div class="text-yellow is-invalid">${item.method_view_name}</div>
                                <div class="text-yellow invalid-feedback">
                                    <i>Методика не проверена</i>
                                </div>`
                    }

                    return `${item.method_view_name}`
                }
            },
            {
                data: 'comment',
                orderable: false,
                className: 'text-center',
                render: function (data, type, item) {
                    if (item.comment !== '' && item.comment !== null && item.comment !== undefined) {
                        return `<a href="#" class="cursor-pointer add_comment_modal_form"><i class="text-black fa-regular fa-message-dots icon-big"></i></a>`
                    }

                    return `<a href="#" class="cursor-pointer add_comment_modal_form"><i class="text-black fa-regular fa-message-pen icon-big"></i></a>`
                }
            },
            {
                data: 'tech_condition_string',
                orderable: false,
                className: 'tu_view_name edit-cell cursor-pointer'
            },
            {
                data: 'assigned_name',
                orderable: false,
                className: 'assigned_name edit-cell cursor-pointer'

            },
            {
                data: 'price',
                width: "111px",
                orderable: false,
                className: 'price edit-cell cursor-pointer'
            },
            {
                data: 'edit',
                orderable: false,
                className: 'text-end',
                render: function (data, type, item) {
                    return `
                        <button type="button" class="btn btn-danger btn-sm btn-square-sm delete-method" data-gtp_id="${item.id}">
                            <i class="fa-solid fa-xmark"></i>
                        </button>`
                }
            },
        ],
        language: dataTablesSettings.language,
    })

    // Инициализация поиска для каждой колонки
    table.find('.search').each(function (index) {
        $(this).on('keyup', function () {
            journal.column(index + 1).search($(this).val()).draw()
        })
    })

    // двигаем строки
    journal.on('row-reordered', function (e, diff, edit) {
        let dataList = []
        $.each(edit.nodes, function (i, item) {
            let data = journal.row(item).data()
            data.newPosition = diff[i].newPosition

            dataList.push(data)
        })

        if (dataList.length > 0) {
            $.ajax({
                url: "/ulab/requirement/changeGostNumberAjax",
                data: {
                    "data": dataList,
                },
                dataType: "text",
                method: "POST",
                success: function (json) {
                    journal.ajax.reload()
                    journal.draw()
                }
            })
        }
    })


    // удалить методику
    journal.on('click', '.delete-method', function () {
        if (confirm("Подтвердите удаление методики.\nВнимание, методика, у которой есть результат испытания, не удалится.")) {
            const id = $(this).data('gtp_id')
            const tzId = $('#tz_id').val()

            $.ajax({
                url: "/ulab/requirement/deleteMethodAjax/",
                data: {
                    "id": id,
                    "tz_id": tzId,
                },
                dataType: "json",
                method: "POST",
                success: function (json) {
                    if (json.success) {
                        $spanMethodCount.text(--rowData.count_methods)

                        $strTotal.text(json.data.price_discount + ' руб.')
                        $strTotalDns.text(round(parseFloat(json.data.price_discount) * dnsMulti, 2))
                        $inputTotal.val(json.data.price)
                        $inputDiscount.val(json.data.price_discount)

                        // Навешиваем разовый обработчик
                        journal.one('xhr', function () {
                            refreshMethodFilterAfterProbeDelete();
                        });

                        journal.ajax.reload()
                        journal.draw()
                    } else {
                        alert(`При удалении возникла ошибка: ${json.error}`)
                    }
                }
            })
        }
    })


    journal.on('click', '.add_comment_modal_form', function () {
        let $form = $('#add-comment-modal-form')

        $form.find('textarea').text('')
        $form.find('.ugtp_id').val('')

        let data = journal.row($(this).closest('tr')).data()

        $.ajax({
            url: "/ulab/requirement/getCommentGtpAjax/",
            data: {
                "ugtp_id": data.id,
            },
            dataType: "json",
            method: "POST",
            success: function (json) {
                $form.find('textarea').text(data.comment)
                $form.find('.ugtp_id').val(data.id)
                $form.find('input').val(json[0]['sequence'])

                $.magnificPopup.open({
                    items: {
                        src: '#add-comment-modal-form',
                        type: 'inline',
                    },
                    closeBtnInside: true,
                    fixedContentPos: false,
                    closeOnBgClick: false,
                    focus: '#focus-blur-loop-select',
                    midClick: true
                })
            }
        })

        return false
    })


    // редактируем ячейку
    journal.on('click', 'td.edit-cell', function (e) {

        const $thisCell = $(this)
        const userGroups = JSON.parse($('#user_groups').val())
        let data = journal.row($thisCell.closest('tr')).data()

        let textConfirmTz = $('.confirmed-tz')

        if ($thisCell.hasClass('assigned_name')) {
            $thisCell.html(`<select class="form-control select2">` + getHtmlOptionAssignedByMethod(data.new_method_id, data.assigned_id) + `</select>`)
        } else if ($thisCell.hasClass('tu_view_name')) {
            $thisCell.html(
                `<input type="text" class="form-control" style="width: 400px" value="${data.tech_condition_string ?? ''}">`
            );
        } else if ($thisCell.hasClass('method_view_name') && (userGroups.includes('23') || textConfirmTz.length === 0)) {
            $thisCell.html(`<select class="form-control select2">` + getHtmlOptionsMethod(methodList, data.new_method_id) + `</select>`)
        } else if ($thisCell.hasClass('price') && userGroups.includes('23')) {
            $thisCell.html($(`<input type="number" class="form-control" style="min-width: 160px" value="${data.price}">`))
        } else {
            return false
        }

        $thisCell.removeClass('edit-cell')

        $('#save').addClass('disabled')

        $thisCell.addClass('save-cell')

        $thisCell.find('.select2').select2({
            theme: 'bootstrap-5',
            templateResult: formatState,
            templateSelection: formatState,
        })

        if ($thisCell.find('.select2.hud').length > 0) {
            $thisCell.find('.select2.hud').select2({
                theme: 'bootstrap-5',
                templateResult: formatState,
                templateSelection: formatState,
                dropdownCssClass: 'hud-dropdown'
            })
        }
    })


    // сохраняем изменения в ячейке
    journal.on('change', 'td.save-cell select, td.save-cell input', function () {
        const tzId = $('#tz_id').val()
        let $thisCell = $(this).closest('td')
        let $thisRow = $(this).closest('tr')
        let val = $(this).val()
        let data = journal.row($thisRow.closest('tr')).data()
        let cellName = ''
        let lastVal = ''

        if ($thisCell.hasClass('assigned_name')) {
            cellName = 'assigned'
            lastVal = data.assigned_id

        } else if ($thisCell.hasClass('tu_view_name')) {
            cellName = 'tu'
            lastVal = data.tech_condition_string
        } else if ($thisCell.hasClass('method_view_name')) {
            cellName = 'method'
            lastVal = data.new_method_id
        } else if ($thisCell.hasClass('price')) {
            cellName = 'price'
            lastVal = data.price
        } else {
            return false
        }

        $.ajax({
            url: "/ulab/requirement/updateMethodAjax/",
            data: {
                "id": data.id,
                "tz_id": tzId,
                "val": val,
                "lastVal": lastVal,
                "field": cellName,
            },
            dataType: "json",
            method: "POST",
            success: function (json) {
                console.log(json);

                if (json.success) {
                    journal.ajax.reload()
                    journal.draw()

                    $strTotal.text(json.data.price_discount + ' руб.')
                    $strTotalDns.text(round(parseFloat(json.data.price_discount) * dnsMulti, 2))
                    $inputTotal.val(json.data.price)
                    $inputDiscount.val(json.data.price_discount)

                    $('#save').removeClass('disabled')
                } else {
                    alert(`При обновлении возникла ошибка: ${json.error}`)
                }
            }
        })

    })
}

const classes = [
    'nd_list',
    'manufacturer',
    'manufacture_date',
    'batch_mass',
    'batch_size',
    'batch_number',
    'packing_name',
    'packaging_integrity',
    'expiration_date',
    'storage_conditions',
    'label_description',
    'delivery_method'
];

$(document).on('change', function (event) {
    const $element = $(event.target);
    const tz_id = $('#tz_id').val();

    const changedClass = classes.find(className =>
        $element.hasClass(className)
    );

    if (!changedClass) {
        return;
    }

    $.ajax({
        method: "POST",
        url: '/ulab/requirement/setDopInfoAjax',
        data: {
            field_name: changedClass,
            id_tz: tz_id,
            value: $element.val()
        },
        dataType: 'json',
        success: function (json) {
            console.log(json);
        }
    })

    console.log('tz', tz_id);
    console.log('Изменившийся класс:', changedClass);
    console.log('Новое значение:', $element.val());
});
