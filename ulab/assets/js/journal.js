$(function ($) {
    /*journal requests*/
    let journalRequests = $('#journal_requests').DataTable({
        destroy: true,
        ajax: {
            type : 'GET',
            url : '/ulab/request/getListAjax/',
            dataSrc: function (json) {
                return json;
            }
        },
        createdRow: function(row, data, dataIndex) {
            $(row).find('td:eq(0)').attr('data-lab', data.LABA_ID);
            $('td:eq(11)', row).addClass(data.bgPrice);
            $(row).addClass(data.bgCheck);
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
                data: 'requestTitle',
                class: 'text-nowrap',
                render: function (data, type, item) {
                    if (type === 'display') {
                        return `<a class="request-link"
                               href="/ulab/request/card/${item.ID_Z}?ACT_NUM=${item['ACT_NUM']}" target="_blank">
                               ${item['REQUEST_TITLE']}${item['certificate']}
                            </a>`
                    }

                    if (type === 'filter') {
                        return item['REQUEST_TITLE']
                    }

                    return item.request
                }
            },
            {
                data: 'DATE_CREATE_TIMESTAMP',
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return item.dateCreateRu
                    }
                    return data;
                },
            },
            {
                data: 'COMPANY_TITLE',
                render: $.fn.dataTable.render.ellipsis(45, true)
            },
            {
                data: 'DEADLINE_TABLE',
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return `<span class="${item.textColor}">${item.DEADLINE_TABLE}</span>`
                    }

                    return item.deadlineISO
                },
            },
            {
                data: 'ACCOUNT',
                render: function (data, type, item) {
                    return item.ACCOUNT
                },
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
                data: 'NUM_ACT_TABLE',
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return `<a class="number-act" href="/probe.php?ID=${item['ID_Z']}&1" target="_blank">
                                ${item['NUM_ACT_TABLE']}
                            </a>`
                    }

                    return item.ACT_NUM
                },
            },
            {
                data: 'tz',
                render: function (data, type, item) {
                    return `<a class="number-tz" href="/tz_show.php?ID=${item['b_tz_id']}" target="_blank">
                                ${item['b_tz_id']}
                            </a>`
                },
            },
            {
                data: 'DOGOVOR_TABLE',
                class: 'text-nowrap',
                render: $.fn.dataTable.render.ellipsis(30, true)
            },
            {
                data: 'PRICE',
                render: $.fn.dataTable.render.intlNumber('ru-RU', { minimumFractionDigits: 2 })
            },
            {
                data: 'DATE_OPLATA',
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return item.DATE_OPLATA
                    }

                    return item.dateOplataISO
                },
            },
            {
                data: 'linkName',
                orderable: false,
                render: function (data, type, item) {
                    return `<a class="results-link"
                               href="/results_isp.php?ID=${item['ID_Z']}&ID_P=${item['firstProtocolId']}" target="_blank">
                                ${item['linkName']}
                            </a>`
                },
            },
            {
                data: 'PROTOCOLS',
                orderable: false,
                render: function (data, type, item) {
                    let dataProtocol = `<div class="d-flex flex-column">`

                    if (item['PROTOCOLS']) {
                        for (const val of item['PROTOCOLS']) {

                            for (const file of val['FILES']) {
                                if (file.indexOf('.pdf') !== -1) {
                                    dataProtocol += `<a class="protocol-link" 
                                                        href="/protocol_generator/archive/${item['b_id']}${val['YEAR']}/${val['ID']}/${file}"
                                                        target="_blank">
                                                        ${val['NUMBER_AND_YEAR']}
                                                    </a>`
                                }
                            }

                            if (val['PROTOCOL_OUTSIDE_LIS'] && val['PDF']) {
                                dataProtocol += `<a class="protocol-link" href="/pdf/${val['ID']}/${val['PDF']}" target='_blank'
                                                    target="_blank">
                                                    ${val['NUMBER_AND_YEAR']}
                                                </a>`
                            }
                        }
                    } else {
                        if(item['NO_BITRIX']) {
                            dataProtocol += `<a class="protocol-link" href="/pdf/${item['PDF']}" target='_blank'>
                                                ${item['PDF'] && item['NUM_P_TABLE'] ? item['NUM_P_TABLE'] : ''}
                                            </a>`
                        } else if (item['b_actual_ver']) {
                            dataProtocol += `<a class="protocol-link" href="/protocol_generator/archive/${item['b_id']}${item['YEAR_ACT']}/${item['b_actual_ver']}.docx?1&1" target='_blank'>
                                                ${item['RESULTS'] && item['NUM_P_TABLE'] ? item['NUM_P_TABLE'] : ''}
                                            </a>`
                        }
                    }
                    dataProtocol += `</div>`

                    return dataProtocol
                },
            },
            {
                data: 'MANUFACTURER_TITLE',
                class: 'text-nowrap',
                render: $.fn.dataTable.render.ellipsis(25, true)
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
        pageLength: 50,
        order: [[ 2, "desc" ]],
        colReorder: true,
        dom: 'fBrt<"bottom"lip>',
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
        initComplete : function() {
            $("#journal_requests_filter").detach().appendTo('#filter_search');
            $('input', '#journal_requests_filter').addClass('form-control');
        }
    });

    journalRequests.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            journalRequests
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        })
    })


    /*journal filters*/
    $('.filter-btn-search').on('click', function () {
        $('#journal_requests_filter').addClass('is-open')
        $('.filter-btn-search').hide()
    })

    $.fn.dataTable.ext.search.push(
        function(settings, data, dataIndex, rowData, test) {
            let min = $('#inputDateStart').val();
            let max = $('#inputDateEnd').val();
            let date = rowData.DATE_CREATE_TIMESTAMP;

            if (min == "" && max == "") {
                return true;
            }
            if (min == "" && date <= max) {
                return true;
            }
            if (max == "" && date >= min) {
                return true;
            }
            if (date <= max && date >= min) {
                return true;
            }
            return false;
        }
    )

    $('#inputDateStart, #inputDateEnd').on('change', function () {
        journalRequests.draw()
    })

    $("#selectStage").on('change', function() {

        $.fn.dataTable.ext.search.push( (settings, data, dataIndex, rowData ) => {

            if ($(this).val() === '0') {
                return true

            } else if ($(this).val() === '1') {
                return !rowData.ACT_NUM &&
                    $.inArray(rowData.STAGE_ID, ['NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING']) !== -1

            } else if ($(this).val() === '2') {
                return rowData.ACT_NUM &&
                    $.inArray(rowData.STAGE_ID, ['NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE']) !== -1

            } else if ($(this).val() === '3') {
                return rowData.STAGE_ID === '1'

            } else if ($(this).val() === '4') {
                return $.inArray(rowData.STAGE_ID, ['2', '3', '4']) !== -1

            } else if ($(this).val() === '5') {
                return $.inArray(rowData.STAGE_ID, ['5', '6', '7', '8', '9', 'LOSE']) !== -1

            } else if ($(this).val() === '6') {
                return rowData.PRICE && !rowData.OPLATA &&
                    $.inArray(rowData.STAGE_ID, ['LOSE', '5', '6', '7', '8', '9', '10', '11', '12', '13']) === -1

            } else if ($(this).val() === '7') {
                return rowData.OPLATA && (rowData.OPLATA < rowData.PRICE)

            } else if ($(this).val() === '8') {
                return rowData.OPLATA > rowData.PRICE

            } else if ($(this).val() === '9') {
                return rowData.OPLATA === rowData.PRICE

            } else if ($(this).val() === '10') {
                return $.inArray(rowData.STAGE_ID, ['NEW', 'PREPARATION', 'PREPAYMENT_INVOICE', 'EXECUTING', 'FINAL_INVOICE', '1', '2', '3', '4', 'WON'])  !== -1 &&
                    rowData.ACT_NUM

            } else if ($(this).val() === '11') {
                return rowData.STAGE_ID === 'WON'
            }
        });

        journalRequests.draw()
    })

    $("#selectLab").on('change', function() {
        $.fn.dataTable.ext.search.push( (settings, data, dataIndex, rowData ) =>{

            let $trDetailDT = $(journalRequests.row(dataIndex).node())
            let lab = $trDetailDT.find('td:eq(0)').data("lab")
            lab = lab ? lab.toString() : '';

            if (this.value === '0') {
                return true;
            }

            return lab.indexOf(this.value) !== -1;
        });
        journalRequests.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $('#journal_requests').width()

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

    /*journal modal window*/
    if (window.location.pathname === '/ulab/request/list/') {

        $.ajax({
            url: '/ulab/request/getCheckTzAjax/',
            method: 'GET',
            dataType: 'json',
            success: function (data) {
                let content = `<table class="table table-striped table-modal"><tbody>`;

                data.forEach(function (item) {
                    //TODO: Изменить ссылку на крточку заявки
                    content  += `<tr class="align-middle text-nowrap">
                                    <td>
                                        <span title="${item.REQUEST_TITLE}">${item.cropRequestTitle}</span>
                                    </td>
                                    <td>
                                        <span title="${item.COMPANY_TITLE}">${item.cropCompanyTitle}</span>
                                    </td>
                                    <td class="${item.bgColor}">
                                        <span title="${item.status}">${item.cropStatus}</span>
                                    </td>
                                    <td>
                                        <!--a href="/request_card.php?ID=${item.ID_Z}" target="_blank">
                                            <div class="card-icon" title="Карточка заявки"></div>
                                        </a-->
                                        <a href="/ulab/request/card/${item.ID_Z}" target="_blank">
                                            <div class="card-icon" title="Карточка заявки"></div>
                                        </a>
                                    </td>
                                </tr>`;
                })

                content += `</tbody></table>`;

                let modal = $modal({
                    title: 'Техническое задание на рассмотрении',
                    content: content,
                    footerButtons: [
                        {
                            class: 'btn btn__ok',
                            text: 'ОК',
                            handler: 'modalHandlerOk'
                        }
                    ]
                });

                modal.show();
            }
        });
    }
})