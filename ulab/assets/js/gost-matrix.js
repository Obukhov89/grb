$(function () {
    const $journal = $('#journal_matrix')
    const $trHeaderTitle = $journal.find('.header-title')
    const $trHeaderSearch = $journal.find('.header-search')
    let journalDataTable = null

    let columns = [
        {
            data: 'num_oa',
            width: '95px'
        },
        {
            data: 'reg_doc',
            render: function (data, type, item) {
                return `<a href="/ulab/gost/edit/${item.gost_id}">${item.reg_doc} ${item.clause}</a>`
            }
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
    ]

    let countCell = $trHeaderTitle.find('th').length

    let centerCell = [0]

    $.ajax({
        url: "/ulab/gost/getLabAndUserAjax/",
        async: false,
        // dataType: "json",
        success: function (data) {
            data = JSON.parse(data)

            $.each(data, function (i, val) {

                if ( val.users === undefined ) {
                    return true
                }

                let objectUsers = Object.keys(val.users)
                let countUser = objectUsers.length

                $trHeaderTitle.find('th:last-child').after(`<th class="text-center  lab_${val.lab_id} lab_visible" scope="col" colspan="${countUser}">${val.short_name}</th>`)

                $.each(val.users, function (k, user) {
                    $trHeaderSearch.find('th:last-child').after(`<th scope="col" class="text-nowrap lab_${val.lab_id} lab_visible">${user.short_name}</th>`)

                    centerCell.push(countCell++)

                    columns.push(
                        {
                            data: null,
                            className: `text-center lab_${val.lab_id} lab_visible`,
                            orderable: false,
                            render: function (data, type, item) {
                                let check = item.assigned.includes(user.id) ? 'checked' : ''

                                return `<input class="form-check-input user_in_method" data-user_id="${user.id}" data-method_id="${item.method_id}" type="checkbox" value="1" ${check}>`
                            }
                        }
                    )
                })


            })

            /*journal requests*/
            journalDataTable = $journal.DataTable({
                bAutoWidth: false,
                autoWidth: false,
                processing: true,
                serverSide: true,
                ajax: {
                    type : 'POST',
                    data: function ( d ) {
                        d.stage = $('#selectStage option:selected').val()
                        // d.lab = $('#selectLab option:selected').val()
                    },
                    url : '/ulab/gost/getJournalMatrixAjax/',
                    dataSrc: function (json) {
                        return json.data
                    }
                },
                columns: columns,
                language: dataTablesSettings.language,
                lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
                pageLength: 25,
                order: [[ 0, "desc" ]],
                fixedColumns: {
                    leftColumns: 3
                },
                dom: 'frtB<"bottom"lip>',
                buttons: dataTablesSettings.buttons,
                bSortCellsTop: true,
                scrollX: true,
                fixedHeader: true,
            });

            journalDataTable.columns().every( function () {
                $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
                    journalDataTable
                        .column( $(this).parent().index() )
                        .search( this.value )
                        .draw();
                })
            })


            $('.filter').on('change', function () {
                journalDataTable.ajax.reload()
                journalDataTable.draw()
            })

            $('#workarea-content').on('change', '.user_in_method', function () {
                const userId = $(this).data('user_id')
                const methodId = $(this).data('method_id')

                $.ajax({
                    url: "/ulab/gost/setAssignedAjax/",
                    method: "POST",
                    data: {
                        user_id: userId,
                        method_id: methodId
                    },
                    success: function (data) {
                        journalDataTable.ajax.reload()
                        journalDataTable.draw()
                    }
                })

                return true
            })
        }
    })

    $('#selectLab').on('change', function () {
        let labId = $(this).val()

        if ( labId == 0 ) {
            $('body').find('.lab_visible').show()
        } else {
            $('body').find('.lab_visible').hide()
            $('body').find(`.lab_${labId}`).show()
        }

        journalDataTable.columns.adjust()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

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
