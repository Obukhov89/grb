$(function () {
    const $body = $('body')

    $body.on('click', '.apply_btn', function () {
        const $btn = $(this)
        const $row = $(this).closest('tr')

        const methodId = $btn.data('id')
        const durationGost = $row.find('.duration_gost').val()
        const durationEmployee = $row.find('.duration_employee').val()
        const countEmployee = $row.find('.count_employee').val()
        const durationEquip = $row.find('.duration_equip').val()
        const durationProbe = $row.find('.duration_probe').val()
        const durationTotal = $row.find('.duration_total').val()
        const durationWork = $row.find('.duration_work').val()

        console.log(durationEmployee)

        $.ajax({
            method: 'POST',
            url: '/ulab/gost/setDurationAjax/',
            data: {
                method_id: methodId,
                duration_gost: durationGost,
                duration_employee: durationEmployee,
                count_employee: countEmployee,
                duration_equip: durationEquip,
                duration_probe: durationProbe,
                duration_total: durationTotal,
                duration_work: durationWork,
            },
            dataType: "text",
            success: function (data) {
                if ( $btn.hasClass('btn-success') ) {
                    $btn.removeClass('btn-success').addClass('btn-outline-success')
                }

                showSuccessMessage("Запись обновлена")

                journalDataTable.ajax.reload()
                journalDataTable.draw()
            }
        })
    })

    $body.on('click', '.click_input', function () {
        let classListDiv = $(this).attr('class'),
            input = $(this).parent('td').find('.form-control'),
            inputVal = input.val()

        $(this).addClass('visually-hidden')
        input.removeClass('visually-hidden')
        if (inputVal === '0') {
            input.focus().val('')
        } else {
            input.focus().val('').val(inputVal)
        }
    })

    $body.on('input', '#journal_gost .duration_input', function () {
        const $tr = $(this).closest('tr')
        const $btn = $tr.find('.apply_btn')
        const $input = $tr.find('.duration_input')
        const $total = $tr.find('.duration_total')
        const $totalDiv = $tr.find('.duration_total_div')

        let total = 0



        $.each($input, function (i, item) {
            if ($(item).val() === '') {
                return false
            }
            total += parseFloat($(item).val())
        })

        $total.val(total)
        $totalDiv.text(total)

        if ( $btn.hasClass('btn-outline-success') ) {
            $btn.removeClass('btn-outline-success').addClass('btn-success')
        }
    })

    $body.on('input', '#journal_gost .work_input', function () {
        const $tr = $(this).closest('tr')
        const $btn = $tr.find('.apply_btn')
        const durationEmployee = $tr.find('.duration_employee').val()
        const countEmployee = $tr.find('.count_employee').val()
        const durationProbe = $tr.find('.duration_probe').val()
        const $work = $tr.find('.duration_work')
        const $totalDiv = $tr.find('.duration_work_div')

        let work = 0

        if (countEmployee > 0) {
            work = (parseFloat(durationEmployee) * parseFloat(countEmployee)) + parseFloat(durationProbe)
        }

        $work.val(work)
        $totalDiv.text(work)

        if ( $btn.hasClass('btn-outline-success') ) {
            $btn.removeClass('btn-outline-success').addClass('btn-success')
        }
    })



    let $journal = $('#journal_gost')

    /*journal requests*/
    let journalDataTable = $journal.DataTable({
        bAutoWidth: false,
        autoWidth: false,
        fixedColumns: false,
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.stage = $('#selectStage option:selected').val()
                d.lab = $('#selectLab option:selected').val()
            },
            url : '/ulab/gost/getJournalAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'reg_doc',
                render: function (data, type, item) {
                    return `<a href="/ulab/gost/edit/${item.gost_id}">${item.reg_doc} ${item.clause}</a>`
                }
            },
            {
                data: 'name',
                render: function (data, type, item) {
                    if ( item.method_id === null ) {
                        return `Методик не добавлено`
                    }
                    if ( item.mp_name === null ) {
                        return `<a href="/ulab/gost/method/${item.method_id}">${item.name}</a>`
                    }
                    return `<a href="/ulab/gost/method/${item.method_id}">${item.mp_name}</a>`
                }
            },
            // {
            //     data: 'duration_gost',
            //     orderable: false,
            //     render: function (data, type, item) {
            //         return `<input class="form-control duration_gost" value="${item.duration_gost}" type="number" step="0.1">`
            //     }
            // },
            {
                data: 'duration_gost',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="duration_gost_div click_input">${item.duration_gost}</div>
                            <input class="form-control visually-hidden duration_gost" value="${item.duration_gost}" type="number" step="0.1">`
                }
            },
            // {
            //     data: 'duration_employee',
            //     orderable: false,
            //     render: function (data, type, item) {
            //         return `<input class="form-control duration_employee duration_input work_input" value="${item.duration_employee}" type="number" step="0.1">`
            //     }
            // },
            {
                data: 'duration_employee',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="duration_employee_div work_input click_input">${item.duration_employee}</div>
                            <input class="form-control visually-hidden duration_employee duration_input work_input" value="${item.duration_employee}" type="number" step="0.1">`
                }
            },
            // {
            //     data: 'duration_equip',
            //     orderable: false,
            //     render: function (data, type, item) {
            //         return `<input class="form-control duration_equip duration_input" value="${item.duration_equip}" type="number" step="0.1">`
            //     }
            // },
            {
                data: 'duration_equip',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="duration_equip_div click_input">${item.duration_equip}</div>
                            <input class="form-control visually-hidden duration_equip duration_input" value="${item.duration_equip}" type="number" step="0.1">`
                }
            },
            {
                data: 'duration_probe',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="duration_probe_div click_input">${item.duration_probe}</div>
                            <input class="form-control visually-hidden duration_probe duration_input work_input" value="${item.duration_probe}" type="number" step="0.1">`
                }
            },
            {
                data: 'duration_total',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="duration_total_div click_input">${item.duration_total}</div>
                            <input class="form-control visually-hidden duration_total" value="${item.duration_total}" type="number" step="0.1">`
                }
            },
            {
                data: 'count_employee',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="count_employee_div click_input">${item.count_employee}</div>
                            <input class="form-control visually-hidden count_employee work_input" value="${item.count_employee}" type="number" step="0.1">`
                }
            },
            {
                data: 'work',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="duration_work_div click_input">${item.duration_work}</div>
                            <input class="form-control visually-hidden duration_work" value="${item.duration_work}" type="number" step="0.1">`
                }
            },
            {
                data: 'btn',
                orderable: false,
                className: 'text-center',
                render: function (data, type, item) {
                    return `<button class="btn btn-outline-success btn-square apply_btn" data-id="${item.method_id}" type="button" title="Применить"><i class="fa-solid fa-check"></i></button>`
                }
            }
        ],
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
        order: [[ 0, "desc" ]],
        colReorder: true,
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
        fixedHeader:   true,
    });

    journalDataTable.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            journalDataTable
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        })
    })

    /*journal filters*/
    $('.filter-btn-search').on('click', function () {
        $('#journal_filter').addClass('is-open')
        $('.filter-btn-search').hide()
    })

    $('.filter').on('change', function () {
        journalDataTable.ajax.reload()
        journalDataTable.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })
})
