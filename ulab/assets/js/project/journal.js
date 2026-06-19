searchParams = new URLSearchParams(window.location.search)

let table = $('#table').DataTable({
    dom: 'frt<"bottom"lip>',
    pageLength: 50,
    // ordering: false,
    order: [[0, 'desc']],
    //  data: data,
    ajax: {
        type : 'POST',
        url: `/ulab/project/getListAjax`,
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
      //  $(row).find('td').eq(3).attr("open-company-modal", data.id)
       // $(row).find('td').eq(0).attr("data-js-plan-expenses", round(data?.plan_expenses, 2))
    },
    columnDefs: [{
        targets: [3, 4],  // Disable sorting for columns 2 and 3
        orderable: false
    }],
    columns: [
        {
            data: 'id',
            defaultContent: '',
            render: function (data, type, item) {
                return `<a href="/ulab/project/dashboard/${data}" target="_blank" class="fw-bold text-primary text-decoration-none">${data}</a>`
            }
        },
        {
            data: 'name',
            defaultContent: ''
        },
        {
            data: 'plan_expenses',
            defaultContent: '',
            render: function (data, type, item) {
                return (data * 1).toLocaleString();
            }
        },
        {
            data: '',
            defaultContent: '',
            render: function (data, type, item) {
                let html = ''
                let companyList = JSON.parse(item?.company_list)

                if (companyList) {
                    companyList.forEach(function (companyItem, index) {
                        html +=
                            `
                                <div class="d-flex justify-content-start align-items-center gap-3">
                                    <div>${index + 1}. ${companyItem.title}</div>
                                    <i class="fa-solid fa-xmark text-danger cursor-pointer" open-delete-company-modal="${companyItem.id}"></i>
                                </div>
                            `
                    })
                }

                return html;
            }
        },
        {
            data: '',
            defaultContent: '',
            render: function (data, type, item) {
                return `<div class="btn-group d-flex justify-content-center">
                                <button open-row-modal="${item.id}"
                                        data-js-plan-expenses="${item.plan_expenses}"
                                        data-js-name="${item.name}"
                                        class="btn"
                                >
                                        <i class="fa-solid fa-pen"></i>
                                </button>
                                <button open-company-modal="${item.id}"
                                        class="btn"
                                >
                                       <i class="fa-solid fa-square-plus fa-xl"></i>
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
    $("[name='name']").val($(this).attr("data-js-name"))
    $("[name='plan_expenses']").val($(this).attr("data-js-plan-expenses"))

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
        url: '/ulab/project/insertUpdateAjax/',
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


$("body").on("click", "[open-company-modal]", function (e) {
    e.stopImmediatePropagation();

    $("#company-modal [data-js-msg]").addClass("d-none")

    let id = $(this).attr("open-company-modal")

    $("#company-list").val("").trigger('change');

    $("[name='id']").val(id)
    // $("[name='name']").val($(this).attr("data-js-name"))
    // $("[name='plan_expenses']").val($(this).attr("data-js-plan-expenses"))

    $.magnificPopup.open({
        items: {
            src: '#company-modal',
            type: 'inline'
        },
        fixedContentPos: false,
        modal: true,
    })
})

$("#add-company").click(function (e) {
    $("#company-modal [data-js-msg]").addClass("d-none")
    $.ajax({
        url: '/ulab/project/addProjectCompanyAjax',
        data: $("#company-modal").serialize(),
        method: 'POST',
        dataType: 'json',
        success: function (data) {
            console.log(data)
            table.ajax.reload()
            $.magnificPopup.close();
        },
        error: function (jqXHR, exception) {
            $("#company-modal [data-js-msg]").removeClass("d-none")
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

$('#company-list').select2({
    ajax: {
        url: `/ulab/project/getCompanyListAjax`,
        type: "post",
        dataType: 'json',
        delay: 250,
        allowClear: true,
        minimumInputLength: 3,
        data: function (params) {
            return {
                title: params.term || '*' // search term
            };
        },
        processResults: function (response) {
            //  console.log("resp select: ")
            //  console.log(response)
            // response.all = {
            //     "deal_id_b": "",
            //     "order_number": "Все заказы"
            // }
            return {
                results: $.map(response, function (item) {
                    return {
                        id: item.id,
                        text: item.title
                    }
                })
            };
        },
        language: {
            "noResults": function(){
                return "Нет результатов, введите новый запрос";
            }
        },
        cache: true,
    }
})

$("body").on("click", "[open-delete-company-modal]", function (e) {
    e.stopImmediatePropagation();

    let id = $(this).attr("open-delete-company-modal")

    $("#delete-company-modal [name='company_id']").val(id)
    // $("[name='name']").val($(this).attr("data-js-name"))
    // $("[name='plan_expenses']").val($(this).attr("data-js-plan-expenses"))

    $.magnificPopup.open({
        items: {
            src: '#delete-company-modal',
            type: 'inline'
        },
        fixedContentPos: false,
        modal: true,
    })
})

$("#del-company").click(function (e) {
    $.ajax({
        url: '/ulab/project/deleteProjectCompanyAjax',
        data: $("#delete-company-modal").serialize(),
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

