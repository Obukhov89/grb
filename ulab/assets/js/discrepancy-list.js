$(function ($) {
    let $journal = $('#discrepancy_table')

    /*journal requests*/
    let journalDataTable = $journal.DataTable({
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
            url: '/ulab/discrepancy/getListProcessingAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'row_number',
                defaultContent: "",
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="text-center">${item.row_number}</div>`;
                }
            },
            {
                data: 'created_at',
                defaultContent: "",
                orderable: true,
                render: function (data, type, item) {
                    return `<div class="text-center">${item.created_at}</div>`;
                }
            },
            {
                data: 'responsible',
                defaultContent: "",
                orderable: true,
                render: function (data, type, item) {
                    return `<div class="text-center">${item.responsible}</div>`;
                }
            },
            {
                data: 'summary',
                defaultContent: "",
                orderable: true,
                render: function (data, type, item) {
                    return `<div class="text-center">${item.summary}</div>`;
                }
            },
            {
                data: 'remark_on_elimination',
                defaultContent: "",
                orderable: true,
                render: function (data, type, item) {
                    return `<div class="text-center">${item.remark_on_elimination}</div>`;
                }
            },
            {
                data: '',
                render: function (data, type, item) {
                    return `<div class="btn-group">
                                <button title="Редактировать" data-js-edit-row data-js-row-id="${item.id}" class="btn"><i class="fa-solid fa-pen"></i></button>
                                <button title="Удалить" data-js-delete-row data-js-row-id="${item.id}" class="btn"><i class="fa-solid fa-trash-can"></i></button>
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
            //     text: '<i class="fa-solid fa-print"></i>',
            //     titleAttr: 'Вывод суммы',
            //     className: "dt-buttons btn-group flex-wrap",
            //     action: () => {
            //         window.location.href = "/ulab/execJournal/printSumByMonth";
            //     }
            // },
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
            {
                extend: 'excel',
                titleAttr: 'Экспорт',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                },
            }
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

    async function setResponsibleInSelect() {
        const url = "/ulab/discrepancy/getResponsible";
        const data = {};

        await $.post(url, data, function (response) {
            response = JSON.parse(response);
            let fioData = response.data;
            let options = `<option value="">Выбрать</option>`;
            for (const fio of fioData) {
                options += `<option value="${fio.fio}">${fio.fio}</option>`;
            }

            $("#responsible").empty().append(options);

            console.log('1')
        })
    }

    async function fillEditForm(rowId){
        let url = "/ulab/discrepancy/getRow";
        let data = {
            'id': rowId,
        };

        await $.post(url, data, function (response) {
            response = JSON.parse(response);
            if (!response) {
                return;
            }

            const data = response.data;

            $('#fix_date').val(data.fix_date);
            $('#fix_number').val(data.fix_number);
            $('#summary').val(data.summary);
            $(`#responsible option[value="${data.responsible}"]`).attr('selected', 'selected');
            $('#row_id').val(data.id);

            console.log('2')
        });
    }

    $("body").on("click", "[data-js-create]", function (e) {

        setResponsibleInSelect();

        $.magnificPopup.open({
            items: {
                src: '#create_row',
                type: 'inline'
            },
            fixedContentPos: false,
            // modal: true
        });
    });

    $("body").on("click", "[data-js-create-responsible]", function (e) {
        $.magnificPopup.open({
            items: {
                src: '#create_responsible',
                type: 'inline'
            },
            fixedContentPos: false
        });
    });

    $("body").on("click", "[data-js-delete-row]", async function (e) {
        let conf = confirm("Удалить запись?");
        if (!conf)
            return;

        let url = "/ulab/discrepancy/delete";
        let data = {
            'id': $(this).attr("data-js-row-id"),
        };

        $.post(url, data, function (response) {
            if (!response.error) {
                journalDataTable.draw();
                return;
            }
        });
    });
    $("body").on("click", "[data-js-import-excel]", async function (e) {
        $.magnificPopup.open({
            items: {
                src: '#excel-import-modal',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: false
        });
    });

    $("body").on("click", "[data-js-edit-row]", async function (e) {
        $.magnificPopup.open({
            items: {
                src: '#edit-row-modal',
                type: 'inline'
            },
            fixedContentPos: false,
        });

        await setResponsibleInSelect();
        await fillEditForm($(this).attr("data-js-row-id"));
    });
})