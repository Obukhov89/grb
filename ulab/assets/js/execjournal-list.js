$(function ($) {
    let $journal = $('#journal_protocol')

    /*journal requests*/
    let journalDataTable = $journal.DataTable({
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
            url: '/ulab/execJournal/getListProcessingAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'edit',
                render: function (data, type, item) {
                    return `<div data-js-update="" style="cursor: pointer;" data-id="${item.id}" data-contractor_id="${item.contractor_id}" title="Редактировать" class="action_button"><i class="fa-solid fa-pen"></i></div>`;
                },
                orderable: false,
            },
            {
                data: 'status',
                render: function (data, type, item) {
                    return `<div class="stage rounded bg-secondary" data-id="${item.id}" data-place="status_${item.id}" ></div>`;
                },
                orderable: false,
            },
            {
                data: 'application_number',
                render: function (data, type, item) {
                    let styles = "";
                    if (parseFloat(item.closed)) {
                        styles += "text-decoration: line-through !important;";
                    }

                    return `<a class="text-decoration-none" style="${styles}" href="/ulab/applicationCard/index/${item.contractor_id}" data-id="${item.id}">${data}</a>`;
                },
                orderable: false,
            },
            {
                data: 'acceptance_date',
                orderable: false,
            },
            {
                data: 'work_name',
                orderable: false,
            },
            {
                data: 'work_place',
                orderable: false,
            },
            {
                data: 'project',
                render: function (data, type, item) {
                    let value = item.project;

                    return `<input class="form-control" type="text" name="project"  value="${value}" data-id="${item.id}" />`;
                },
                orderable: false,
            },
            {
                data: 'act',
                render: function (data, type, item) {
                    let value = item.act;

                    return `
                        <select class="form-control" style="width: 100px;" name="act" data-id="${item.id}">
                        <option ${selectValue(0, value)} value="0">Выбрать</option>
                        <option ${selectValue(1, value)} value="1">АОСР</option>
                        <option ${selectValue(2, value)} value="2">АООК</option>
                        <option ${selectValue(3, value)} value="3">Не требуется</option>
                        </select>
                    `;
                }
            },
            {
                data: 'executive_scheme',
                render: function (data, type, item) {
                    let value = item.executive_scheme;
                    let checked = isChecked(value);

                    return `<div style="text-align: center;"><input type="checkbox" name="executive_scheme" ${checked} data-id="${item.id}" /></div>`;
                }
            },
            {
                data: 'materials_used',
                render: function (data, type, item) {
                    let value = item.materials_used;
                    let checked = isChecked(value);

                    return `<div style="text-align: center;"><input type="checkbox" name="materials_used" ${checked} data-id="${item.id}" /></div>`;
                }
            },
            {
                data: 'quality_document',
                render: function (data, type, item) {
                    let value = item.quality_document;
                    let checked = isChecked(value);

                    return `<div style="text-align: center;"><input type="checkbox" name="quality_document" ${checked} data-id="${item.id}" /></div>`;
                }
            },
            {
                data: 'avk_for_materials',
                render: function (data, type, item) {
                    let value = item.avk_for_materials;
                    let checked = isChecked(value);

                    return `<div style="text-align: center;"><input type="checkbox" name="avk_for_materials" ${checked} data-id="${item.id}" /></div>`;
                }
            },
            {
                data: 'protocols_conclusions_acts',
                render: function (data, type, item) {
                    let value = item.protocols_conclusions_acts;
                    let checked = isChecked(value);

                    return `<div style="text-align: center;"><input type="checkbox" name="protocols_conclusions_acts" ${checked} data-id="${item.id}" /></div>`;
                }
            },
            {
                data: 'volumes',
                render: function (data, type, item) {
                    let value = item.volumes;

                    return `<input class="form-control" type="text" name="volumes"  value="${value}" data-id="${item.id}" />`;
                },
                orderable: false,
            },
            {
                data: 'summa',
                render: function (data, type, item) {
                    let value = item.summa;

                    return `<input class="form-control" type="number" name="summa"  value="${value}" data-id="${item.id}" />
                               <input type="hidden" value="${item.closed}" name="is_card_closed" id="is_card_closed" />`;
                },
                orderable: false,
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
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        pageLength: 25,
        order: [],
        colReorder: true,
        dom: 'frtB<"bottom"lip>',
        buttons: [
            { //reseted_button
                text: '<i class="fa-solid fa-table"></i>',
                titleAttr: 'Редактор схем',
                className: "dt-buttons btn-group flex-wrap",
                action: () => {
                    window.location.href = "/ulab/schemeEditor/index";
                }
            },
            // {
            //     text: '<i class="fa-solid fa-print"></i>',
            //     titleAttr: 'Вывод суммы',
            //     className: "dt-buttons btn-group flex-wrap",
            //     action: () => {
            //         window.location.href = "/ulab/execJournal/printSumByMonth";
            //     }
            // },
            // {
            //     extend: 'colvis',
            //     titleAttr: 'Выбрать'
            // },
            // {
            //     extend: 'copy',
            //     titleAttr: 'Копировать',
            //     exportOptions: {
            //         modifier: {
            //             page: 'current'
            //         }
            //     }
            // },
            {
                extend: 'excel',
                titleAttr: 'excel',
                exportOptions: {
                    modifier: {
                        page: 'current'
                    }
                },
                customizeData: function (excelData) {
                    let rows = excelData.body;
                    let checkedCheckboxText = "Да";
                    excelData.header[15] = "Закрыта?";

                    for (let i = 0; i < rows.length; i++) {
                        // второй индекс здесь это номер столбца.
                        rows[i][14] = $(".journal td [name='summa']").eq(i).val();
                        rows[i][13] = $(".journal td [name='volumes']").eq(i).val();
                        rows[i][12] = $(".journal td [name='protocols_conclusions_acts']").eq(i).prop("checked") ? checkedCheckboxText : "";
                        rows[i][11] = $(".journal td [name='avk_for_materials']").eq(i).prop("checked") ? checkedCheckboxText : "";
                        rows[i][10] = $(".journal td [name='quality_document']").eq(i).prop("checked") ? checkedCheckboxText : "";
                        rows[i][9] = $(".journal td [name='materials_used']").eq(i).prop("checked") ? checkedCheckboxText : "";
                        rows[i][8] = $(".journal td [name='executive_scheme']").eq(i).prop("checked") ? checkedCheckboxText : "";
                        rows[i][7] = $(".journal td [name='act'] option:selected").eq(i).text();
                        rows[i][6] = $(".journal td [name='project']").eq(i).val();
                        rows[i][15] = $(".journal td [name='is_card_closed']").eq(i).val() === "1" ? "Да" : "Нет";
                    }
                }
            }
        ],
        bSortCellsTop: true,
        scrollX: true,
        fixedHeader: false,
        fnDrawCallback: function (oSettings) {
            updateStatus();
        }
    });

    journalDataTable.on('change', 'input[type="text"], input[type="number"], input[type="checkbox"], select', function (event) {
        let value;
        if (event.target.getAttribute('type') === "checkbox") {
            value = Number($(event.target).is(':checked'));
        } else {
            value = $(event.target).val();
        }

        let id = event.target.dataset.id;
        let name = event.target.name;

        let url = "/ulab/execJournal/editJournal";
        let data = {
            "id": id,
            "name": name,
            "value": value,
        }

        $.post(url, data, (response) => {
            response = JSON.parse(response);

            if (!response.error) {
                // checkForStatus(id);
                updateStatus();
            }
        });
    });

    function updateStatus(id) {
        // TODO: это можно переделать для обновления 1 строки при её изменении, а не всей таблицы целиком
        let table = journalDataTable;
        let numberOfRows = table.data().length;

        let statusBlocks = $('.stage.rounded');
        for (let i = 0; i < numberOfRows; i++) {
            const trElem = table.row(i).node();

            let textInputs = $('input[type="text"]', trElem);
            let checkboxInputs = $('input[type="checkbox"]', trElem);
            let selects = $('select', trElem);

            let fieldsCount = textInputs.length + checkboxInputs.length + selects.length;
            let progress = 0;


            $(textInputs).each((i) => {
                if ($(textInputs[i]).val().trim().length > 0) {
                    progress++;
                }
            });

            $(checkboxInputs).each((i) => {
                if ($(checkboxInputs[i]).is(":checked")) {
                    progress++;
                }
            });

            $(selects).each((index) => {
                if ($(selects[index]).children("option:selected").val() != 0) {
                    progress++;
                }
            });


            if (progress === 0) {
                $(statusBlocks[i])
                    //$(`[data-place="status_${id}"]`)
                    .removeClass("bg-warning")
                    .removeClass("bg-success")
                    .removeClass("bg-secondary")
                    .addClass("bg-secondary")
            } else if (progress === fieldsCount) {
                $(statusBlocks[i])
                    //$(`[data-place="status_${id}"]`)
                    .removeClass("bg-warning")
                    .removeClass("bg-success")
                    .removeClass("bg-secondary")
                    .addClass("bg-success")
            } else {
                $(statusBlocks[i])
                    //$(`[data-place="status_${id}"]`)
                    .removeClass("bg-warning")
                    .removeClass("bg-success")
                    .removeClass("bg-secondary")
                    .addClass("bg-warning")
            }
        }
    }

    journalDataTable.columns().every(function () {
        $(this.header()).closest('thead').find('.search:eq(' + this.index() + ')').on('keyup change clear', function () {
            journalDataTable
                .column($(this).parent().index())
                .search(this.value)
                .draw();
        })
    });

    /*journal filters*/
    $('.filter-btn-search').on('click', function () {
        $('#journal_filter').addClass('is-open')
        $('.filter-btn-search').hide()
    })

    $('.filter').on('change', function () {
        journalDataTable.ajax.reload()
        journalDataTable.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    })

    /*journal buttons*/
    let container = $('div.dataTables_scrollBody'),
        scroll = $journal.width()

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
        })

    $(document).scroll(function () {
        let positionScroll = $(window).scrollTop(),
            tableScrollBody = container.height()

        if (positionScroll > 265 && positionScroll < tableScrollBody) {
            $('.arrowRight').css('transform', `translateY(${positionScroll - 260}px)`);
            $('.arrowLeft').css('transform', `translateY(${positionScroll - 250}px)`);
        }
    });

    function selectValue(optionValue, value) {
        return optionValue === parseInt(value) ? 'selected="selected"' : '';
    }

    function isChecked(value) {
        return value === "1" ? "checked" : "";
    }

    $("body").on('click', '[data-js-update]', function () {
        let rowId = this.dataset.contractor_id;
        $('#row_id').val(rowId);
        $.magnificPopup.open({
            items: {
                src: '#edit_journal',
                type: 'inline'
            },
            fixedContentPos: false
        });

        let url = "/ulab/execJournal/getJournalRow/";
        let data = {
            "rowId": rowId,
        };

        $.post(url, data, function (response) {
            response = JSON.parse(response);

            $('#app_number').text(response.application_number);

            cleanForm();
            $('#edit_journal [name="project"]').val(response.project);
            $('#edit_journal [name="volumes"]').val(response.volumes);
            $('#edit_journal [name="summa"]').val(response.summa);

            if (response.executive_scheme != "0" && response.executive_scheme != null) {
                $('#edit_journal [name="executive_scheme"]').prop('checked', true);
            }
            if (response.materials_used != "0" && response.materials_used != null) {
                $('#edit_journal [name="materials_used"]').prop('checked', true);
            }
            if (response.quality_document != "0" && response.quality_document != null) {
                $('#edit_journal [name="quality_document"]').prop('checked', true);
            }
            if (response.avk_for_materials != "0" && response.avk_for_materials != null) {
                $('#edit_journal [name="avk_for_materials"]').prop('checked', true);
            }
            if (response.protocols_conclusions_acts != "0" && response.protocols_conclusions_acts != null) {
                $('#edit_journal [name="protocols_conclusions_acts"]').prop('checked', true);
            }

            if (response.act != null) {
                $(`#edit_journal select[name='act'] option[value=${response.act}]`).attr('selected', 'true')
            }


        });
    });

    function cleanForm() {
        $('#edit_journal [name="project"]').val(null);
        $('#edit_journal [name="volumes"]').val(null);
        $('#edit_journal [name="summa"]').val(null);
        $('#edit_journal [name="executive_scheme"]').prop('checked', false);
        $('#edit_journal [name="materials_used"]').prop('checked', false);
        $('#edit_journal [name="quality_document"]').prop('checked', false);
        $('#edit_journal [name="avk_for_materials"]').prop('checked', false);
        $('#edit_journal [name="protocols_conclusions_acts"]').prop('checked', false);
        $(`#edit_journal select[name='act'] option`).attr('selected', false);
        $(`#edit_journal select[name='act'] option[value="0"]`).attr('selected', 'selected');
    }
})