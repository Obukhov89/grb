$(function ($) {
    let rowData = {}
    let projectBg = {}

    let table = $('#table').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        //  stripeClasses: [],
        //order: [[0, "desc"]],
        ordering: false,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        pageLength: 50,
        dom: 'frt<"bottom"lip>',
        // scrollY: true,
      //  fixedHeader: true,
        ajax: {
            type: 'POST',
            dataType: 'json',
            url: `/ulab/overhead/getJournalAjax`,
            data: function (d) {
                d.date_start = $("#date_start").val(),
                d.date_end = $("#date_end").val()
                d.project_id = $("#project_id").val()
            },
            dataSrc: function (json) {
                console.log(json)
               // tableData = json.data
                projectBg = json.project_bg
                return json.data
            }
        },
        createdRow: function (row, data, dataIndex) {
           // $(row).find("td").eq(3).addClass(projectBg[data?.project_id])
        },
        columns: [
            {
                data: 'id',
                defaultContent: '',
                render: function (data, type, item) {
                    return data;
                }
            },
            {
                data: 'sum',
                defaultContent: '',
                render: function (data, type, item) {
                    return (data * 1)?.toLocaleString();
                }
            },
            {
                data: 'date_ru',
                defaultContent: '',
            },
            {
                data: 'project_name',
                defaultContent: '',
                render: function (data, type, item) {
                    return data ?? "Без проекта";
                }
            },
            {
                data: '',
                defaultContent: '',
                render: function (data, type, item) {
                    return `<div class="btn-group d-flex">
                                <button open-row-modal="${item.id}"
                                        data-js-date="${item.date}"
                                        data-js-sum="${item.sum}"
                                        data-js-project-id="${item.project_id}"
                                        class="btn"
                                >
                                        <i class="fa-solid fa-pen"></i>
                                </button>
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
    })

    $("[data-js-close-modal]").click(function (e) {
        $.magnificPopup.close();
    })

    $("body").on("click", "[open-row-modal]", function (e) {
        e.stopImmediatePropagation();

        let id = $(this).attr("open-row-modal")

        $("[name='id']").val(id)
        $("[name='sum']").val($(this).attr("data-js-sum"))
        $("[name='date']").val($(this).attr("data-js-date"))
        $("[name='project_id']").val($(this).attr("data-js-project-id") ?? 0)

        $.magnificPopup.open({
            items: {
                src: '#row-modal',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        })
    })

    $("#update-row").click(function (e) {
        $.ajax({
            url: '/ulab/overhead/insertUpdateAjax/',
            data: $("#row-modal").serialize(),
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                console.log(data)
                table.ajax.reload()
                $.magnificPopup.close();
            },
            error: function (jqXHR, exception) {
                let msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status === 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status === 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                console.error(msg)
            }
        })
    })

    $("#search-btn").click(function (e) {
        console.log("BBB")
        let dateStart = $("#date_start").val()
        let dateEnd = $("#date_end").val()
        //  let orderNumber = $("[data-js-select-order]").val()
        let projectId = $("#project_id").val()

        document.location.href = `/ulab/overhead/journal/?date_start=${dateStart}&date_end=${dateEnd}&project_id=${projectId}`
    })

    $("#reset-btn").click(function (e) {
        $("#date_start").val("")
        $("#date_end").val("")
       $("#project_id").val("")
    })
})