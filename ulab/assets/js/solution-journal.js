$(function ($) {
    /*solution journal*/
    let solutionJournal = $('#solution_journal').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.dateStart = $('#inputDateStart').val()
                d.dateEnd = $('#inputDateEnd').val()
            },
            url : '/ulab/solution/getListProcessingAjax/',
            dataSrc: function (json) {
                return json.data
            },
        },

        columns: [
            {
                data: 'Name'
            },
            {
                data: 'name_recipe',
                render: function (data, type, item) {
                    // return `<a href="/ulab/recipe/list?param=1"
                    return `<a href="/ulab/recipe/list/${item['name_recipe']}" 
                                >${item['name_recipe']}                 
                            </a>`
                }
            },
            {
                data: 'date_preparation'
            },
            {
                data: 'id_people'
            },
        ],
        'columnDefs': [{
            'targets': [0,1,2,3],
            'orderable': false,
        }],
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
        order: [],
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
        bSortCellsTop: false,
        scrollX:       true,
        fixedHeader:   false,
    })

    solutionJournal.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            solutionJournal
                .column( $(this).parent().index() )
                .search( this.value )
                .draw()
        })
    })


    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $('#solution_journal').width()

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
            container.stop()
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
            container.stop()
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
            $('.arrowRight').css('transform',`translateY(${positionScroll-260}px)`)
            $('.arrowLeft').css('transform',`translateY(${positionScroll-250}px)`)
        }
    })
    $('#solution_journal').on("click","tbody tr",function (){
        let table = $('#solution_journal'). DataTable()
        let clickOn = table.columns(this).data()

       console.log(clickOn)
    })

    /** modal */
    $('.popup-with-form').magnificPopup({
        items: {
            src: '#add-entry-modal-form',
            type: 'inline'
        },
        fixedContentPos: false
    })

    $('.select-recipe').select2({
        placeholder: 'Выбирете рецепт',
        width: '100%'
    })


    /** journal filters */
    $('.filter-btn-search').on('click', function () {
        $('#journal_requests_filter').addClass('is-open')
        $('.filter-btn-search').hide()
    })

    $('.filter').on('change', function () {
        solutionJournal.ajax.reload()
        solutionJournal.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })
})
