$(document).ready(function () {
    searchParams = new URLSearchParams(window.location.search)

    let workTypeTable = $('#kindofwork-handbook-table').DataTable({
        processing: true,
        serverSide: true,
        order: false,
        ajax: {
            type: 'POST',
            data: function (d) {
                d.dateStart = $('#inputDateStart').val()
                d.dateEnd = $('#inputDateEnd').val()
                d.stage = $('#selectStage option:selected').val()
                d.lab = $('#selectLab option:selected').val()
                d.everywhere = $('#filter_everywhere').val()
            },
            url: '/ulab/workDiary/getKindOfWorkList/',
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
                    return `<div class="text-center" id="kindofwork_${item.id}">${data}</div>`;
                }
            },
            {
                data: '',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="btn-group">
                                <button type="button" title="Редактировать тип работы" data-js-edit-kindofwork data-js-kindofwork-id="${item.id}" class="btn"><i class="fa-solid fa-pen"></i></button>
                                <button type="button" title="Удалить тип рыботы" data-js-delete-kindofwork data-js-kindofwork-id="${item.id}" class="btn"><i class="fa-solid fa-trash-can"></i></button>
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

    workTypeTable.columns().every(function () {
        $(this.header()).closest('thead').find('.search:eq(' + this.index() + ')').on('keyup change clear', function () {
            workTypeTable
                .column($(this).parent().index())
                .search(this.value)
                .draw();
        })
    });


    $("#search-btn").click(function (e) {
        workTypeTable.ajax.reload();
    })



    $("[data-js-close-modal]").click(function (e) {
        $.magnificPopup.close();
    });

    $("[data-js-toggle-scheme]").click(function (e) {
        $("[data-js-form-scheme]").toggle(500)
    });

    $("body").on('click', '[data-js-show-kindofwork-handbook]', function () {

        $.magnificPopup.open({
            items: {
                src: '#kindofwork_handbook',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });
    });

    $("body").on('click', '[data-js-delete-kindofwork]', function () {
        let conf = confirm("Удалить тип работы?");
        if (!conf)
            return;

        let url = "/ulab/workDiary/deleteKindOfWork";
        let data = {
            'id': $(this).attr("data-js-kindofwork-id"),
        };

        $.post(url, data, function (response) {
            if (!response.error) {
                workTypeTable.draw();
                return;
            }
        });
    });

    $("body").on('click', '[data-js-create-kindofwork]', function () {

        $.magnificPopup.open({
            items: {
                src: '#create-kindofwork-modal',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });
    });

    $("body").on('click', '[data-js-edit-kindofwork]', function () {
        const kindOfWorkId = $(this).attr("data-js-kindofwork-id");
        const kindOfWorkName = $(`#kindofwork_${kindOfWorkId}`).text();

        $.magnificPopup.open({
            items: {
                src: '#edit-kindofwork-modal',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });

        $('#kindofwork_name_input').val(kindOfWorkName);
        $('#kind_of_work_id').val(kindOfWorkId);
    });
});
