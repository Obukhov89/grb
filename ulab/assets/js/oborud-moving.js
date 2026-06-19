$(function ($) {
    $('.select2').select2({
        theme: 'bootstrap-5'
    })

    $('#is_return_check').change(function () {
        if ( $(this).prop('checked') ) {
            $('#is_new_check').prop('checked', false)
            $('#place-moving-block').hide()
            $('#place-moving-block').find('input').val('Возвращено')
        } else {
            $('#place-moving-block').find('input').val('')
            $('#place-moving-block').show()
        }
    })

    $('#is_new_check').change(function () {
        if ( $(this).prop('checked') ) {
            $('#is_return_check').prop('checked', false)
            $('#place-moving-block').hide()
            $('#place-moving-block').find('input').val('Куплено')
        } else {
            $('#place-moving-block').find('input').val('')
            $('#place-moving-block').show()
        }
    })

    const $journal = $('#journal_moving')

    let journalDataTable = $journal.DataTable({
        rowReorder: true,
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
                d.oborud_id = $('#selectOborud').val()
            },
            url : '/ulab/oborud/getOborudMovingJournal/',
            dataSrc: function (json) {
                return json.data
            },
        },
        rowReorder: {
            dataSrc: 'name'
        },
        columns: [
            {
                data: 'name',
                orderable: true,
                render: function (data, type, item) {
                    return `<a href="/ulab/oborud/edit/${item.ID}">${item.name}</a>`
                }
            },
            {
                data: 'place',
                orderable: false,
            },
            {
                data: 'date',
                orderable: true,
            },
            {
                data: 'receiver_user',
                orderable: false,
            },
            {
                data: 'completeness',
                orderable: false,
            },
            {
                data: 'no_defects',
                orderable: false,
            },
            {
                data: 'passport',
                orderable: false,
            },
            {
                data: 'manual',
                orderable: false,
            },
            {
                data: 'documents',
                orderable: false,
            },
            {
                data: 'performance',
                orderable: false,
            },
            {
                data: 'comment',
                orderable: false,
            },
            {
                data: 'responsible_user',
                orderable: false,
            },
            {
                data: 'is_confirm',
                orderable: false,
                render: function (data, type, item) {
                    if ( item.is_confirm == 1 ) {
                        return 'Да'
                    } else if ( item.is_confirm == 0 ) {
                        return 'Нет'
                    } else {
                        return '--'
                    }
                }
            },
        ],
        language: dataTablesSettings.language,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        pageLength: 25,
        order: [[ 2, 'desc' ]],
        dom: 'frtB<"bottom"lip>',
        buttons: dataTablesSettings.buttons,
    })

    journalDataTable.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            journalDataTable
                .column( $(this).parent().index() )
                .search( this.value )
                .draw()
        })
    })

    $('.filter').on('change', function () {
        journalDataTable.ajax.reload()
        journalDataTable.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })
})