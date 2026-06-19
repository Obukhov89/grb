$(function ($) {
    let resultStages = [];
    /*journal requests*/
    let tableJournal = $('#tableJournal').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.everywhere = $('#filter_everywhere').val()
                d.date_start = $('#inputDateStart').val()
                d.date_end = $('#inputDateEnd').val()
            },
            url : '/ulab/contractor/getJournalAjax/',
            dataSrc: function (json) {
                console.log(json)
                resultStages = json.result_stages
                return json.data;
            },
        },
        columns: [
            {
                data: 'status',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item) {
                    let bg = ""
                    let title = ""
                   if (data == 0) {
                       bg = "bg-success"
                       title = "Новая"
                   } else if (data == 1) {
                       bg = "bg-primary"
                       title = "В процессе"
                   } else if (data == 2) {
                       bg = "bg-secondary"
                       title = "Завершена"
                   }
                    return `<div class="stage rounded ${bg}" 
                                 title="${title}"
                                 data-js-status-btn="${item.id}"
                                 data-js-status="${data}"
                            ></div>`
                }

            },
            {
                data: 'id',
                class: "fw-bold",
                render: function (data, type, item) {
                    return `<a 
                                class="text-decoration-none" 
                                href="/ulab/contractor/user/${item.user_id}"
                            >${item.monthly_order_number}/${item.month_number}</a>`
                }
            },
            {
                data: 'datetime',
                defaultContent: '',
                orderable: false
            },
            {
                data: 'weather',
                defaultContent: '',
                orderable: false
            },
            {
                data: 'content',
                defaultContent: '',
                orderable: false
            },
            {
                data: '',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item) {
                    if (item.content_file !== "") {
                        let filePath = `/ulab/upload/contractor/content/${item.id}/${item.content_file}`
                        return `
                            <div class="block">
                                <a href="${filePath}" target="_blank"></a>
                                <div class="image" style="z-index: 9999">
                                    <img src="${filePath}" width="300" height="400">
                                </div>
                            </div>
                        `;
                    }
                    return ""


                }
            },
            {
                data: 'work_place',
                defaultContent: '',
                orderable: false
            },
            {
                data: 'constructive',
                defaultContent: '',
                orderable: false
            },
            {
                data: 'company_name',
                defaultContent: '',
                orderable: false,
            },
            {
                data: 'area_number',
                defaultContent: '',
                orderable: false,
            },
            {
                data: 'fio',
                defaultContent: '',
                orderable: false,
            },
            {
                data: 'phone',
                defaultContent: '',
                orderable: false,
            },
            {
                data: 'work_object',
                defaultContent: '',
                orderable: false
            },
            {
                data: 'job_desc',
                defaultContent: '',
                orderable: false
            },
            {
                data: 'assigned_completed',
                defaultContent: '',
                orderable: false
            },
            {
                data: 'checklist',
                defaultContent: '',
                orderable: false
            },
            {
                data: 'aok',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item) {
                    if (data == 0) {
                        return "Нет"
                    } else if (data == 1) {
                        return "Да"
                    } else {
                        return ""
                    }
                }

            },
            {
                data: 'act',
                defaultContent: '',
                orderable: false
            },
            {
                data: 'comment',
                defaultContent: '',
                orderable: false
            },
            {
                data: 'result',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item) {
                    return data == 0 ? "" : resultStages[data].name
                }
            },
            {
                data: '',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="btn-group">
                                <button 
                                    class="btn"
                                    data-js-update="${item.id}" 
                                    data-js-aok="${item.aok}"
                                    data-js-act="${item.act}"
                                    data-js-comment="${item.comment}"
                                    data-js-result="${item.result}"
                                    data-js-job-desc="${item.job_desc}"
                                    data-js-weather="${item.weather}"
                                    data-js-assigned-completed="${item.assigned_completed}"
                                    
                                    data-js-tg-id="${item.tg_id}"
                                    data-js-area-number="${item.area_number}"
                                    data-js-datetime="${item.datetime}"
                                    data-js-work-place="${item.work_place}"
                                    data-js-content="${item.content}"
                                    data-js-work-object="${item.work_object}"
                                    data-js-constructive="${item.constructive}"
                                    data-js-checklist="${item.checklist}"
                                >
                                    <i class="fa-solid fa-pen"></i>
                                </button>
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
            aria: {
                sortAscending: ': активировать для сортировки столбца по возрастанию',
                sortDescending: ': активировать для сортировки столбца по убыванию'
            }
        },
        buttons: [
            {
                extend: 'excel',
                text: '',
                title: 'Журнал поставщиков',
                exportOptions: {
                    stripHtml: false,
                    modifier: {
                        page: 'current'
                    },
                    columns: [1, 2, 3, 8, 4, 12, 13, 18],
                    format: {
                        body: function ( data, row, column) {
                           return column === 18
                               ? data.replace( /\n/g, String.fromCharCode(10))
                               : data.replace(/(&nbsp;|<([^>]+)>)/ig, "");
                        }
                    },
                    rows: function ( idx, data, node ) {
                        let comment = $(node).find("td").eq(18).text();

                        if (data["result"] != 0) {
                            let resultStage = resultStages[data["result"]].name

                            if (data["result"] == 1) {
                                data["comment"] = resultStage
                            } else {
                                data["comment"] = resultStage + '\n' + comment
                               // data["comment"] = comment
                            }
                        } else {
                            data["comment"] = comment
                        }

                        return true;

                    }
                },
            }
        ],
        lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
        pageLength: 25,
        order: [[ 1, "desc" ]],
        //dom: 'frt<"bottom"lip>',
        dom: 'Bfrt<"bottom"lip>',
        bSortCellsTop: true,
        scrollX:       true,
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

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

    $("[data-js-close-modal]").click(function (e) {
        $.magnificPopup.close();
    })

    // Добавить транспорт
    $("body").on('click', '[data-js-update]', function () {
        $.magnificPopup.open({
            items: {
                src: '#add-entry-modal-form',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true
        });

        let id = $(this).attr("data-js-update")
        $("#row_id").val(id)
        $("#assigned_completed").val($(this).attr("data-js-assigned-completed"))
        $("#act").val($(this).attr("data-js-act"))
        $("#aok").val($(this).attr("data-js-aok"))
        $("#comment").val($(this).attr("data-js-comment"))
        $("#result").val($(this).attr("data-js-result"))
        $("#job_desc").val($(this).attr("data-js-job-desc"))
        $("#weather").val($(this).attr("data-js-weather"))

        $("#tg_id").val($(this).attr("data-js-tg-id")).change()
        $("#area_number").val($(this).attr("data-js-area-number"))
        $("#datetime").val($(this).attr("data-js-datetime"))
        $("#work_place").val($(this).attr("data-js-work-place"))
        $("#content").val($(this).attr("data-js-content"))
        $("#work_object").val($(this).attr("data-js-work-object"))
        $("#constructive").val($(this).attr("data-js-constructive"))
        $("#checklist").val($(this).attr("data-js-checklist"))
    })

    $("body").on('click', '[data-js-status-btn]', function () {
        console.log($(this).attr("data-js-status"))

        $.magnificPopup.open({
            items: {
                src: '#status-modal',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true
        });

        $("#row_id").val($(this).attr("data-js-status-btn"))
        $("#status").val($(this).attr("data-js-status"))
    })

    $("#save-status").click(function (e) {
        $.ajax({
            url: '/ulab/contractor/updateStatusAjax/',
            data: {
                "row_id": $("#row_id").val(),
                "status": $("#status").val()
            },
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                console.log(data)
                tableJournal.ajax.reload()
                $('[data-js-close-modal]').click();

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

    $("#add-entry-modal-btn").click(function (e) {
        $.ajax({
            url: '/ulab/contractor/updateRowAjax/',
            data: $("#add-entry-modal-form").serialize(),
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                console.log(data)
                if (data) {
                    tableJournal.ajax.reload()
                    $('[data-js-close-modal]').click();
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
            $('.arrowLeft').css('transform',`translateY(${positionScroll-255}px)`);
        }
    })

    $('.filter').on('change', function () {
        tableJournal.ajax.reload()
        tableJournal.draw()
    })

    $("body").on('mouseover', '.block', function () {
        if (container.scrollLeft() === 0) {
            $(".dataTables_scrollBody").css("overflow", "visible");
            $(this).find(".image").css("display", "flex")
        }
    })

    $("body").on('mouseleave', '.block', function () {
        $(".dataTables_scrollBody").css("overflow", "auto");
        $(this).find(".image").css("display", "none")
    })
})