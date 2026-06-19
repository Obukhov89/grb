$(function ($) {
    let $journal = $('#application_card_table')

    /*journal requests*/
    let journalDataTable = $journal.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            data: function (d) {
                d.url = window.location.href.split('?')[0];
                d.dateStart = $('#inputDateStart').val();
                d.dateEnd = $('#inputDateEnd').val();
                d.stage = $('#selectStage option:selected').val();
                d.lab = $('#selectLab option:selected').val();
                d.everywhere = $('#filter_everywhere').val();
                d.scheme_id = $('#work_type_select').val();
            },
            url: '/ulab/applicationCard/getListProcessingAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'view_id',
                orderable: false,
            },
            {
                data: 'checkbox',
                orderable: false,
                render: function (data, type, item) {
                    let value = item.checkbox;
                    let checked = isChecked(value);

                    return `<input type="checkbox" name="schema_info[${item.index}][checkbox]" ${checked} data-contractor_id="${item.contractor_id}" />`;
                }
            },
            {
                data: 'photo',
                render: function (data, type, item) {
                    if (item.file_path) {
                        return `<div class="block">
                                <a href="${item.file_path}" target="_blank"></a>
                                <div class="image" style="z-index: 9999">
                                    <img src="${item.file_path}" width="300" height="400">
                                </div>
                            </div>`;
                    } else{
                        return `<input class="form-control" type="file" name="schema_info[${item.index}][img]" />`;
                    }
                    // return `<i class="fa-solid fa-square-plus" name="${item.field_name}_img"></i>`;

                },
                orderable: false,
            },
            {
                data: 'comment',
                render: function (data, type, item) {
                    let comment = item.comment;
                    return `<textarea class="form-control" name="schema_info[${item.index}][comment]" data-contractor_id="${item.contractor_id}">${comment}</textarea>
                               <input type="hidden" name="schema_info[${item.index}][contractor_id]" value="${item.contractor_id}" />
                               <input type="hidden" name="schema_info[${item.index}][scheme_id]" value="${item.scheme_id}" />
                               <input type="hidden" name="schema_info[${item.index}][index]" value="${item.index}" />
                               <input type="hidden" name="schema_info[${item.index}][scheme_type_id]" value="${item.scheme_type_id}" />`;
                },
                orderable: false,
            },
        ],
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
            // {
            //     extend: 'colvis',
            //     titleAttr: 'Выбрать'
            // },
            // {
            //     extend: 'copy',
            //     titleAttr: 'Копировать',
            //     exportOptions: {
            //         modifier: {
            //             page: 'current'
            //         }
            //     }
            // },
            // {
            //     extend: 'excel',
            //     titleAttr: 'excel',
            //     exportOptions: {
            //         modifier: {
            //             page: 'current'
            //         }
            //     }
            // },
            // {
            //     extend: 'print',
            //     titleAttr: 'Печать',
            //     exportOptions: {
            //         modifier: {
            //             page: 'current'
            //         }
            //     }
            // }
        ],
        bSortCellsTop: true,
        scrollX: true,
        fixedHeader: false,
    });

    journalDataTable.columns().every(function () {
        $(this.header()).closest('thead').find('.search:eq(' + this.index() + ')').on('keyup change clear', function () {
            journalDataTable
                .column($(this).parent().index())
                .search(this.value)
                .draw();
        })
    });

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
            container.stop();
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
            container.stop();
        })

    $(document).scroll(function () {
        let positionScroll = $(window).scrollTop(),
            tableScrollBody = container.height()

        if (positionScroll > 265 && positionScroll < tableScrollBody) {
            $('.arrowRight').css('transform', `translateY(${positionScroll - 260}px)`);
            $('.arrowLeft').css('transform', `translateY(${positionScroll - 250}px)`);
        }
    });

    $("#work_type_select").on('change', function () {
        journalDataTable.draw();
    });

    function selectValue(optionValue, value) {
        return optionValue === parseInt(value) ? 'selected="selected"' : '';
    }

    function isChecked(value) {
        return value === "1" ? "checked" : "";
    }

    $("body").on('mouseover', '.block', function () {
        if (container.scrollLeft() === 0) {
            $(".dataTables_scrollBody").css("overflow", "visible");
            $(this).find(".image").css("display", "flex")
        }
    })

    $("body").on('mouseleave', '.block', function () {
        $(".dataTables_scrollBody").css("overflow", "auto");
        $(this).find(".image").css("display", "none")
    })
})