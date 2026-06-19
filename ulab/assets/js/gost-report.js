$(function () {
    let $journal = $('#journal_gost')

    /*journal requests*/
    let journalDataTable = $journal.DataTable({
        retrieve: true,
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
            data: function ( d ) {
                d.dateStart = $('#inputDateStart').val()
                d.dateEnd = $('#inputDateEnd').val()
                d.stage = $('#selectStage option:selected').val()
                d.lab = $('#selectLab option:selected').val()
            },
            url : '/ulab/gost/getJournalReportAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'reg_doc',
                render: function (data, type, item) {
                    return `<a href="/ulab/gost/edit/${item.gost_id}">${item.reg_doc}</a>`
                }
            },
            {
                data: 'clause'
            },
            // {
            //     data: 'description',
            //     render: $.fn.dataTable.render.ellipsis(32, true)
            // },
            {
                data: 'materials',
                render: $.fn.dataTable.render.ellipsis(32, true)
            },
            {
                data: 'name',
                render: function (data, type, item) {
                    if ( item.method_id === null ) {
                        return `Методик не добавлено`
                    }
                    if ( item.mp_name === null ) {
                        return `<a href="/ulab/gost/method/${item.method_id}">${item.name}</a>`
                    }
                    return `<a href="/ulab/gost/method/${item.method_id}">${item.mp_name}</a>`
                }
            },
            {
                data: 'count_method'
            },
            {
                data: 'count_vlk',
                orderable: false,
            },
            {
                data: 'in_field',
                orderable: false,
                render: function (data, type, item) {
                    if (item['in_field'] == 1) {
                        return 'Да'
                    }
                    return 'Нет'
                }
            },
        ],
        language: dataTablesSettings.language,
        lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
        pageLength: 25,
        order: [[ 4, "desc" ]],
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

        $(".dtfh-floatingparenthead tr:first-child th")
            .css("padding-inline", "0px")

        if (positionScroll > 265 && positionScroll < tableScrollBody) {
            $('.arrowRight').css('transform',`translateY(${positionScroll-260}px)`);
            $('.arrowLeft').css('transform',`translateY(${positionScroll-250}px)`);
        }
    })



})