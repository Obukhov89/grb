$(function ($) {
    let $journal = $('#journal_oborud')

    /*journal requests*/
    let journalDataTable = $journal.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.stage = $('#selectStage option:selected').val()
                d.lab = $('#selectLab option:selected').val()
                d.everywhere = $('#filter_everywhere').val()
            },
            url : '/ulab/oborud/getListProcessingAjax/',
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
                data: 'NAME',
                render: $.fn.dataTable.render.ellipsis(50, true)
            },
            {
                data: 'OBJECT',
                render: function (data, type, item) {
                    return `<a href="/ulab/oborud/edit/${item['ID']}">${item['OBJECT']}</a>`
                }
            },
            {
                data: 'TYPE_OBORUD',
                render: $.fn.dataTable.render.ellipsis(50, true)
            },
            {
                data: 'IDENT',
                render: $.fn.dataTable.render.ellipsis(50, true)
            },
            {
                data: 'FACTORY_NUMBER',
                render: $.fn.dataTable.render.ellipsis(50, true)
            },
            {
                data: 'REG_NUM'
            },
            {
                data: 'god_vvoda_expluatation'
            },
            {
                data: 'measuring_range',
                render: $.fn.dataTable.render.ellipsis(50, true)
            },
            {
                data: 'сlass_precision_and_accuracy',
                render: $.fn.dataTable.render.ellipsis(50, true)
            },
            {
                data: 'certificate_name',
                render: $.fn.dataTable.render.ellipsis(50, true)
            },
            {
                data: 'date_start',
                width:"125px"
            },
            {
                data: 'date_end',
                width:"125px"
            },
            {
                data: 'POVERKA_PLACE'
            },
            {
                data: 'property_rights'
            },
            {
                data: 'laba_name'
            },
            {
                data: 'room'
            },
            {
                data: 'IN_AREA',
                render: function (data, type, item) {
                    if (item['IN_AREA'] == 1) {
                        return 'В области'
                    }
                    return 'Не в области'
                }
            },
            {
                data: 'CHECKED',
                render: function (data, type, item) {
                    if (item['CHECKED'] == 1) {
                        return 'Да'
                    }
                    return 'Нет'
                }
            },
            {
                data: 'manufacturer',
                render: $.fn.dataTable.render.ellipsis(50, true)
            },
            {
                data: 'note',
                render: $.fn.dataTable.render.ellipsis(40, true)
            }
        ],
        language: dataTablesSettings.language,
        lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
        pageLength: 25,
        order: [[ 2, "asc" ]],
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
})
