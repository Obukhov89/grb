$(document).ready(function () {
    searchParams = new URLSearchParams(window.location.search)

    let journalRequests = $('#contractor-handbook-table').DataTable({
        processing: true,
        serverSide: true,
        order: false,
        ajax: {
            type: 'POST',
            data: function (d) {
                // d.dateStart = $('#inputDateStart').val()
                // d.dateEnd = $('#inputDateEnd').val()
                // d.stage = $('#selectStage option:selected').val()
                // d.lab = $('#selectLab option:selected').val()
                // d.everywhere = $('#filter_everywhere').val()
            },
            url: '/ulab/workDiary/getContractorsList/',
            dataSrc: function (json) {
                return json.data
            }
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
                data: 'contractor_name',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="text-center" id="contractor_name_${item.id}">${data}</div>`;
                }
            },
            {
                data: '',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="btn-group">
                                <button type="button" title="Редактировать подрядчика" data-js-edit-contractor data-js-contractor-id="${item.id}" class="btn"><i class="fa-solid fa-pen"></i></button>
                                <button type="button" title="Удалить подрядчика" data-js-delete-contractor data-js-contractor-id="${item.id}" class="btn"><i class="fa-solid fa-trash-can"></i></button>
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



    $("[data-js-close-modal]").click(function (e) {
        $.magnificPopup.close();
    });

    $("[data-js-toggle-scheme]").click(function (e) {
        $("[data-js-form-scheme]").toggle(500)
    });

    $("body").on('click', '[data-js-show-contractor-handbook]', function () {

        $.magnificPopup.open({
            items: {
                src: '#contractor_handbook',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });
    });

    $("body").on('click', '[data-js-create-contractor]', function () {

        $.magnificPopup.open({
            items: {
                src: '#create-contractor-modal',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });
    });

    $("body").on('click', '[data-js-delete-contractor]', function () {
        let conf = confirm("Удалить подрядчика?");
        if (!conf)
            return;

        let url = "/ulab/workDiary/deleteContractor";
        let data = {
            'id': $(this).attr("data-js-contractor-id"),
        };

        $.post(url, data, function (response) {
            if (!response.error) {
                journalRequests.draw();
                return;
            }
        });
    });

    $("body").on('click', '[data-js-edit-contractor]', function () {
        const contractorId = $(this).attr("data-js-contractor-id");
        const contractorName = $(`#contractor_name_${contractorId}`).text();

        $.magnificPopup.open({
            items: {
                src: '#edit-contractor-modal',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });

        $('#contractor_name_input').val(contractorName);
        $('#contractor_id').val(contractorId);
    });
});
