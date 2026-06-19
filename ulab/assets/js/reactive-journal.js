$(function ($) {
    /*recipe journal*/

    var recipeJournal = $('#recipe_journal').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            data: function (d) {
                d.dateStart = $('#inputDateStart').val()
                d.dateEnd = $('#inputDateEnd').val()
                // d.stage = $('#selectStage option:selected').val()
                // d.lab = $('#selectLab option:selected').val()
                d.everywhere = $('#filter_everywhere').val()
            },
            url: '/ulab/reactive/getListProcessingAjax/',
            dataSrc: function (json) {
                return json.data
            },
        },
        columns: [
            {
                data: 'name',
                width: 300,
            },
            {
                data: 'number_batch',
                width: 100,
                render: function (data, type, item) {
                    return `<a class="control-link"
                               data-id="${item['id']}">
                               ${item['number_batch']}
                            </a>`
                },
            },
            {
                data: 'doc_receive',
                width: 300,
            },
            {
                data: 'date_receive',
                width: 150,
            },
            {
                data: 'full_quantity',
            },
            {
                data: 'date_production',
                width: 150,
            },
            {
                data: 'date_expired',
                width: 150,
            },
            {
                data: 'aggregate_name',
                width: 150,
            },
            {
                data: 'short_name',
                width: 100,
            },
            {
                data: 'doc_name',
                width: 300,
            },
            {
                data: 'lab_id',
            },
            {
                data: 'is_reactive',
                width: 150,
                render: function (data, type, item) {
                    if (item.is_reactive == 1) {
                        return 'Реактив'
                    } else if (item.is_reactive == 2) {
                        return 'Расходник'
                    }
                    return ''
                },
            },
            {
                data: 'personal_create',
                orderable: false,
                width: 200,
                render: function (data, type, item) {
                    return `${item['user_name']} ${item['user_last']}`
                },
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, item) {
                    return `<a class="popup-with-form-edit"
                               href="#edit-entry-modal-form-first"
                               data-id="${item['id']}" style="color: black">
                               <i class="fa fa-pencil"/>
                            </a>`
                },
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, item) {
                    return `<a class="popup-with-form-delete"
                               href="#delete-entry-modal"
                               data-id="${item['id']}" style="color: black">
                               <i class="fa fa-trash"></i>
                            </a>`
                },
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, item) {
                    return `<a class="popup-with-form-delete"
                               href="#delete-entry-modal"
                               data-id="${item['id']}" style="color: black">
                               <i class="fa-solid fa-plus"></i>
                            </a>`
                },
            },

        ],
        // responsive: {
        //     details: {
        //         type: 'column',
        //         target: 'tr'
        //     }
        // },
        // columnDefs: [{
        //     className: 'control',
        //     /*'targets': */
        //     'orderable': false,
        // }],
        language: {
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
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        pageLength: 25,
        order: [],
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
        scrollX: true,
        fixedHeader: true,
        "rowCallback": function (nRow, data) {
            if (data['is_precursor'] == 1) {
                $('td', nRow).css('background-color', '#dacfcf')
            }
        }

    })

    $('#recipe_journal').on('click', '.control-link', function () {
        var tr = $(this).closest('tr');
        var row = recipeJournal.row(tr);
        let id = $(this).data('id')

        if (row.child.isShown()) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        } else {
            // Open this row
            row.child(format(id)).show();
            tr.addClass('shown');
        }
    });

    function format(id) {
        let table = `<table class='table table-striped w-50'>
                        <thead>
                            <th>Дата списания</th>
                            <th>Цель списания</th>
                            <th>Ответственный</th>
                            <th>Количество</th>
                            <th></th>
                        </thead>
                        <tbody>
                            <tr class="total">
                                <td></td>
                                <td></td>
                                <td><strong>Остаток:</strong></td>
                                <td>
                                    <strong>123</strong>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th><button type="button" class="btn btn-danger add-control" data-id="${id}"><i class="fa-solid fa-minus"></i> списать</button></th>
                        </tfoot>
                     </table>`
        $.ajax( {
            url: '/ulab/reactive/getBatchControlAjax/',
            type: 'post',
            data: {
                id: id
            },
            dataType: 'json',
            success: function ( json ) {
                console.log(json)
            }
        } );
        // `d` is the original data object for the row
        return table;
    }

    recipeJournal.columns().every(function () {
        $(this.header()).closest('thead').find('.search:eq(' + this.index() + ')').on('keyup change clear', function () {
            recipeJournal
                .column($(this).parent().index())
                .search(this.value)
                .draw()
        })
    })

    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $('#recipe_journal').width()

    $('.btnRightTable, .arrowRight').hover(function () {
            container.animate(
                {
                    scrollLeft: scroll
                },
                {
                    duration: 4000, queue: false
                }
            )
        },
        function () {
            container.stop()
        })

    $('.btnLeftTable, .arrowLeft').hover(function () {
            container.animate(
                {
                    scrollLeft: -scroll
                },
                {
                    duration: 4000, queue: false
                }
            )
        },
        function () {
            container.stop()
        })

    let $body = $("body")
    let $containerScroll = $body.find('.dataTables_scroll')
    let $thead = $('.journal thead tr:first-child')

    $(document).scroll(function () {
        let positionScroll = $(window).scrollTop(),
            tableScrollBody = container.height(),
            positionTop = $containerScroll.offset().top

        if (positionScroll >= positionTop) {
            $thead.attr('style', 'position:fixed;top:0;z-index:99')
        } else {
            $thead.attr('style', '')
        }

        if (positionScroll > 265 && positionScroll < tableScrollBody) {
            $('.arrowRight').css('transform', `translateY(${positionScroll - 260}px)`)
            $('.arrowLeft').css('transform', `translateY(${positionScroll - 250}px)`)
        }
    })

    $("body").on('click', '.add-reactive', function () {

        $.each($('.select-reactive'), function () {
            $(this).select2('destroy');
        });
        let cloneReactive = $('.reactives[data-id = "1"]').clone()
        let countReactive = $('.reactives').length
        let countReactivePlusOne = countReactive + 1
        cloneReactive.attr('data-id', countReactivePlusOne)
        cloneReactive.find('select').attr('name', `reactives[reactive` + countReactivePlusOne + `][id_reactive]`)
        cloneReactive.find('input').attr('name', `reactives[reactive` + countReactivePlusOne + `][quantitya_reactive]`)
        $(`.reactives[data-id="${countReactive}"]`).after(cloneReactive)
        $.each($('.select-reactive'), function () {
            $(this).select2();
        });
    })

    $($body).on('change', '#reactive-name', function () {
        let id = $(this).val()

        $.ajax({
            method: 'POST',
            url: '/ulab/reactive/getReactiveInfoAjax',
            data: {
                id: id
            },
            dataType: 'json',
            success: function (data) {
                $('#reactive-nd').val(data.doc_name)
                $('#quality').val(`${data.rpName} (${data.short_name})`)
                $('#number').val(data.number)
                $('#id-reactive').val(data.rId)
            }
        })
    })

    $("body").on('click', '.del-reactive', function () {
        let countReactive = $('.reactives').length
        if (countReactive > 1) $(`.reactives[data-id="${countReactive}"]`).remove()
        else alert("Нельзя удалить единственный реактив")
    })
    /** modal */
    $('.popup-first').magnificPopup({
        items: {
            src: '#add-entry-modal-form-first',
            type: 'inline'
        },
        fixedContentPos: false,
        closeOnBgClick: false
    })
    $('.popup-second').magnificPopup({
        items: {
            src: '#add-entry-modal-form-second',
            type: 'inline'
        },
        fixedContentPos: false,
        closeOnBgClick: false
    })
    $('.popup-third').magnificPopup({
        items: {
            src: '#add-entry-modal-form-third',
            type: 'inline'
        },
        fixedContentPos: false
    })
    $('.select-reactive').select2({
        placeholder: 'Выбирете реактив',
        width: '100%',
    })

    /** journal filters */
    $('.filter-btn-search').on('click', function () {
        $('#journal_requests_filter').addClass('is-open')
        $('.filter-btn-search').hide()
    })

    $('.filter').on('change', function () {
        recipeJournal.ajax.reload()
        recipeJournal.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

    $('body').on('click', '.add-control', function() {
        let id = $(this).data('id')

            $.magnificPopup.open({
                items: {
                    src: '#add-control-modal',
                    type: 'inline',
                    fixedContentPos: false
                },
                closeOnBgClick: false
            });

        console.log(id)
    })

    //Временно сохранить
    /* $('.search:first').on('input', function () {
         let val = $(this).val()
         $('#param').val(val)
     })*/

})
