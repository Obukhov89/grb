$(function ($) {
    let resultStages = [];
    /*journal requests*/
    let tableJournal = $('#seal-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            data: function (d) {
                d.dateStart = $('#inputDateStart').val()
                d.dateEnd = $('#inputDateEnd').val()
                d.stage = $('#selectStage option:selected').val()
                d.lab = $('#selectLab option:selected').val()
                d.everywhere = $('#filter_everywhere').val()
            },
            url: '/ulab/seal/getListProcessingAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'row_number',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="text-center">${item.row_number}</div>`;
                }
            },
            {
                data: 'date_field',
                defaultContent: '',
                orderable: true,
                render: function (data, type, item) {
                    return `<div class="text-center">${item.date_field}</div>`;
                }
            },
            {
                data: 'coordinates',
                defaultContent: '',
                orderable: true,
                render: function (data, type, item) {
                    return `<div class="text-center">${item.coordinates}</div>`;
                }
            },
            {
                data: 'remark',
                defaultContent: '',
                orderable: true,
                render: function (data, type, item) {
                    return `<div class="text-center">${item.remark}</div>`;
                }
            },
            {
                data: 'protocol',
                defaultContent: '',
                orderable: true,
                render: function (data, type, item) {
                    return `<div class="text-center">${item.protocol}</div>`;
                }
            },
            {
                data: 'file_path',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item) {
                    if (data) {
                        return `<div class="block" style="margin: 0 auto;">
                                <a href="${data}" target="_blank"></a>
                                <div class="image" style="z-index: 9999;">
                                    <img src="${data}" width="300" height="400" alt="Фото">
                                </div>
                            </div>`;
                    } else {
                        return ``;
                    }
                }
            },
            {
                data: '',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="btn-group">
                                <button data-js-edit-journal-row data-js-row-id="${item.id}" title="Редактировать" class="btn"><i class="fa-solid fa-pen"></i></button>
                                <button data-js-delete-journal-row data-js-row-id="${item.id}" title="Удалить" class="btn"><i class="fa-solid fa-trash-can"></i></button>
                           </div>`
                }
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
            aria: {
                sortAscending: ': активировать для сортировки столбца по возрастанию',
                sortDescending: ': активировать для сортировки столбца по убыванию'
            }
        },
        buttons: [
            {
                extend: 'excel',
                text: '',
                titleAttr: 'excel',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            },
        ],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        pageLength: 25,
        order: [],
        //dom: 'frt<"bottom"lip>',
        dom: 'frtB<"bottom"lip>',
        bSortCellsTop: true,
        scrollX: true,
        fixedHeader: false,
        // fixedHeader:   true,
        autoWidth: false
    });

    tableJournal.columns().every(function () {
        $(this.header()).closest('thead').find('.search:eq(' + this.index() + ')').on('keyup change clear', function () {
            tableJournal
                .column($(this).parent().index())
                .search(this.value)
                .draw();
        })
    })

    /*journal filters*/
    $('.filter-btn-search').on('click', function () {
        $('#journal_requests_filter').addClass('is-open')
        $('.filter-btn-search').hide()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

    $("[data-js-close-modal]").click(function (e) {
        $.magnificPopup.close();
    })

    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $('#work-diary-table').width()

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
        });

    $(document).scroll(function () {
        let positionScroll = $(window).scrollTop(),
            tableScrollBody = container.height();

        if (positionScroll > 265 && positionScroll < tableScrollBody) {
            $('.arrowRight').css('transform', `translateY(${positionScroll - 260}px)`);
            $('.arrowLeft').css('transform', `translateY(${positionScroll - 255}px)`);
        }
    });

    $('.filter').on('change', function () {
        tableJournal.ajax.reload();
        tableJournal.draw();
    });

    $("body").on('mouseover', '.block', function () {
        if (container.scrollLeft() === 0) {
            $(".dataTables_scrollBody").css("overflow", "visible");
            $(this).find(".image").css("display", "flex");
        }
    });

    $("body").on('mouseleave', '.block', function () {
        $(".dataTables_scrollBody").css("overflow", "auto");
        $(this).find(".image").css("display", "none");
    });

    $("body").on('mouseover', '.block', function () {
        if (container.scrollLeft() === 0) {
            $(".dataTables_scrollBody").css("overflow", "visible");
            $(this).find(".image").css("display", "flex");
        }
    });

    $("body").on('mouseleave', '.block', function () {
        $(".dataTables_scrollBody").css("overflow", "auto");
        $(this).find(".image").css("display", "none");
    });

    $("body").on('click', '[data-js-create-row]', function () {
        $.magnificPopup.open({
            items: {
                src: '#create_row',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });
    });

    $("body").on('click', '[data-js-delete-journal-row]', function () {
        let conf = confirm("Удалить запись?");
        if (!conf)
            return;

        let url = "/ulab/seal/delete";
        let data = {
            'id': $(this).attr("data-js-row-id"),
        };

        $.post(url, data, function (response) {
            if (!response.error) {
                tableJournal.draw();
                return;
            }
        });
    });

    $("body").on('click', '[data-js-edit-journal-row]', function () {

        const rowId = $(this).attr("data-js-row-id");

        const url = "/ulab/seal/getJournalById";
        const data = {
            'id': rowId
        };

        $.magnificPopup.open({
            items: {
                src: '#edit_row',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });

        $.post(url, data, function (response) {
            let journalData = JSON.parse(response);

            $('#row_id').val(journalData.id);
            //
            $(`#date_field_input option[value="${journalData.date_field}"]`).attr('selected', true);
            $(`#coordinates_input option[value="${journalData.coordinates}"]`).attr('selected', true);
            $("#remark_input").val(journalData.remark);
            $("#protocol_input").val(journalData.protocol);


            $("#photo_path").attr("src", journalData.file_path);
            $("#photo_link").attr("href", journalData.file_path);
        });
    });
})