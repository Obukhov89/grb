searchParams = new URLSearchParams(window.location.search)

$('#overheadTable').DataTable({
    dom: 'frt<"bottom"lip>',
    pageLength: 50,
    ordering: false,
    //  data: secondmentData,
    ajax: {
        type : 'POST',
        url: `/ulab/project/getOverheadDataAjax`,
        data: function (d) {
            d.project_id = $("#project_id").val(),
            d.date = searchParams.get("date")
        },
        dataSrc: function (data) {
            console.log(data)
            return data
        }
    },
    createdRow: function (row, data, dataIndex) {
        $(row).find('td').css('text-align', 'center')
    },
    columns: [
        {
            data: 'id',
            defaultContent: '',
            render: function (data, type, item) {
                return `<div class="fw-bold">${item.id}</div>`;
            }
        },
        {
            data: 'sum',
            defaultContent: '',
            render: function (data, type, item) {
                return data.toLocaleString();
            }
        },
        {
            data: 'date',
            defaultContent: '',
            render: function (data, type, item) {
                return data;
            }
        }
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
})
