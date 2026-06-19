$(function () {
    /** modal */
    $('.popup-with-form1').magnificPopup({
        items: {
            src: '#method-modal-form',

            type: 'inline'
        },
        closeOnBgClick: true,
        fixedContentPos: false
    })

    $('.select2').select2({
        theme: 'bootstrap-5'
    })

    let $journal = $('#table-method')

    /*journal requests*/
    let journalDataTable = $journal.DataTable({
        bAutoWidth: false,
        autoWidth: false,
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.id = $('#gost-id').val()
            },
            url : '/ulab/gost/getListMethodByGostAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'is_confirm',
                orderable: true,
                render: function (data, type, item) {
                    if (item.is_confirm == 1) {
                        return `<span class="text-green" title="Методика потверждена"><i class="fa-regular fa-circle-check"></i></span>`
                    } else {
                        return `<span class="text-red" title="Методика не потверждена"><i class="fa-regular fa-circle-xmark"></i></span>`
                    }
                }
            },
            {
                data: 'name',
                render: $.fn.dataTable.render.ellipsis(50, true)
            },
            {
                data: 'clause',
            },
            {
                data: 'test_method_name',
                render: $.fn.dataTable.render.ellipsis(50, true)
            },
            {
                data: 'unit_rus',
                render: $.fn.dataTable.render.ellipsis(50, true)
            },
            {
                data: 'in_field',
                render: function (data, type, item) {
                    if (item.in_field == 1) {
                        return 'Да'
                    } else {
                        return 'Нет'
                    }
                }
            },
            {
                data: 'is_extended_field',
                render: function (data, type, item) {
                    if (item.is_extended_field == 1) {
                        return 'Да'
                    } else {
                        return 'Нет'
                    }
                }
            },
            {
                data: 'buttons',
                render: function (data, type, item) {
                    return `<div class="text-end d-flex justify-content-around">
                                <a
                                        href="/ulab/gost/method/${item['id']}"
                                        class="btn btn-success btn-square me-1"
                                        title="Редактировать методику">
                                    <i class="fa-solid fa-pencil icon-fix"></i>
                                </a>
                                <form action="/ulab/gost/copyMethod/" method="post">
                                    <input type="hidden" name="method_id" value="${item['id']}">
                                    <input type="hidden" name="gost_id" value="${item['gost_id']}">
                                    <button
                                            type="submit"
                                            class="btn btn-primary btn-square"
                                            title="Скопировать методику">
                                        <i class="fa-regular fa-copy icon-fix"></i>
                                    </button>
                                </form>
                            </div>`
                }
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
        order: [[ 2, "asc" ]],
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
        fixedHeader:   false,
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
