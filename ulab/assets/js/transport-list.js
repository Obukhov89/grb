$(function ($) {

    /*journal requests*/
    let transportJournal = $('#transportJournal').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.everywhere = $('#filter_everywhere').val()
            },
            url : '/ulab/transport/getListProcessingAjax/',
            dataSrc: function (json) {
                console.log(json.data)
                return json.data;
            },
        },
        columns: [
            {
                data: 'id'
            },
            {
                data: 'model'
            },
            {
                data: 'number'
            },
            {
                data: 'owner_name'
            },
            {
                data: 'fuel_title'
            },
            {
                data: 'consumption_rate'
            },
            {
                data: 'personal',
                render: function (data, type, item) {
                    return data == 1 ? "Да" : "Нет"
                }
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

    transportJournal.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            transportJournal
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
        transportJournal.ajax.reload()
        transportJournal.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

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
        $("#transport_id").val(id)

        if (id) {
            let $tr = $(this).closest("tr")
            let values = $tr.find("td")

            $("#model").val($(values).eq(1).text())
            $("#number").val($(values).eq(2).text())
            $("#owner").val($(values).eq(3).text())
            $(`#fuel option:contains(${$(values).eq(4).text()})`).attr('selected', true);
            $("#consumption_rate").val($(values).eq(5).text())

            let personal = $(values).eq(6).text() === "Да" ? 1 : 0;

            if (personal === 1) {
                $("#personal").prop("checked", true)
            } else {
                $("#personal").prop("checked", false)
            }

          //  $("#personal").val(personal)

            console.log(values)
        } else {
            $("#model").val("")
            $("#number").val("")
            $("#owner").val("")
            $(`#fuel option:eq(0)`).attr('selected', true);
            $("#consumption_rate").val("")
            $("#personal").prop("checked", false)
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
                url: '/ulab/transport/addTransportAjax/',
                data: $("#add-entry-modal-form").serialize(),
                method: 'POST',
                dataType: 'json',
                success: function (data) {
                    console.log(data)
                    if (data) {
                        transportJournal.ajax.reload()
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
                    transportJournal.ajax.reload()
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
        scroll = $('#transportJournal').width()

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



})