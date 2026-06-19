$(function ($) {
    let $journal = $('#journal_probe')

    /*journal requests*/
    let journalDataTable = $journal.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.dateStart = $('#inputDateStart').val()
                d.dateEnd = $('#inputDateEnd').val()
                d.lab = $('#selectLab option:selected').val()
                d.stageGroup = $('#selectStageGroup option:selected').val()
                d.selectIrrevocable = $('#selectIrrevocable option:selected').val()
                d.selectAcceptProbe = $('#selectAcceptProbe option:selected').val()
            },
            url : '/ulab/probe/getListAjax/',
            dataSrc: function (json) {
                console.log(json)
                return json.data
            }
        },
        columns: [
            {
                data: 'NUM_ACT_TABLE'
            },
            {
                data: 'CIPHER',
                render: $.fn.dataTable.render.ellipsis(40, true)
            },
            {
                data: 'DOGOVOR_TABLE'
            },
            {
                data: 'DATE_ACT'
            },
            {
                data: 'COMPANY_TITLE'
            },
            {
                data: 'MATERIAL',
                render: $.fn.dataTable.render.ellipsis(40, true)
            },
            {
                data: 'ASSIGNED'
            },
            {
                data: 'REQUEST_TITLE',
                class: 'text-nowrap',
                render: function (data, type, item) {
                    return `<a class="request-link"
                           href="/ulab/request/card/${item.ID_Z}" target="_blank">
                           ${item['REQUEST_TITLE']}
                        </a>`
                }
            },
            {
                data: 'LAB',
                orderable: false,
            },
            {
                data: 'PROTOCOLS',
                orderable: false,
            },
            {
                data: 'PROTOCOLS_TEST',
                defaultContent: '', // Если данных нет - подставит пустую строку
                orderable: false,  // Отключаем сортировку (как у PROTOCOLS)
                render: function(data, type, row) {
                    // Если данных нет или это null/undefined - возвращаем пустую строку
                    return data ? data : '';
                }
            },
            {
                data: 'irrevocable',
                defaultContent: '', // Если данных нет - подставит пустую строку
                orderable: false,  // Отключаем сортировку (как у PROTOCOLS)
                render: function(data, type, row) {
                    // Если данных нет или это null/undefined - возвращаем пустую строку
                    return data ? data : '';
                }
            }
            ,
            {
                data: 'accept_probe',
                defaultContent: '', // Если данных нет - подставит пустую строку
                orderable: false,  // Отключаем сортировку (как у PROTOCOLS)
                render: function(data, type, row) {
                    // Если данных нет или это null/undefined - возвращаем пустую строку
                    return data ? data : '';
                }
            }
        ],
        language: dataTablesSettings.language,
        lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
        pageLength: 25,
        order: [[ 0, "desc" ]],
        colReorder: true,
        dom: 'frtB<"bottom"lip>',
        buttons: dataTablesSettings.buttons,
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

    $('body').on('click', '.accept_probe', function () {
        console.log($(this))
    })


})
