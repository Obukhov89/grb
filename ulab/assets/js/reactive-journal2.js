$(function ($) {

    /*reactive-journal*/
    let reactiveJournal = $('#reactive_journal').DataTable({
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
            url: '/ulab/reactive/getListReactiveProcessingAjax/',
            dataSrc: function (json) {
                console.log(json)
                return json.data
            },
        },
        columns: [
            {
                data: 'lab'
            },
            {
                data: 'is_reactive',
                width: '15%',
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
                data: 'name',
                width: '40%',
                // render: function (data, type, item) {
                //     if (type === 'display' || type === 'filter') {
                //         return `<a class="popup-with-form-edit"
                //                href="#edit-entry-modal-form-first"
                //                data-id="${item['id']}">
                //                ${item['name']}
                //             </a>`
                //     }
                //
                //     return item.request
                // }
            },
            {
                data: 'aggregate_name',
                width: '25%'
            },
            {
                data: 'short_name',
                width: '10%'
            },
            {
                data: 'doc_name',
                width: '15%'
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
                        return `<a class="popup-with-form-delete delete-reactive"
                               href="#delete-entry-modal"
                               data-id="${item['id']}" style="color: black">
                               <i class="fa fa-trash"></i>
                            </a>`
                },
            }

        ],
        responsive: {
            details: {
                type: 'column',
                target: 'tr'
            }
        },
        // columnDefs: [{
        // //     // className: 'control',
        // //     /*'targets': */
        //     'orderable': false,
        //     'targets': [6,7],
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
        fixedHeader: false,
        "rowCallback": function (nRow, data) {
            if (data['is_precursor'] == 1) {
                $('td', nRow).css('background-color', '#dacfcf')
            }
        }
    })

    reactiveJournal.columns().every(function () {
        $(this.header()).closest('thead').find('.search:eq(' + this.index() + ')').on('keyup change clear', function () {
            reactiveJournal
                .column($(this).parent().index())
                .search(this.value)
                .draw()
        })
    })

    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $('#reactive_journal').width()

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
        fixedContentPos: false
    })
    $('.popup-second').magnificPopup({
        items: {
            src: '#add-entry-modal-form-second',
            type: 'inline'
        },
        fixedContentPos: false
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
    $('.popup-with-form-edit').magnificPopup({
        items: {
            src: '#edit-entry-modal-form-first',
            type: 'inline'
        },
        fixedContentPos: false,
        closeOnBgClick: false
    })

    $('.popup-with-form-delete').magnificPopup({
        items: {
            src: '#delete-entry-modal',
            type: 'inline'
        },
        fixedContentPos: false,
        closeOnBgClick: false
    })

    /** journal filters */
    $('.filter-btn-search').on('click', function () {
        $('#journal_requests_filter').addClass('is-open')
        $('.filter-btn-search').hide()
    })

    $('.filter').on('change', function () {
        reactiveJournal.ajax.reload()
        reactiveJournal.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

    //Временно сохранить
    /* $('.search:first').on('input', function () {
         let val = $(this).val()
         $('#param').val(val)
     })*/

    $('body').on("click", ".popup-with-form-edit", function () {

        $.ajax({
            url: "/ulab/reactive/getReactiveInfoAjax",
            data: {"id": $(this).data('id')},
            dataType: "json",
            method: "POST",
            success: function (data) {
                console.log(data)
                $('#nameReactiveEdit').val(data.rmName)
                $('#reactive-id').val(data.id)
                $(`#reactive-type option[value=${data.is_reactive}]`).prop('selected', true)
                $(`#laba-select option[value=${data.laba_id}]`).prop('selected', true)
                $(`#agregate-select option[value=${data.agsId}]`).prop('selected', true)
                $(`#is_precursorEdit`).prop('checked', data.is_precursor == 1)
                $('#nd-doc').val(data.doc_name)
                $(`#qualityEdit option[value=${data.id_pure}]`).prop('selected', true)

                $.magnificPopup.open({
                    items: {
                        src: '#edit-entry-modal-form-first',
                        type: 'inline'
                    },
                    fixedContentPos: false
                })
            }
        })

        return false
    })

    $('body').on("click", ".popup-with-form-delete", function () {
        let id = $(this).data('id')
        $('.delete_reactive').val(id);

                $.magnificPopup.open({
                    items: {
                        src: '#delete-entry-modal',
                        type: 'inline'
                    },
                    fixedContentPos: false
                })
            // }
        // })

        return false
    })

})
