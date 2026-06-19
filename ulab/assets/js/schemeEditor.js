$(document).ready(function () {
    searchParams = new URLSearchParams(window.location.search)

    let journalRequests = $('#table').DataTable({
        processing: true,
        serverSide: true,
        // processing: true,
        // serverSide: true,
        // responsive: true,
        // colReorder: true,
        order: false,
        // lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        // pageLength: 50,
        // dom: 'frt<"bottom"lip>',
        // bSortCellsTop: true,
        // scrollX: true,
        // fixedHeader: false,
        ajax: {
            type: 'POST',
            dataType: 'json',
            url: '/ulab/schemeEditor/getListProcessingAjax/',
            data: function (d) {
                d.type = $("#filter-type").val(),
                    d.dateStart = $("#dateStart").val(),
                    d.dateEnd = $("#dateEnd").val()
            },
            dataSrc: function (json) {
                return json.data
            },
        },
        createdRow: function (row, data, dataIndex) {
            $(row).find('td').css('text-align', 'center')
        },
        columnDefs: [{
            'targets': "_all", /* table column index */
            'orderable': false, /* true or false */
        }],
        columns: [
            {
                data: 'work_type',
                defaultContent: "",
                orderable: false,
                render: function (data, type, item) {
                    //  return `<a href="${URI}/laboratory/materialCard/${item.material_id}/">${data}</a>`;
                    return `<div class="text-center" data-id="work_type_${item.work_type_id}" >${data}</div>`;
                }
            },
            {
                data: 'object',
                defaultContent: "",
                orderable: false,
                render: function (data, type, item) {
                    //  return `<a href="${URI}/laboratory/materialCard/${item.material_id}/">${data}</a>`;
                    return `<div class="text-center" data-id="object_${item.work_type_id}">${data}</div>`;
                }
            },
            {
                data: 'scheme_list',
                defaultContent: "",
                orderable: false,
                render: function (data, type, item) {
                    let str = '';
                    if (item.scheme_list.length > 0) {
                        for (let elem of item.scheme_list) {
                            str += `<a href="/ulab/schemeEditor/card/${elem.id}">
                                    ${elem.name}
                                </a>  
                                <br>`;
                        }
                    }

                    return str;
                }
            },
            {
                data: '',
                render: function (data, type, item) {
                    return `<div class="btn-group">
                                <button title="Добавить схему" data-js-add-scheme data-js-worktype-id="${item.work_type_id}" class="btn"><i class="fa-solid fa-plus"></i></button>
                                <button title="Редактировать тип работ" data-js-edit-work-type data-js-worktype-id="${item.work_type_id}" class="btn"><i class="fa-solid fa-pen"></i></button>
                                <button title="Удалить тип работ" data-js-delete-work-type data-js-worktype-id="${item.work_type_id}" class="btn"><i class="fa-solid fa-trash-can"></i></button>
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
        }

    });

    journalRequests.columns().every(function () {
        $(this.header()).closest('thead').find('.search:eq(' + this.index() + ')').on('keyup change clear', function () {
            journalRequests
                .column($(this).parent().index())
                .search(this.value)
                .draw();
        })
    });

    $("body").on("click", "[data-js-update]", function (e) {
        $.magnificPopup.open({
            items: {
                src: '#create_work_type',
                type: 'inline'
            },
            fixedContentPos: false
        });

    });


    $("#search-btn").click(function (e) {
        journalRequests.ajax.reload();
    })


    // $("#material").select2({
    //     dropdownParent: $('#add-entry-modal-form'),
    // })
    //
    // $("#gost").select2({
    //     dropdownParent: $('#add-entry-modal-form'),
    // })
    // $("#gost_scheme_list").select2({
    //     dropdownParent: $('#add-entry-modal-form'),
    // })


    $("[data-js-close-modal]").click(function (e) {
        $.magnificPopup.close();
    });

    $("[data-js-toggle-scheme]").click(function (e) {
        $("[data-js-form-scheme]").toggle(500)
    });

    $("#filter-type").change(function (e) {
        let URI = window.location.origin;
        if ($(this).val() == 1) {
            document.location.href = `${URI}/ulab/schemeEditor/index?type=1`;
        } else {
            document.location.href = `${URI}/ulab/schemeEditor/index?type=2`;
        }
    });

    $("body").on("click", "[data-js-add-scheme]", function (e) {
        $.magnificPopup.open({
            items: {
                src: '#add-scheme-modal-form',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true
        });

        $("[data-js-add-modal-worktype-id]").val($(this).attr("data-js-worktype-id"));
    });

    $("body").on("click", "[data-js-delete-work-type]", async function (e) {
        // delete
        let conf = confirm("Удалить тип работ?");
        if (!conf)
            return;

        let url = "/ulab/schemeEditor/deleteSchemeEditorItem";
        let data = {
            'worktype_id': $(this).attr("data-js-worktype-id"),
        };

        $.post(url, data, function (response) {
            if (!response.error) {
                journalRequests.draw();
                return;
            }
        });
    });

    $("body").on("click", "[data-js-edit-work-type]", function (e) {
        // edit
        $.magnificPopup.open({
            items: {
                src: '#edit-scheme-modal-form',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true
        });

        let worktypeId = $(this).attr("data-js-worktype-id");

        $("[data-js-edit-modal-worktype-id]").val(worktypeId);

        $("#edit_work_type").val($(`[data-id="work_type_${worktypeId}"]`).text());
        $("#edit_object").val($(`[data-id="object_${worktypeId}"]`).text());
    });
});
