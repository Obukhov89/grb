$(function () {
    let $journal = $('#journal_table')

    /*journal requests*/
    let journalDataTable = $journal.DataTable({
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
            url : '/ulab/statistic/getDiffDayProtocolJournalAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'stage',
                orderable: false,
                render: function (data, type, item) {
                    return `<a class="stage rounded ${item['bgStage']}" data-deal-id="${item['ID_Z']}" title="${item['titleStage']}" data-bs-toggle="offcanvas" href="#offcanvasExample" role="button" aria-controls="offcanvasExample" style="background-color: ${item['bgStage']};">
                                <div class="rounded" style="width: 32px; height: 32px; background-color: ${item['bgStage']};"></div>
                            </a>`
                }
            },
            {
                data: 'protocol_number',
                class: 'text-center',
            },
            {
                data: 'title',
                render: function (data, type, item) {
                    return `<a class="request-link"
                               href="/ulab/request/card/${item.deal_id}" target="_blank">
                               ${item['title']}
                            </a>`
                }

            },
            {
                data: 'in_lims',
                orderable: false,
                class: 'text-center',
            },
            {
                data: 'protocol_date',
                class: 'text-center',
            },
            {
                data: 'test_date',
                class: 'text-center',
            },
            {
                data: 'diff_day',
                class: 'text-center',
            },
            {
                data: 'diff_day_send',
                class: 'text-center',
            },
            {
                data: 'user',
                orderable: false,
            },
        ],
        language: dataTablesSettings.language,
        lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
        pageLength: 25,
        order: [[ 1, "desc" ]],
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

    /*journal filters*/
    $('.filter').on('change', function () {
        journalDataTable.ajax.reload()
        journalDataTable.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })
})