$(function ($) {
    let $journal = $('#journal_protocol')

    /*journal requests*/
    let journalDataTable = $journal.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.dateStart = $('#inputDateStart').val()
                d.dateEnd = $('#inputDateEnd').val()
                d.stage = $('#selectStage option:selected').val()
                d.lab = $('#selectLab option:selected').val()
                d.send = $('#selectSend option:selected').val()
                d.everywhere = $('#filter_everywhere').val()
            },
            url : '/ulab/protocol/getListProcessingAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'stage',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="stage rounded ${item['bgStage']}" title="${item['titleStage']}"></div>`
                }
            },
            {
                data: 'ATTESTAT',
                orderable: false
            },
            {
                data: 'ATTESTAT_IN_PROTOCOL'
            },
            {
                data: 'NUMBER_AND_YEAR',
                render: function (data, type, item) {
                    if ( item['is_non_actual'] == 1 ) {
                        return `<span class="text-red" title="Протокол неактуален">${item['NUMBER_AND_YEAR']}</span>`
                    }
                    return `${item['NUMBER_AND_YEAR']}`
                }
            },
            {
                data: 'DATE'
            },
            {
                data: 'date_end_trials'
            },
            {
                data: 'PROTOCOL_SEND_DATETIME'
            },
            {
                data: 'COMPANY_TITLE',
                render: $.fn.dataTable.render.ellipsis(45, true)
            },
            {
                data: 'MATERIAL',
                render: $.fn.dataTable.render.ellipsis(40, true)
            },
            {
                data: 'ASSIGNED',
                render: $.fn.dataTable.render.ellipsis(40, true)
            },
            {
                data: 'requestTitle',
                class: 'text-nowrap',
                render: function (data, type, item) {
                    return `<a class="request-link"
                           href="/ulab/request/card/${item.ID_Z}" target="_blank">
                           ${item['REQUEST_TITLE']}
                        </a>`
                }
            },
            {
                data: 'tz',
                render: function (data, type, item) {
                    if ( item['ID_Z'] < 9357 ) {
                        return `<a class="number-tz" href="/ulab/requirement/card_old/${item['b_tz_id']}" target="_blank">
                                ${item['b_tz_id']}
                            </a>`
                    }
                    return `<a class="number-tz" href="/ulab/requirement/card/${item['b_tz_id']}" target="_blank">
                                ${item['b_tz_id']}
                            </a>`
                }
            },
            // {
            //     data: 'DOGOVOR_TABLE',
            // },
            // {
            //     data: 'PRICE',
            //     render: $.fn.dataTable.render.intlNumber('ru-RU', { minimumFractionDigits: 2 })
            // },
            // {
            //     data: 'ACCOUNT',
            //     render: function (data, type, item) {
            //         return item.ACCOUNT
            //     }
            // },
            // {
            //     data: 'DATE_OPLATA',
            //     render: function (data, type, item) {
            //         return item.DATE_OPLATA
            //     }
            // },
            {
                data: 'linkName',
                orderable: false,
                render: function (data, type, item) {
                    if ( item['PROTOCOL_OUTSIDE_LIS'] == 1 ) {
                        return 'Вне ЛИМС'
                    }
                    return `<a class="results-link"
                               href="/ulab/result/card_new/${item['ID_Z']}?protocol_id=${item['protocol_id']}" target="_blank">
                               Открыть
                            </a>`
                }
            },
            {
                data: 'DOC',
                render: function (data, type, item) {
                    if ( item['DOC'] !== '' ) {
                        return `<a class="results-link"
                               href="${item['DOC']}" target="_blank">
                               PDF
                            </a>`
                    }

                    return ''
                },
                orderable: false
            },
            {
                data: 'USER_HISTORY',
                class: 'text-nowrap',
                render: $.fn.dataTable.render.ellipsis(25, true)
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
        order: [[ 3, "desc" ]],
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

    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $journal.width()

    $('.btnRightTable, .arrowRight').hover(function() {
        container.animate(
            {
                scrollLeft: scroll
            },
            {
                duration: 4000, queue: false
            }
        )
    },
    function() {
        container.stop();
    })

    $('.btnLeftTable, .arrowLeft').hover(function() {
        container.animate(
            {
                scrollLeft: -scroll
            },
            {
                duration: 4000, queue: false
            }
        )
    },
    function() {
        container.stop();
    })

    $(document).scroll(function() {
        let positionScroll = $(window).scrollTop(),
            tableScrollBody = container.height()

        if (positionScroll > 265 && positionScroll < tableScrollBody) {
            $('.arrowRight').css('transform',`translateY(${positionScroll-260}px)`);
            $('.arrowLeft').css('transform',`translateY(${positionScroll-250}px)`);
        }
    })
})
