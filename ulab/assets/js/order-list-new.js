$(function ($) {
    let $journal = $('#journal_order')


    /*journal requests*/
    let journalDataTable = $journal.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.dateStart = $('#inputDateStart').val()
                d.dateEnd = $('#inputDateEnd').val()
                d.everywhere = $('#filter_everywhere').val()
                d.need_date_end = $('.filter-need-end').val()
                d.need_note = $('.filter-need-note').val()
                d.originals = $('.filter-originals').val()
            },
            url : '/ulab/order/getListProcessingAjax/',
            dataSrc: function (json) {
                return json.data
            },
        },
        columns: [
            {
                data: 'titleStage',
                width: '52px',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="stage rounded ${item['bgStage']}" title="${item['titleStage']}"></div>`
                }
            },
            {
                data: 'NUMBER',
                width: '100px',
                render: function (data, type, item) {
                    return `<a class="results-link"
                               href="/ulab/order/card/${item['d_id']}" target="_blank">
                               ${item['NUMBER']}
                            </a>`
                }
            },
            {
                data: 'DATE',
                width: '100px'
            },
            {
                data: 'COMPANY_TITLE',
                width: '100%',
                render: $.fn.dataTable.render.ellipsis(45, true)
            },
            {
                data: 'type_dogovor',
            },
            {
                data: 'belonging',
            },
            {
                data: 'payment_type',
            },
            {
                data: 'price_dogovor',
            },
            {
                data: 'document_dogovor',
                orderable: false,
                render: function (data, type, item) {
                    if(item['document_dogovor'].length !== 0) {
                        let stroke = ''

                        if(item['document_dogovor']['scan']['data']) {
                            stroke = item['document_dogovor']['scan']['data'].join(', ')
                        }else {
                            stroke = '--'
                        }

                        return `
                            <p>${stroke}</p>
                        `
                    }else {
                        return `<p>--</p>`
                    }
                }
            },
            {
                data: 'document_dogovor',
                orderable: false,
                render: function (data, type, item) {
                    if(item['document_dogovor'].length !== 0) {
                        let stroke = ''

                        if(item['document_dogovor']['original']['data']) {
                            stroke = item['document_dogovor']['original']['data'].join(', ')
                        }else {
                            stroke = '--'
                        }

                        return `
                            <p>${stroke}</p>
                        `
                    }else {
                        return `<p>--</p>`
                    }
                }
            },
            {
                data: 'document_dogovor',
                orderable: false,
                render: function (data, type, item) {

                    if(item['document_additional'].length !== 0) {
                        let stroke = ''

                        if(item['document_additional']['scan']['data']) {
                            stroke = item['document_additional']['scan']['data'].join(', ')
                        }else {
                            stroke = '--'
                        }

                        return `
                            <p>${stroke}</p>
                        `
                    }else {
                        return `<p>--</p>`
                    }
                }
            },
            {
                data: 'document_dogovor',
                orderable: false,
                render: function (data, type, item) {
                    if(item['document_additional'].length !== 0) {
                        let stroke = ''

                        if(item['document_additional']['original']['data']) {
                            stroke = item['document_additional']['original']['data'].join(', ')
                        }else {
                            stroke = '--'
                        }

                        return `
                            <p>${stroke}</p>
                        `
                    }else {
                        return `<p>--</p>`
                    }
                }
            },
            {
                data: 'document_dogovor',
                orderable: false,
                render: function (data, type, item) {
                    if(item['document_protocol'].length !== 0) {
                        let stroke = ''

                        if(item['document_protocol']['scan']['data']) {
                            stroke = item['document_protocol']['scan']['data'].join(', ')
                        }else {
                            stroke = '--'
                        }

                        return `
                            <p>${stroke}</p>
                        `
                    }else {
                        return `<p>--</p>`
                    }
                }
            },
            {
                data: 'document_dogovor',
                orderable: false,
                render: function (data, type, item) {
                    if(item['document_protocol'].length !== 0) {
                        let stroke = ''

                        if(item['document_protocol']['original']['data']) {
                            stroke = item['document_protocol']['original']['data'].join(', ')
                        }else {
                            stroke = '--'
                        }

                        return `
                            <p>${stroke}</p>
                        `
                    }else {
                        return `<p>--</p>`
                    }
                }
            },
            {
                data: 'dogovor_end',
            },
            {
                data: 'special_notes'
            }
        ],
        createdRow: function(row, data, dataIndex) {
            if(typeof data['document_additional']['original'] === 'object' && data['document_additional']['original'] !== null && data['need_additional']) {
                $('td:eq(11)', row).css('background-color', data['document_additional']['original']['bg-color'])
            }

            if(typeof data['document_additional']['scan'] === 'object' && data['document_additional']['scan'] !== null && data['need_additional']) {
                $('td:eq(10)', row).css('background-color', data['document_additional']['scan']['bg-color'])
            }

            if(typeof data['document_protocol']['original'] === 'object' && data['document_protocol']['original'] !== null && data['need_protocol']) {
                $('td:eq(13)', row).css('background-color', data['document_protocol']['original']['bg-color'])
            }

            if(typeof data['document_protocol']['scan'] === 'object' && data['document_protocol']['scan'] !== null && data['need_protocol']) {
                $('td:eq(12)', row).css('background-color', data['document_protocol']['scan']['bg-color'])
            }

            console.log(data)

            if(data['dogovor_end_color']) {
                $('td:eq(13)', row).css('background-color', data['dogovor_end_color'])
            }

            $('td:eq(9)', row).css('background-color', data['document_dogovor']['original']['bg-color'])
            $('td:eq(8)', row).css('background-color', data['document_dogovor']['scan']['bg-color'])

            $('td:eq(4)', row).css('text-wrap', 'nowrap')

            if(data['IS_ACTION'] === '0') {
                $(row).css('background-color', '#d94e5e')

                $('td:eq(9)', row).css('background-color', '#d94e5e')
                $('td:eq(8)', row).css('background-color', '#d94e5e')
                $('td:eq(12)', row).css('background-color', '#d94e5e')
                $('td:eq(11)', row).css('background-color', '#d94e5e')
                $('td:eq(10)', row).css('background-color', '#d94e5e')
                $('td:eq(13)', row).css('background-color', '#d94e5e')
            }
        },
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
        order: [[ 1, "desc" ]],
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
        paging: true,
        fixedHeader:   false,
        fixedColumns: {
            left: 7
        }
    });

    journalDataTable.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            journalDataTable
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        })


    })

    let observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutationRecord) {
            for(let i = 1; i <= 7; i++) {
                setActualLeftValue(i)
            }
        })
    })

    function setActualLeftValue(index) {
        let needleValue = document.querySelector(`.fix-col-${index}`).style.left

        $(`.fix-search-${index}`).css('left', needleValue)
    }

    let target = document.getElementsByClassName('fix-col-1')[0]

    console.log(target)

    observer.observe(target, {attributes: true, attributeFilter: ['style']})

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

    let $body = $("body")
    let $containerScroll = $body.find('.dataTables_scroll')
    let $thead = $('.journal thead tr:first-child')

    $(document).scroll(function() {
        let positionScroll = $(window).scrollTop(),
            tableScrollBody = container.height(),
            positionTop = $containerScroll.offset().top

        if ( positionScroll >= positionTop ) {
            $thead.attr('style', 'position:fixed;top:0;z-index:99')
        } else {
            $thead.attr('style', '')
        }

        if (positionScroll > 265 && positionScroll < tableScrollBody) {
            $('.arrowRight').css('transform',`translateY(${positionScroll-260}px)`);
            $('.arrowLeft').css('transform',`translateY(${positionScroll-250}px)`);
        }
    })

    $('#add-new-dogovor').on('click', function () {
        $.magnificPopup.open({
            items: {
                src: '#new_dogovor',
                type: 'inline',
            },
            fixedContentPos: false,
            closeOnBgClick: false
        })
    })

    $.ajax({
        url: '/ulab/order/getAllClientsAjax/',
        data: {},
        method: 'POST',
        dataType: 'json',
        success: function (data) {
            $.each(data, function (i, v) {
                $('.client-select').append(
                    `
                        <option value="${v['ID']}">${v['TITLE']}</option>
                    `
                )
            })

            $('.client-select').select2({
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

    $.ajax({
        url: '/ulab/order/getNextDogovorNumberAjax/',
        data: {},
        method: 'POST',
        dataType: 'json',
        success: function (data) {
            $('#number').val(data['number'])
            $('#next_number').val(data['number'])
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

    $('#number').on('change', function () {
        $('#custom_number').val(1)
    })

    $(".visible-create").click(function (e) {
        $(".form-create").toggle(500)
    })

    $("#saveCompany").click(function (e) {
        $.ajax({
            url: '/ulab/secondment/addCompanyAjax/',
            data: $("#company input").serialize(),
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                $('#client_id').append(`<option value="${data.id}">${data.name}</option>`)
                $('#client_id').val(data.id).change();
                $(".form-create").toggle(300)
                // $('[data-js-clients]').append(`<option value="${data.id}">${data.name}</option>`)
                // $("[data-js-clients]").val(data.id).change();
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
    });
})
