$(function ($) {
    $('.popup-with-form').magnificPopup({
        type: 'inline',
        closeBtnInside:true,
        fixedContentPos: false
    })

    let body = $('body')

    let $journal = $('#journal_material')

    /*journal requests*/
    let journalDataTable = $journal.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
                url : '/ulab/material/getListAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'NAME',
                orderable: true,
                render: function (data, type, item) {
                    return `<a class="results-link"
                               href="/ulab/material/card/${item['ID']}" target="_blank">
                               ${item['NAME']}
                            </a>`
                }
            },
            {
                data: 'is_active',
                width: '100px',
                className: 'text-center',
                orderable: true,
                render: function (data, type, item) {
                    let checked = ''
                    if ( item.is_active == 1 ) {
                        checked = 'checked'
                    }
                    return `<label class="switch m-auto">
                                <input type="checkbox" class="form-check-input mt-0 change-is-used" ${checked} data-id="${item.ID}">
                                <span class="slider"></span>
                            </label>`
                }
            },
            // {
            //     data: 'linkName',
            //     orderable: false,
            //     className: 'text-center',
            //     width: '100px',
            //     render: function (data, type, item) {
            //         return `<a class="delete-material btn btn-sm btn-danger btn-square-sm">
            //                     <i class="fa-solid fa-xmark"></i>
            //                 </a>`
            //     }
            // },
        ],
        language: dataTablesSettings.language,
        lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
        pageLength: 25,
        dom: 'frt<"bottom"lip>',
        colReorder: true,
        bSortCellsTop: true,
        order: [[ 0, 'asc' ]],
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

    $journal.on('click', '.delete-material', function () {
        let data = journalDataTable.row($(this).closest('tr')).data()

        if (confirm('Вы точно хотите удалить материал ' + data.NAME + '?')) {
            $.ajax({
                method: 'POST',
                url: '/ulab/material/deleteMaterialAjax',
                data: {
                    id: data.ID
                },
                success: function (data) {
                    journalDataTable.ajax.reload()
                    journalDataTable.draw()
                }
            })
        }
    })

    $journal.on('change', '.change-is-used', function () {
        let id = $(this).data('id')

        $.ajax({
            method: 'POST',
            async: false,
            url: '/ulab/material/changeActiveMaterialAjax/',
            data: {
                id: id
            },
            dataType: 'text',
            success: function (data) {
                journalDataTable.ajax.reload()
                journalDataTable.draw()
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
})