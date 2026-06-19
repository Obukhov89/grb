$( document ).ready(function() {
    let isAdmin = false
    /*journal requests*/
    let secondmentJournal = $('#secondmentJournal').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.dateStart = $('#inputDateStart').val()
                d.dateEnd = $('#inputDateEnd').val()
                d.everywhere = $('#filter_everywhere').val()
                d.stage_filter = $('#stage-filter').val()
            },
            url : '/ulab/secondment/getListProcessingAjax/',
            dataSrc: function (json) {
                console.log(json)
                isAdmin = json.isAdmin
                return json.data;
            },
        },

        createdRow: function (row, data, dataIndex) {
            if (isAdmin) {
                $(row).find("td").eq(6)
                    .attr("open-project-modal", data.s_id)
                    .attr("data-js-project-id", data.project_id)
            }

        },
        columns: [
            {
                data: 'viewed',
                render: function (data, type, item) {
                    if (data) {
                        return ""
                    } else {
                        return `<i class="fa-solid fa-circle-exclamation text-warning"></i>`
                    }
                }
            },
            {
                data: 'stage',
            //    orderable: false,
                render: function (data, type, item) {
                    if (item['stage']) {
                        return `<div class="border rounded px-3 py-2 ${item['stage_border_color']}">${item['stage']}</div>`
                    } else {
                        return `<div></div>`
                    }
                }
            },
            {
                data: 'title',
                class: 'text-nowrap',
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return `<a class="request-link"
                               href="${item['uri']}/secondment/card/${item['s_id']}">
<!--                               ${item['title']}-->
                               ${item['s_id']}
                            </a>`
                    }

                    return item.s_id
                }
            },
            {
                data: 'fio'
            },
            {
                data: 's_s_name',
                render: function (data, type, item) {
                  //  console.log(item)
                    if (data != null) {
                        return data
                    } else {
                        return item.settlement
                    }
                }
            },
            {
                data: 'd_o_name',
                render: $.fn.dataTable.render.ellipsis(40, true)
            },
            {
                data: 'project_name',
                defaultContent: '',
                render: function (data, type, item) {
                    return data ?? "";
                }
            },
            {
                data: 'oborud_list'
            },
            {
                data: 'date_begin',
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return item.date_begin_ru
                    }
                    return item.date_begin
                }
            },
            {
                data: 'date_end',
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return item.date_end_ru
                    }
                    return item.date_end
                }
            },
            {
                data: 'planned_expenses',
                render: function (data, type, item) {
                    // return new Intl.NumberFormat('ru-RU').format(data.toString())
                    return data.toLocaleString()
                }
            },
            {
                data: 'total_spent',
                render: function (data, type, item) {
                    if (data != "") {
                        return new Intl.NumberFormat('ru-RU').format(data.toString())
                    }

                    return data
                }
            },
            {
                data: 'overspending',
                render: function (data, type, item) {
                    if (type === 'display' || type === 'filter') {
                        return `<span ${item.overspending > 20 ? 'class="text-danger"' : ''}>${item.overspending}</span>`
                    }
                    return item.overspending
                }
            }
        ],
        language:{
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
        lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
        pageLength: 25,
        order: [[ 2, "desc" ]],
        colReorder: true,
        dom: 'frtB<"bottom"lip>',
        buttons: [
            {
                extend: 'colvis',
                titleAttr: 'Выбрать'
            },
            {
                extend: 'copy',
                titleAttr: 'Копировать',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'excel',
                titleAttr: 'excel',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            },
            {
                extend: 'print',
                titleAttr: 'Печать',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            }
        ],
        bSortCellsTop: true,
        scrollX:       true,
        //fixedHeader:   true,
       // autoWidth: false
    });

    secondmentJournal.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            secondmentJournal
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        })
    })

    /*journal filters*/
    $('.filter-btn-search').on('click', function () {
        $('#journal_requests_filter').addClass('is-open')
        $('.filter-btn-search').hide()
    })

    $('.filter').on('change', function () {
        secondmentJournal.ajax.reload()
        secondmentJournal.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $('#secondmentJournal').width()

    $('.btnRightTable, .arrowRight').hover(function() {
            container.animate(
                {
                    scrollLeft: scroll
                },
                {
                    duration: 4000, queue: false
                }
            )
        },
        function() {
            container.stop();
        })

    $('.btnLeftTable, .arrowLeft').hover(function() {
            container.animate(
                {
                    scrollLeft: -scroll
                },
                {
                    duration: 4000, queue: false
                }
            )
        },
        function() {
            container.stop();
        })

    $(document).scroll(function() {
        let positionScroll = $(window).scrollTop(),
            tableScrollBody = container.height()

        if (positionScroll > 265 && positionScroll < tableScrollBody) {
            $('.arrowRight').css('transform',`translateY(${positionScroll-260}px)`);
            $('.arrowLeft').css('transform',`translateY(${positionScroll-250}px)`);
        }
    })

    $(document).on("submit", "#add-entry-modal-form", function () {
        $("#save_button").prop("disabled", true);
    });

    /** modal */
    $("body").on("click", "[name='add_entry']", function (e) {
        e.stopImmediatePropagation();

        $.magnificPopup.open({
            items: {
                src: '#add-entry-modal-form',
                type: 'inline',

            },
            fixedContentPos: false,
        })

        console.log($.magnificPopup)
    })
    // $('[name="add_entry"]').magnificPopup({
    //     items: {
    //         src: '#add-entry-modal-form',
    //         type: 'inline'
    //     },
    //     fixedContentPos: false
    // })

    $('#company').on('change', function () {
     //   let companyId = $('#company-hidden').val();
        let companyId = $(this).val();
        console.log(companyId)

        $("[data-js-clients]").val(companyId).change();
      //  $("[data-js-clients]").text("ЗАО \"МПЗК\"")

        $.ajax({
            url: '/ulab/secondment/getObjectsAjax/',
            data: {
                company_id: companyId
            },
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                let $selectObject = $('#object');
                $selectObject.empty().append(`<option value=""></option>`);

                if (data.length !== 0) {
                    for (const i in data) {
                        if (data.hasOwnProperty(i)) {
                            $selectObject.append(
                                `<option value="${data[i].ID}">${data[i].NAME}</option>`
                            )
                        }
                    }
                }
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
    });

    // Модальное окно - объект
    $("[data-js-toggle-object]").click(function (e) {
        $("[data-js-form-object]").toggle(500)
    })

    $("[data-js-toggle-company]").click(function (e) {
        $("[data-js-form-company]").toggle(500)
    })

    $('[data-js-company-list]').select2();
    $('[data-js-clients]').select2();
    $('#object').select2();
    $('#user').select2();

    $('#city').select2({

        ajax: {
            url: "/ulab/secondment/getSettlementsAjax",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                console.log("succ")
                return {
                   searchTerm: params.term || '*'
                }
            },
            processResults: function (response) {
                console.log("err")
                return {
                    results: response
                };
            },
            cache: true
            }
    })

    $("#saveCompany").click(function (e) {
        $.ajax({
            url: '/ulab/secondment/addCompanyAjax/',
            data: $("#company input").serialize(),
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                $('#company').append(`<option value="${data.id}">${data.name}</option>`)
                $('#company').val(data.id).change();
                $("[data-js-form-company]").toggle(300)
                $('[data-js-clients]').append(`<option value="${data.id}">${data.name}</option>`)
                $("[data-js-clients]").val(data.id).change();
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
    });

    $("#inn").on('input', function () {

        $innHelp = $("#innHelp")
        let inn = $(this).val()

        $.ajax({
            url: "/ulab/request/getCompanyByInnFromBxAjax/",
            data: { "INN": $(this).val() },
            dataType: "json",
            method: "POST",
            success: function (data) {
                console.log(data)
                if (data.ENTITY_ID !== undefined) {
                    $innHelp.text('Найдено в системе').addClass('text-green')
                    $('#company').val(data.ENTITY_ID).change();
                    $("[data-js-form-company]").toggle(300)
                    $("[data-js-clients]").val(data.ENTITY_ID).change();
                } else {
                    findCompanyByInn(inn)
                }

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
                console.log(msg)
            }
        })

    })

    function findCompanyByInn(inn)
    {
        console.log("FIND")
        console.log(inn)
        $innHelp = $("#innHelp")

        $.ajax({
            url: "/ulab/request/getCompanyByInnAjax/",
            data: { "INN": inn },
            dataType: "json",
            method: "POST",
            success: function (data) {

                //  console.log(data)
                if (data && data.name_short !== undefined) {
                    $innHelp.text('Найдено в сети Интернет.').addClass('text-green')
                    if ( confirm(`Найдена компания с таким ИНН. Название: ${data.name_short}. Применить данные этой компании?`) ) {
                        $('input[name="company_name"]').val(data.name_short)
                        $('input[name="CompanyFullName"]').val(data.name)
                        $('input[name="KPP"]').val(data.kpp)
                        $('input[name="ADDR"]').val(data.adress)
                        $('input[name="Position2"]').val(data.position_name)
                        $('input[name="DirectorFIO"]').val(data.official_name)
                        $('input[name="OGRN"]').val(data.ogrn)
                    }
                } else {
                    $innHelp.text('Компаний с таким ИНН не найдено').addClass('text-red')
                }
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
                console.log(msg)
            }
        })
    }

    $("#stage-filter-btn").on('click', function (e) {
        $("#stage-filter-list").toggle(300)
    })

    $("body").on("change", "[data-js-stage]", function (e) {
        let checkedStagesArr = [];
        let checkedStages = "";

        $("[data-js-stage]").each(function (index) {
            if ($(this).is(":checked")) {
                checkedStagesArr.push("'" + $(this).attr("data-js-stage") + "'")
            }
        })

        checkedStages = checkedStagesArr.join(",")

        $("[name='stage-filter']").val(checkedStages)

        secondmentJournal.ajax.reload()
        secondmentJournal.draw()
    })

    $("body").on("click", "[open-project-modal]", function (e) {
        e.stopImmediatePropagation();

        $("[name='id']").val($(this).attr("open-project-modal"))

        let projectId = $(this).attr("data-js-project-id");

        if (projectId) {
            $("[name='project_id']").val($(this).attr("data-js-project-id"))
        }


        $.magnificPopup.open({
            items: {
                src: '#project-modal',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        })
    })

    $("#update-project").click(function (e) {
        $.ajax({
            url: '/ulab/secondment/updateProjectAjax/',
            data: $("#project-modal").serialize(),
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                console.log(data)
                secondmentJournal.ajax.reload(null, false)
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

    $("[data-js-close-modal]").click(function (e) {
        $.magnificPopup.close();
    })

})
