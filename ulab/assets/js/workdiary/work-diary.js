$(function ($) {
    let resultStages = [];
    /*journal requests*/
    let tableJournal = $('#work-diary-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            data: function (d) {
                d.dateStart = $('#inputDateStart').val()
                d.dateEnd = $('#inputDateEnd').val()
                d.stage = $('#selectStage option:selected').val()
                d.lab = $('#selectLab option:selected').val()
                d.everywhere = $('#filter_everywhere').val()
            },
            url: '/ulab/workDiary/getListProcessingAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: '',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="btn-group">
                                <button data-js-edit-journal-row data-js-row-id="${item.id}" title="Редактировать" class="btn"><i class="fa-solid fa-pen"></i></button>
                                <button data-js-delete-journal-row data-js-row-id="${item.id}" title="Удалить" class="btn"><i class="fa-solid fa-trash-can"></i></button>
                           </div>`
                }
            },
            {
                data: 'order_number',
                defaultContent: '',
                orderable: false
            },
            {
                data: 'inspection_date',
                defaultContent: '',
                orderable: true,
                render: function (data, type, item) {
                    return moment(data).format("DD.MM.YYYY");
                }
            },
            {
                data: 'file_path',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item) {
                    if (data) {
                        return `<div class="block">
                                <a href="${data}" target="_blank"></a>
                                <div class="image" style="z-index: 9999;">
                                    <img src="${data}" width="300" height="400" alt="Фото">
                                </div>
                            </div>`;
                    } else {
                        return ``;
                    }
                }
            },
            {
                data: 'contractor_name',
                defaultContent: '',
                orderable: true
            },
            {
                data: 'sectionNumber',
                defaultContent: '',
                orderable: true
            },
            {
                data: 'workType',
                defaultContent: '',
                orderable: true
            },
            {
                data: 'structureName',
                defaultContent: '',
                orderable: true
            },
            {
                data: 'kind_of_work_name',
                defaultContent: '',
                orderable: true
            },
            {
                data: 'coordinates',
                defaultContent: '',
                orderable: true
            },
            {
                data: 'acceptance_results',
                defaultContent: '',
                orderable: true,
                render: function (data, type, item) {
                    if (data === "0") {
                        return "Не принято";
                    }

                    return "Принято";
                }
            },
            {
                data: 'precepts',
                defaultContent: '',
                orderable: true
            },
            {
                data: 'сontent_of_remarks',
                defaultContent: '',
                orderable: true
            },

            {
                data: 'technical_regulations',
                defaultContent: '',
                orderable: true
            },
            {
                data: 'elimination_date',
                defaultContent: '',
                orderable: true,
                render: function (data, type, item) {
                    return moment(data).format("DD.MM.YYYY");
                }
            },
            {
                data: 'elimination_remark',
                defaultContent: '',
                orderable: true,
                render: function (data, type, item) {
                    if (item.elimination_remark === "1") {
                        return `<span><i class="fa-regular fa-square-check"></i></span>`;
                    }

                    return `<span><i class="fa-regular fa-square"></i></span>`;
                }
            },
            {
                data: 'note',
                defaultContent: '',
                orderable: true,
            },
            {
                data: 'volume',
                defaultContent: '',
                orderable: true,
            },
            {
                data: 'responsible_for_acceptance_name',
                defaultContent: '',
                orderable: true,
            },
            {
                data: '',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="btn-group">
                                <button data-js-edit-journal-row data-js-row-id="${item.id}" title="Редактировать" class="btn"><i class="fa-solid fa-pen"></i></button>
                                <button data-js-delete-journal-row data-js-row-id="${item.id}" title="Удалить" class="btn"><i class="fa-solid fa-trash-can"></i></button>
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
        },
        buttons: [
            {
                extend: 'excel',
                text: '',
                titleAttr: 'excel',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                }
            },
        ],
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        pageLength: 25,
        order: [],
        //dom: 'frt<"bottom"lip>',
        dom: 'frtB<"bottom"lip>',
        bSortCellsTop: true,
        scrollX: true,
        fixedHeader: false,
        // fixedHeader:   true,
        autoWidth: false
    });

    tableJournal.columns().every(function () {
        $(this.header()).closest('thead').find('.search:eq(' + this.index() + ')').on('keyup change clear', function () {
            tableJournal
                .column($(this).parent().index())
                .search(this.value)
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

    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $('#work-diary-table').width()

    $('.btnRightTable, .arrowRight').hover(function () {
            container.animate(
                {
                    scrollLeft: scroll
                },
                {
                    duration: 4000, queue: false
                }
            )
        },
        function () {
            container.stop();
        })

    $('.btnLeftTable, .arrowLeft').hover(function () {
            container.animate(
                {
                    scrollLeft: -scroll
                },
                {
                    duration: 4000, queue: false
                }
            )
        },
        function () {
            container.stop();
        });

    $(document).scroll(function () {
        let positionScroll = $(window).scrollTop(),
            tableScrollBody = container.height();

        if (positionScroll > 265 && positionScroll < tableScrollBody) {
            $('.arrowRight').css('transform', `translateY(${positionScroll - 260}px)`);
            $('.arrowLeft').css('transform', `translateY(${positionScroll - 255}px)`);
        }
    });

    $('.filter').on('change', function () {
        tableJournal.ajax.reload();
        tableJournal.draw();
    });

    $("body").on('mouseover', '.block', function () {
        if (container.scrollLeft() === 0) {
            $(".dataTables_scrollBody").css("overflow", "visible");
            $(this).find(".image").css("display", "flex");
        }
    });

    $("body").on('mouseleave', '.block', function () {
        $(".dataTables_scrollBody").css("overflow", "auto");
        $(this).find(".image").css("display", "none");
    });

    $("body").on('click', '[data-js-create-row]', function () {
        $.magnificPopup.open({
            items: {
                src: '#create_row',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });
    });

    $("body").on('click', '#elimination_remark_input', function (event) {
        let isChecked = !$(this).prop("checked");
        let conf = confirm("Вы уверены?");
        if (conf) {
            $(this).prop("checked", !isChecked);
            $(this).attr("checked", !isChecked);
        } else {
            event.preventDefault();
        }
    });

    function cleanSelect(id) {
        $(`#${id}`).empty().append('<option value="0">Выбрать</option>');
    }

    $("body").on('change', '#project_input', function (event) {
        cleanSelect("work_type_input");
        cleanSelect("constructive_name_input");

        const url = "/ulab/workDiary/getWorkType";
        const data = {
            'project_name': $(event.target).val(),
        };

        $.post(url, data, function (response) {
            response = JSON.parse(response);
            let data = response.data;

            let html = "";
            for (const elem of data) {
                html += `<option value="${elem.workType}">${elem.workType}</option>`;
            }

            $(`#work_type_input`).empty().append('<option value="0">Выбрать</option>').append(html);
        });
    });

    $("body").on('change', '#work_type_input', function (event) {
        cleanSelect("constructive_name_input");

        const url = "/ulab/workDiary/getConstructiveName";
        const data = {
            'project_name': $("#project_input").val(),
            'work_type': $(event.target).val(),
        };

        $.post(url, data, function (response) {
            response = JSON.parse(response);
            let data = response.data;

            let html = "";
            for (const elem of data) {
                html += `<option value="${elem.structureName}">${elem.structureName}</option>`;
            }

            $(`#constructive_name_input`).empty().append('<option value="0">Выбрать</option>').append(html);
        });
    });

    $("body").on('mouseover', '.block', function () {
        if (container.scrollLeft() === 0) {
            $(".dataTables_scrollBody").css("overflow", "visible");
            $(this).find(".image").css("display", "flex");
        }
    });

    $("body").on('mouseleave', '.block', function () {
        $(".dataTables_scrollBody").css("overflow", "auto");
        $(this).find(".image").css("display", "none");
    });


    $("body").on('click', '[data-js-delete-journal-row]', function () {
        let conf = confirm("Удалить запись?");
        if (!conf)
            return;

        let url = "/ulab/workDiary/deleteJournal";
        let data = {
            'id': $(this).attr("data-js-row-id"),
        };

        $.post(url, data, function (response) {
            if (!response.error) {
                tableJournal.draw();
                return;
            }
        });
    });

    $("body").on('click', '[data-js-edit-journal-row]', function () {

        const rowId = $(this).attr("data-js-row-id");

        const url = "/ulab/workDiary/getJournalById";
        const data = {
            'id': rowId
        };

        $.magnificPopup.open({
            items: {
                src: '#edit_row',
                type: 'inline'
            },
            fixedContentPos: false,
            modal: true,
        });

        $.post(url, data, function (response) {
            let data = (JSON.parse(response)).data;
            let journalData = data.journal;

            $('#order_number').text(journalData.order_number);
            $('#row_id').val(journalData.id);

            $("#inspection_date_input").val(journalData.inspection_date);
            $("#coordinates_input").val(journalData.coordinates);
            $("#precepts_input").val(journalData.precepts);
            $("#сontent_of_remarks_input").val(journalData.сontent_of_remarks);
            $("#technical_regulations_input").val(journalData.technical_regulations);
            $("#elimination_date_input").val(journalData.elimination_date);
            $("#note_input").val(journalData.note);
            $("#volume").val(journalData.volume);

            if (journalData.elimination_remark === "1") {
                $("#elimination_remark_input").attr("checked", true);
            }

            fillContractorSelect(data.contractors_handbook);
            $(`#сontractor_input option[value="${journalData.contractor_id}"]`).attr('selected', true);

            fillKindOfWorkSelect(data.kind_of_work_handbook);
            $(`#kind_of_work_input option[value="${journalData.kind_of_work_id}"]`).attr('selected', true);

            $(`#acceptance_results_input option[value="${journalData.acceptance_results}"]`).attr('selected', true);

            fillResponsibleSelect(data.responsible_for_acceptance_handbook);
            $(`#responsible_for_acceptance_input option[value="${journalData.responsible_for_acceptance_id}"]`).attr('selected', true);

            $("#photo_path").attr("src", journalData.file_path);
            $("#photo_link").attr("href", journalData.file_path);

            fillProjectSelect(data.project_data);
            $(`#project_input option[value="${journalData.sectionNumber}"]`).attr('selected', true);

            fillWorkTypeSelect(data.work_type);
            $(`#work_type_input option[value="${journalData.workType}"]`).attr('selected', true);

            fillConstructiveSelect(data.constructive_name);
            $(`#constructive_name_input option[value="${journalData.structureName}"]`).attr('selected', true);
        });
    });

    function fillConstructiveSelect(constructives) {
        cleanSelect("constructive_name_input");
        let html = "";
        for (const elem of constructives) {
            html += `<option value="${elem.structureName}">${elem.structureName}</option>`;
        }

        $(`#constructive_name_input`).empty().append('<option value="0">Выбрать</option>').append(html);
    }

    function fillWorkTypeSelect(workType) {
        cleanSelect("work_type_input");
        let html = "";
        for (const elem of workType) {
            html += `<option value="${elem.workType}">${elem.workType}</option>`;
        }

        $(`#work_type_input`).empty().append('<option value="0">Выбрать</option>').append(html);
    }

    function fillProjectSelect(projects) {
        cleanSelect("project_input");
        let html = "";
        for (const elem of projects) {
            html += `<option value="${elem.section_number}">${elem.section_number}</option>`;
        }

        $(`#project_input`).empty().append('<option value="0">Выбрать</option>').append(html);
    }

    function fillResponsibleSelect(responsibles) {
        cleanSelect("responsible_for_acceptance_input");
        let html = "";
        for (const elem of responsibles) {
            html += `<option value="${elem.id}">${elem.name}</option>`;
        }

        $(`#responsible_for_acceptance_input`).empty().append('<option value="0">Выбрать</option>').append(html);
    }

    function fillKindOfWorkSelect(kind_of_work) {
        cleanSelect("kind_of_work_input");
        let html = "";
        for (const elem of kind_of_work) {
            html += `<option value="${elem.id}">${elem.name}</option>`;
        }

        $(`#kind_of_work_input`).empty().append('<option value="0">Выбрать</option>').append(html);
    }

    function fillContractorSelect(contractors) {
        cleanSelect("сontractor_input");
        let html = "";
        for (const elem of contractors) {
            html += `<option value="${elem.id}">${elem.contractor_name}</option>`;
        }

        $(`#сontractor_input`).empty().append('<option value="0">Выбрать</option>').append(html);
    }
})