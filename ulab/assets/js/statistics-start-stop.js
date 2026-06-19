$(function () {
    let $journal = $('#trialStatistics')

    /*journal trial statistics*/
    let journalDataTable = $journal.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            url : '/ulab/result/getTrialStatisticsAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            {
                data: 'method',
                render: function (data, type, item) {
                    return `<a href="/ulab/gost/method/${item.method_id}" class="text-decoration-none" target="_blank">
                                ${item.method}
                            </a>`
                }
            },
            {
                data: 'ugtp_count'
            },
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
        order: [[ 2, "desc" ]],
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

    // Добавить прослушиватель событий для открытия и закрытия сведений
    $('#trialStatistics tbody').on('click', 'td.details-control', function () {
        let tr = $(this).closest('tr');
        let row = journalDataTable.row( tr );

        if ( row.child.isShown() ) {
            // Эта строка уже открыта - закрываем
            destroyChild(row);
            tr.removeClass('shown');
        }
        else {
            // Открыть эту строку
            createChild(row);
            tr.addClass('shown');
        }
    });

});


function createChild (row) {
    let rowData = row.data();

    // Таблица, которую мы преобразуем в DataTable
    let table = $('<table class="table table-striped journal table-fixed text-left" />');

    // Отобразить дочернюю строку
    row.child(table).show();

    let ust = table.DataTable( {
        dom: 't<"bottom"ip>',
        pageLength: 15,
        ajax: {
            url : '/ulab/result/getStartStopTrialsAjax/',
            type: 'post',
            data: function (d) {
                d.method_id = rowData.method_id;
            },
            dataSrc: function (json) {
                console.log('json.data', json.data)
                return json.data
            }
        },
        columns: [
            {
                data: 'TITLE',
                class: 'text-nowrap',
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return `<a class="request-link"
                               href="/ulab/request/card/${item.deal_id}" target="_blank">
                               ${item['TITLE']}
                            </a>`
                    }

                    return item.TITLE
                }
            },
            { data: 'cipher' },
            {
                "orderable":      false,
                "data":           null,
                "defaultContent": 'Дата начала'
            },
            { data: 'date_start' },
            {
                "orderable":      false,
                "data":           null,
                "defaultContent": 'Дата окончания'
            },
            { data: 'date_complete' },
            {
                "orderable":      false,
                "data":           null,
                "defaultContent": 'Кол-во времени'
            },
            { data: 'time' },
            {
                "orderable":      false,
                "data":           null,
                "defaultContent": 'Исполнитель'
            },
            { data: 'short_name' },
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
        ordering: false,
        fnDrawCallback: function() {
            table.find("thead").remove();
            table.css("border-bottom", "none");
        }
    } );
}

function destroyChild(row) {
    let table = $("table", row.child());
    table.detach();
    table.DataTable().destroy();

    // Скрыть дочернюю строку
    row.child.hide();
}