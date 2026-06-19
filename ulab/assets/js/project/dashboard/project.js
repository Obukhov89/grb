searchParams = new URLSearchParams(window.location.search)

let projectTable = $('#projectTable').DataTable({
    dom: 'frt<"bottom"lip>',
    pageLength: 50,
    ordering: false,
  //  data: data,
    ajax: {
        type : 'POST',
        url: `/ulab/project/getDashboardData`,
        data: function (d) {
            d.project_id = $("#project_id").val(),
            d.date = searchParams.get("date")
        },
        dataSrc: function (data) {
            console.log("project: ")
            console.log(data)
            return data
        }
    },
    createdRow: function (row, data, dataIndex) {
        $(row).find('td').css('text-align', 'center')
        $(row).find('td').eq(0).attr("data-js-plan-expenses", round(data?.plan_expenses, 2))
    },
    columns: [
        {
            data: 'plan_expenses',
            defaultContent: '',
            render: function (data, type, item) {
                return (data * 1).toLocaleString();
            }
        },
        {
            data: 'fact_expenses',
            defaultContent: '',
            render: function (data, type, item) {
                return data.toLocaleString();
            }
        },
        {
            data: 'profitability',
            defaultContent: '',
            render: function (data, type, item) {
                return data ? data + '%' : ''
            }
        },
        {
            data: 'fot',
            defaultContent: '',
            render: function (data, type, item) {
                return (data * 1).toLocaleString();
            }
        },
        {
            data: 'overhead_sum',
            defaultContent: '',
        },
        {
            data: 'secondment_expenses',
            defaultContent: '',
            render: function (data, type, item) {
                return data.toLocaleString();
            }
        },
        {
            data: 'fuel_sum',
            defaultContent: '',
            render: function (data, type, item) {
                return data.toLocaleString();
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

$("body").on("click", "[open-date-modal]", function (e) {
    e.stopImmediatePropagation();

    $.magnificPopup.open({
        items: {
            src: '#add-date-modal',
            type: 'inline'
        },
        fixedContentPos: false,
        modal: true,
    })
})

$("body").on("click", "[open-project-modal]", function (e) {
    e.stopImmediatePropagation();

    let planExpenses = $("[data-js-plan-expenses]").attr("data-js-plan-expenses")
    $("#project-modal [name='plan_expenses']").val(planExpenses)

    $.magnificPopup.open({
        items: {
            src: '#project-modal',
            type: 'inline'
        },
        fixedContentPos: false,
        modal: true,
    })
})

$("#save-project").click(function (e) {
    $.ajax({
        url: '/ulab/project/updateProjectAjax/',
        data: {
            "project_id": $("#project_id").val(),
            "plan_expenses": $("#project-modal [name='plan_expenses']").val()
        },
        method: 'POST',
        dataType: 'json',
        success: function (data) {
            console.log(data)
            projectTable.ajax.reload()
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

$("#save-month-project").click(function (e) {
    $.ajax({
        url: '/ulab/project/updateMonthProjectAjax/',
        data: {
            "project_id": $("#project_id").val(),
            "plan_expenses": $("#month-project-modal [name='month_plan_expenses']").val(),
            "date": searchParams.get("date")
        },
        method: 'POST',
        dataType: 'json',
        success: function (data) {
            console.log(data)
            projectTable.ajax.reload()
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

$("body").on("click", "[open-month-project-modal]", function (e) {
    e.stopImmediatePropagation();

    let planExpenses = $("[data-js-plan-expenses]").attr("data-js-plan-expenses")
    $("#month-project-modal [name='month_plan_expenses']").val(planExpenses)

    $.magnificPopup.open({
        items: {
            src: '#month-project-modal',
            type: 'inline'
        },
        fixedContentPos: false,
        modal: true,
    })
})

$("[data-js-close-modal]").click(function (e) {
    $.magnificPopup.close();
})
