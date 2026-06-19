$(document).ready(function () {
    searchParams = new URLSearchParams(window.location.search)

    let journalRequests = $('#responsibleforacceptance-handbook-table').DataTable({
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
            url: '/ulab/workDiary/getResponsibleForAcceptanceList/',
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
                data: 'name',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="text-center" id="responsibleforacceptance_name_${item.id}">${data}</div>`;
                }
            },
            {
                data: '',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="btn-group">
                                <button type="button" title="Редактировать подрядчика" data-js-edit-responsibleforacceptance data-js-responsibleforacceptance-id="${item.id}" class="btn"><i class="fa-solid fa-pen"></i></button>
                                <button type="button" title="Удалить подрядчика" data-js-delete-responsibleforacceptance data-js-responsibleforacceptance-id="${item.id}" class="btn"><i class="fa-solid fa-trash-can"></i></button>
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

    $("body").on('click', '[data-js-show-responsibleforacceptance-handbook]', function () {

        $.magnificPopup.open({
            items: {
                src: '#responsibleforacceptance_handbook',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });
    });

    $("body").on('click', '[data-js-create-responsibleforacceptance]', function () {

        $.magnificPopup.open({
            items: {
                src: '#create-responsible-for-acceptance-modal',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });
    });



    $("body").on('click', '[data-js-edit-responsibleforacceptance]', function () {
        const responsibleId = $(this).attr("data-js-responsibleforacceptance-id");
        const responsibleName = $(`#responsibleforacceptance_name_${responsibleId}`).text();



        $.magnificPopup.open({
            items: {
                src: '#edit-responsible-for-acceptance-modal',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });

        $('#responsibleforacceptance_name_input').val(responsibleName);
        $('#responsible_id').val(responsibleId);
    });

    $("body").on('click', '[data-js-delete-responsibleforacceptance]', function () {
        let conf = confirm("Удалить ответственного?");
        if (!conf)
            return;

        let url = "/ulab/workDiary/deleteResponsibleForAcceptance";
        let data = {
            'id': $(this).attr("data-js-responsibleforacceptance-id"),
        };

        $.post(url, data, function (response) {
            if (!response.error) {
                journalRequests.draw();
                return;
            }
        });
    });
});
