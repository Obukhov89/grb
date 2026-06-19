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
            url: '/ulab/schemeEditor/getIDTypesList/',
            data: function (d) {
                d.type = $("#filter-type").val(),
                    d.dateStart = $("#dateStart").val(),
                    d.dateEnd = $("#dateEnd").val(),
                    d.card_id = $("#card_id").val()
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
                data: 'name',
                orderable: false,
                render: function (data, type, item) {
                    return `<div data-id="schemetype_${item.scheme_type_id}" class="text-center" >${data}</div>`;
                }
            },
            {
                data: '',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="btn-group">
                                <button data-js-card-id="${item.card_id}" data-js-type-id="${item.type_id}" data-js-schemetype-id="${item.scheme_type_id}" data-js-edit-work-type class="btn"><i class="fa-solid fa-pen"></i></button>
                                <button data-js-card-id="${item.card_id}" data-js-type-id="${item.type_id}" data-js-schemetype-id="${item.scheme_type_id}" data-js-delete-scheme-type class="btn"><i class="fa-solid fa-trash-can"></i></button>
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

    $("#search-btn").click(function (e) {
        journalRequests.ajax.reload();
    })


    $("#gost").select2({
        dropdownParent: $('#add-entry-modal-form'),
    })


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

    $("body").on("click", "[data-js-create-docs]", function (e) {
        $.magnificPopup.open({
            items: {
                src: '#add-entry-modal-form',
                type: 'inline'
            },
            fixedContentPos: false

        });

        // $("[data-js-add-modal-worktype-id]").val($(this).attr("data-js-worktype-id"));
    });

    $("body").on("click", "[data-js-create-docs]", function (e) {
        $.magnificPopup.open({
            items: {
                src: '#add-entry-modal-form',
                type: 'inline'
            },
            fixedContentPos: false

        });

        // $("[data-js-add-modal-worktype-id]").val($(this).attr("data-js-worktype-id"));
    });

    $("body").on("click", "[data-js-create-id]", function (e) {
        $.magnificPopup.open({
            items: {
                src: '#add-id-modal-form',
                type: 'inline'
            },
            fixedContentPos: false

        });

        // $("[data-js-add-modal-worktype-id]").val($(this).attr("data-js-worktype-id"));
    });

    $("body").on("click", "[data-js-delete-scheme-type]", function (e) {
        // delete
        let conf = confirm("Удалить тип ИД?");
        if (!conf)
            return;

        let url = "/ulab/schemeEditor/deleteIDType";
        let data = {
            'scheme_type_id': $(this).attr("data-js-schemetype-id"),
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
                src: '#edit-id-modal-form',
                type: 'inline'
            },
            fixedContentPos: false,
        });

        let schemetypeId = $(this).attr("data-js-schemetype-id");

        $("#idtype_name").val($(`[data-id="schemetype_${schemetypeId}"]`).text());
        $("#type_id").val($(this).attr("data-js-type-id"));
        $("#card_id").val($(this).attr("data-js-card-id"));
    });
});
