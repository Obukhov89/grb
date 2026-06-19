$(function ($) {

    /*journal requests*/
    let tableJournal = $('#tableJournal').DataTable({
        // processing: true,
        // serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {

            },
            url : '/ulab/transport/getReportTableAjax/',
            dataSrc: function (json) {
                console.log(json)
                return json.data;
            },
        },
        columns: [
            {
                data: 'date'
            },
            {
                data: 'time_start'
            },
            {
                data: 'time_end'
            },
            {
                data: 'km'
            },
            {
                data: 'gsm'
            },
            {
                data: 'price'
            },
            {
                data: 'full_sum'
            },
            {
                data: 'place'
            },
            {
                data: '',
                render: function (data, type, item) {
                    return `<div class="btn-group">
                                <button data-js-update="${item.id}" class="btn"><i class="fa-solid fa-pen"></i></button>
                                <button data-js-delete="${item.id}" class="btn"><i class="fa-solid fa-trash-can"></i></button>
                           </div>`
                }
            },

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
        order: [[ 0, "desc" ]],
        colReorder: true,
        dom: 'frt<"bottom"lip>',
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
        // scrollX:       true,
        // fixedHeader:   true,
        //autoWidth: false

    });

    tableJournal.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            tableJournal
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
        tableJournal.ajax.reload()
        tableJournal.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

    $('#user').select2();
    $('#transport').select2();

    // Добавить транспорт
    $("body").on('click', '[data-js-update]', function () {
        $.magnificPopup.open({
            items: {
                src: '#add-entry-modal-form',
                type: 'inline'
            },
            fixedContentPos: false
        });

        let id = $(this).attr("data-js-update")
        $("#report_id").val(id)

        if (id) {
            let $tr = $(this).closest("tr")
            let values = $tr.find("td")

            let dateText = $(values).eq(0).text()
            let dateArr = dateText.split(".")
            let dateValue = `${dateArr[2]}-${dateArr[1]}-${dateArr[0]}`
            $("#date").val(dateValue)

            let userId = $(values).eq(1).find("div").attr("data-js-user-id")
            $(`#user`).val(userId).change();

            let transportId = $(values).eq(2).find("div").attr("data-js-transport-id")
            $(`#transport`).val(transportId).change();

            $("#time_start").val($(values).eq(3).text())
            $("#time_end").val($(values).eq(4).text())
            $("#km").val($(values).eq(5).text())
            $("#gsm").val($(values).eq(6).text())
            $("#price").val($(values).eq(7).text())
            $("#place").val($(values).eq(9).text())
        } else {
            let userId = $("#currentUser").val();

            $("#date").val("")
            $(`#user`).val(userId).change();
            $(`#transport`).val("").change();
            $("#time_start").val("")
            $("#time_end").val("")
            $("#km").val("")
            $("#gsm").val("")
            $("#price").val("")
            $("#place").val("")
        }

    })

    $("#add-entry-modal-btn").click(function (e) {

        let check = true;

        $("[data-js]").each(function (index) {
            if ($(this).val() == "") {
                $(this).css("background", "#F08080")
                check = false;
            } else {
                $(this).css("background", "#FFF")
            }
        })

        if (check) {
            $.ajax({
                url: '/ulab/transport/addReportAjax/',
                data: $("#add-entry-modal-form").serialize(),
                method: 'POST',
                dataType: 'json',
                success: function (data) {
                    console.log(data)
                    if (data) {
                        tableJournal.ajax.reload()
                        $('.mfp-close').click();
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
        }


    })

    $("body").on('click', '[data-js-delete]', function () {
        $.magnificPopup.open({
            items: {
                src: '#delete-entry-modal-form',
                type: 'inline'
            },
            fixedContentPos: false
        });

        let id = $(this).attr("data-js-delete")
        $("#delete_transport").val(id)
    })

    $("#delete-entry-modal-btn").click(function (e) {
        $.ajax({
            url: '/ulab/transport/deleteAjax/',
            data: $("#delete-entry-modal-form").serialize(),
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                console.log(data)
                if (data) {
                    tableJournal.ajax.reload()
                    $('.mfp-close').click();
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
    })

    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $('#tableJournal').width()

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

    $('.filter').on('change', function () {
        tableJournal.ajax.reload()
        tableJournal.draw()
    })

    let gsmCounter = 0;

    $("body").on('click', '#gsm-report-add', function (e) {
        const $tbody = $("#gsm-report-table").find("tbody");

        $tbody.append(`
        <tr>
            <td><input type="date" name="gsmText[${gsmCounter}][date]" class="form-control"></td>
            <td><input type="time" name="gsmText[${gsmCounter}][time_start]" class="form-control"></td>
            <td><input type="time" name="gsmText[${gsmCounter}]time_end]" class="form-control"></td>
            <td><input type="number" data-js-km="${gsmCounter}" name="gsmText[${gsmCounter}][km]" class="form-control"></td>
            <td><input type="number" data-js-gsm="${gsmCounter}" name="gsmText[${gsmCounter}][gsm]" class="form-control"></td>
            <td><input type="number" name="gsmText[${gsmCounter}][price]" class="form-control"></td>
            <td><input type="text" name="gsmText[${gsmCounter}][object]" class="form-control"></td>
            <td class="d-flex justify-content-center">
                <button data-js-remove-row type="button" class="btn btn-danger rounded fa-solid fa-minus"></button>
            </td>
        </tr>
    `);

        gsmCounter++;
    })

    $("body").on('click', '[data-js-remove-row]', function () {
        $(this).closest("tr").remove()
    })



})