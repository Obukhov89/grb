$(function ($) {
    let resultStages = [];
    let fixedCol = 8;
    let cellArr = []
    $('#concreting_class').find('option').remove()
    let tableJournal = $('#volumes-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            url: '/ulab/journal/getConcretingJournal/',
            dataSrc: function (json) {
                console.log(json.data)
                return json.data
            }
        },
        createdRow: function (row, data, dataIndex) {
            console.log(data)
            if (data.concrete_type_id == 1) {
                $(row).css("background-color", "#FFC107")
            }
            if (data.concrete_type_id == 2) {
                $(row).css("background-color", "rgba(48,79,26,0.68)")
            }
            if (data.concrete_type_id == 3) {
                $(row).css("background-color", "rgba(168,248,109,0.82)")
            }
            if (data.concrete_type_id == 4) {
                $(row).css("background-color", "rgba(119,211,3)")
            }
        },
        drawCallback: function () {

        },
        columns: [
            {
                data: 'workType',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {
                    let content = `
                    <input type="number" name="id" id="id" value="${item.id}" hidden>
                    <input type="number" name="concreteTypeId" id="concreteTypeId" value="${item.concrete_type_id}" hidden>
                    <div class="text-center">
                    ${item.volumes_workType} ${item.project} ${item.constructive_name}</div>`;

                    return content;
                }
            },
            {
                data: 'precepts',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {
                    let content = `
                    <div class="text-center">
                    ${item.precepts}</div>`;

                    return content;
                }
            },
            {
                data: 'mark',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {
                    let content = `
                    <div class="text-center">
                    ${item.id}</div>`;

                    return content;
                }
            },
            {
                data: 'elimination_date',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {
                    let content = `
                    <div class="text-center">
                    ${formatDate(item.elimination_date)}</div>`;

                    return content;
                }
            },
            {
                data: 'concreting_class',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {
                    let content = `
                    <div class="form-group">
                      <select class="form-control" id="concreting_class">
                    
                      </select>
                    </div>
                    `;

                    performAsyncAjaxRequest('/ulab/journal/getConcretingType').then(function (response) {
                        response = JSON.parse(response);
                        let options = `<option selected disabled>Не выбрано</option>`;
                        response.forEach(function (i) {
                            if (i.id == item.concrete_type_id) {
                                options += `<option selected value="${i.id}">${i.name}</option>`
                            } else {
                                options += `<option value="${i.id}">${i.name}</option>`
                            }
                        })
                        $('#concreting_class').append(options);

                    })
                    return content;

                }
            },
            {
                data: 'seven_days_test',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {
                    let content = `
                    <div class="text-center">
                    ${addSevenDays(item.elimination_date)}</div>`;

                    return content;
                }
            },
            {
                data: 'twenty_eigth_days_test',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {
                    let content = `
                    <div class="text-center">
                    ${addTwentyEightDays(item.elimination_date)}</div>`;

                    return content;
                }

            },
            {
                data: 'seven_days_cube',
                defaultContent: '',
                orderable: false,
                className: 'data-edit-concreting seven_days_cube',
                render: function (data, type, item, meta) {
                    let bg = setColorWithStatus(item.concrete_sevenDaysCubeStatus)
                    let div = `<div class="text-center ${bg} data-js-seven-days-cube" style="border-radius: 10px !important"></div>`;
                    let id = item.concrete_sevenDaysCubeStatus ?? 1

                    performAsyncAjaxRequest(`/ulab/journal/getStatusBuId/`, {id: item.concrete_sevenDaysCubeStatus}).then(function (response) {
                        response = JSON.parse(response);
                        item.concrete_sevenDaysCubeProtocol === null ? $(`.data-js-seven-days-cube`).text(response.name) : $(`.data-js-seven-days-cube`).text(item.concrete_sevenDaysCubeProtocol)
                    })

                    return div;
                }

            },
            {
                data: 'twenty_eigth_days_cube',
                defaultContent: '',
                orderable: false,
                className: 'data-edit-concreting twenty_eigth_days_cube',
                render: function (data, type, item, meta) {
                    let bg = setColorWithStatus(item.concrete_twentyEigthDaysCubeStatus)
                    let div = `<div class="text-center ${bg} data-js-twenty-eigth-days-cube " style="border-radius: 10px !important"></div>`;
                    let id = item.concrete_twentyEigthDaysCubeStatus ?? 1

                    performAsyncAjaxRequest(`/ulab/journal/getStatusBuId/`, {id: item.concrete_twentyEigthDaysCubeStatus}).then(function (response) {
                        response = JSON.parse(response);
                        item.concrete_twentyEigthDaysCubeProtocol === null ? $(`.data-js-twenty-eigth-days-cube`).text(response.name) : $(`.data-js-twenty-eigth-days-cube`).text(item.concrete_twentyEigthDaysCubeProtocol)
                    })

                    return div;
                }

            },
            {
                data: 'seven_days_NK',
                defaultContent: '',
                orderable: false,
                className: 'data-edit-concreting seven_days_NK',
                render: function (data, type, item, meta) {
                    let bg = setColorWithStatus(item.concrete_sevenDaysNKStatus)
                    let div = `<div class="text-center ${bg} data-js-seven-days-NK" style="border-radius: 10px !important"></div>`;
                    let id = item.concrete_sevenDaysNKStatus ?? 1

                    performAsyncAjaxRequest(`/ulab/journal/getStatusBuId/`, {id: item.concrete_sevenDaysNKStatus}).then(function (response) {
                        response = JSON.parse(response);
                        item.concrete_sevenDaysNKProtocol === null ? $(`.data-js-seven-days-NK`).text(response.name) : $(`.data-js-seven-days-NK`).text(item.concrete_sevenDaysNKProtocol)
                    })

                    return div;
                }

            },
            {
                data: 'twenty_eigth_NK_status',
                defaultContent: '',
                orderable: false,
                className: 'data-edit-concreting twenty_eigth_NK',
                render: function (data, type, item, meta) {
                    let bg = setColorWithStatus(item.concrete_twentyEigthNKStatus)
                    let div = `<div class="text-center ${bg} data-js-twenty-eigth-NK" style="border-radius: 10px !important"></div>`;
                    let id = item.concrete_twentyEigthNKStatus ?? 1

                    performAsyncAjaxRequest(`/ulab/journal/getStatusBuId/`, {id: id}).then(function (response) {
                        response = JSON.parse(response);
                        item.concrete_twentyEigthDaysNKProtocol === null ? $(`.data-js-twenty-eigth-NK`).text(response.name) : $(`.data-js-twenty-eigth-NK`).text(item.concrete_twentyEigthDaysNKProtocol)
                    })

                    return div;
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

            aria: {
                sortAscending: ': активировать для сортировки столбца по возрастанию',
                sortDescending: ': активировать для сортировки столбца по убыванию'
            }
        },
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Все"]],
        pageLength: 10,
        fixedColumns: {
            leftColumns: fixedCol
        },
        order: [[1, "desc"]],
        dom: 'frtB<"bottom"lip>',
        bSortCellsTop: true,
        scrollX: true,
        fixedHeader: true,
        autoWidth: false,
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

    $('body').on('click', '.data-edit-concreting', function () {
        clearSelect();

        let id = $(this).closest('tr').find('#id').val();
        let elem = $(this).attr('class').split(' ')[2];
        $('[data-js-file-upload]').show();
        $('#editСoncreting [data-js-file]').addClass('d-none');
        // $('#editСoncreting [data-js-file-exists]').remove();
        if (!$(this).closest('tr').find('#concreteTypeId').val()) {
            let alertMessage = 'Пожалуйста, выберите класс бетона';
            $('.alert').text(alertMessage).removeClass('d-none').show();
            setTimeout(function () {
                $('.alert').hide().addClass('d-none');
            }, 3000);
        } else {
            performAsyncAjaxRequest(`${URI}/journal/editConcreting/`, {"diaryJournal": id, "elemType": elem})
                .then(response => {
                    response = JSON.parse(response);
                    console.log(response)
                    $('#editСoncreting #diaryJournal').val(response.concreting[0].diaryJournal);
                    $('#editСoncreting #elemType').val(response.elemType);
                    let options = `<option value="" selected disabled>Выберите статус</option>`

                    if (response.elemType == 'seven_days_cube') {
                        if (response.concreting[0].sevenDaysCubeProtocolUrl) {
                            $('[data-js-file-upload]').hide();
                            $('#editСoncreting [data-js-file]').removeClass('d-none');
                            $('#editСoncreting [data-js-file-exists]').html(
                               `<a href="${response.concreting[0].sevenDaysCubeProtocolUrl}" target="_blank">
                                    <i class="fas fa-file-invoice"></i>
                                </a>`
                            )
                        }
                        $('#numberProtocol').val(response.concreting[0].sevenDaysCubeProtocol);
                        response.status.forEach(item => {
                            if (response.concreting[0].sevenDaysCubeStatus == item.id) {
                                options += `<option selected value="${item.id}">${item.name}</option>`
                            } else {
                                options += `<option value="${item.id}">${item.name}</option>`
                            }
                        })
                    }

                    if (response.elemType == 'twenty_eigth_days_cube') {
                        $('#numberProtocol').val(response.concreting[0].twentyEigthDaysCubeProtocol);
                        if (response.concreting[0].twentyEigthDaysCubeUrl) {
                            $('[data-js-file-upload]').hide();
                            $('#editСoncreting [data-js-file]').removeClass('d-none');
                            $('#editСoncreting [data-js-file-exists]').html(
                                `<a href="${response.concreting[0].twentyEigthDaysCubeUrl}" target="_blank">
                                    <i class="fas fa-file-invoice"></i>
                                </a>`
                            )
                        }
                        response.status.forEach(item => {
                            if (response.concreting[0].twentyEigthDaysCubeStatus == item.id) {
                                options += `<option selected value="${item.id}">${item.name}</option>`
                            } else {
                                options += `<option value="${item.id}">${item.name}</option>`
                            }
                        })
                    }

                    if (response.elemType == 'seven_days_NK') {
                        if (response.concreting[0].sevenDaysNKProtocolUrl) {
                            $('[data-js-file-upload]').hide();
                            $('#editСoncreting [data-js-file]').removeClass('d-none');
                            $('#editСoncreting [data-js-file-exists]').html(
                                `<a href="${response.concreting[0].sevenDaysNKProtocolUrl}" target="_blank">
                                    <i class="fas fa-file-invoice"></i>
                                </a>`
                            )
                        }
                        $('#numberProtocol').val(response.concreting[0].sevenDaysNKProtocol);
                        response.status.forEach(item => {
                            if (response.concreting[0].sevenDaysNKStatus == item.id) {
                                options += `<option selected value="${item.id}">${item.name}</option>`
                            } else {
                                options += `<option value="${item.id}">${item.name}</option>`
                            }
                        })
                    }

                    if (response.elemType == 'twenty_eigth_NK') {
                        if (response.concreting[0].twentyEigthDaysNKProtocolUrl) {
                            $('[data-js-file-upload]').hide();
                            $('#editСoncreting [data-js-file]').removeClass('d-none');
                            $('#editСoncreting [data-js-file-exists]').html(
                                `<a href="${response.concreting[0].twentyEigthDaysNKProtocolUrl}" target="_blank">
                                    <i class="fas fa-file-invoice"></i>
                                </a>`
                            )
                        }
                        $('#numberProtocol').val(response.concreting[0].twentyEigthDaysNKProtocol);
                        response.status.forEach(item => {
                            if (response.concreting[0].twentyEigthNKStatus == item.id) {
                                options += `<option selected value="${item.id}">${item.name}</option>`
                            } else {
                                options += `<option value="${item.id}">${item.name}</option>`
                            }
                        })
                    }

                    $('#editСoncreting #status').append(options);
                    openMagnificPopup('#editСoncreting');
                })
                // fillFormInputs(response, 'editСoncreting')

                .catch(error => {

                })
        }
    })
    $('body').on('click', '[data-update-concreting]', function () {
        let fd = new FormData();
        $('#editСoncreting').find('input, select').each(function (index) {
            let type = $(this).attr("type");
            let files = [];
            if (type == "file") {
                $.each($(this)[0].files, function (i, file) {
                    fd.append(`${i}`, file);
                });
            } else {
                fd.append($(this).attr("name"), $(this).val());
            }
        })

        performAsyncAjaxRequest(`${URI}/journal/updateConcretingStatus/`, fd, true)
            .then(response => {
                response = JSON.parse(response);
                tableJournal.ajax.reload();
                console.log(response);
                $.magnificPopup.close();
            })
            .catch(error => {

            });
    })

    $('body').on('change', '#concreting_class', function () {
        let id = $(this).closest('tr').find('#id').val();
        let data = {
            'concrete': $(this).val(),
            'diaryJournal': id
        }
        performAsyncAjaxRequest(`${URI}/Journal/updateConcretingType/`, data)
            .then(response => {
                tableJournal.ajax.reload();
            })
            .catch(error => {
            })
    })
})

function openMagnificPopup(sourceId) {
    $.magnificPopup.open({
        items: {
            src: sourceId,
            type: 'inline'
        },
        fixedContentPos: false,
        modal: true,
    });
}

function performAsyncAjaxRequest(url, data, file = false) {
    let fileSettings = {};
    if (file) {
        fileSettings = {
            processData: false,
            contentType: false,
        }
    }
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            method: 'POST',
            ...fileSettings,
            data: data,
            success: resolve,
            error: reject
        });
    });
}

function fillFormInputs(data, formId) {
    for (let key in data) {
        if (data.hasOwnProperty(key)) {
            let input = $(`#${formId}`).find('input[name="' + key + '"]');
            if (input.length > 0) {
                input.val(data[key]);
            }
        }
    }
}

function validateRequiredFields(requiredFields, delayTime) {
    let emptyFields = [];

    requiredFields.each(function () {
        if ($(this).val() === '') {
            let labelFor = $(this).attr('id');
            let label = $('label[for="' + labelFor + '"]').text();
            emptyFields.push(label);
            $(this).addClass('is-invalid');
            setTimeout(() => {
                $(this).removeClass('is-invalid');
            }, delayTime);
        }
    });

    return emptyFields;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const formattedDate = `${date.getDate().toString().padStart(2, '0')}.${(date.getMonth() + 1).toString().padStart(2, '0')}.${date.getFullYear()}`;
    return formattedDate;
}

function addSevenDays(dateString) {
    const date = new Date(dateString);
    date.setDate(date.getDate() + 7);
    const formattedDate = `${date.getDate().toString().padStart(2, '0')}.${(date.getMonth() + 1).toString().padStart(2, '0')}.${date.getFullYear()}`;
    return formattedDate;
}

function addTwentyEightDays(dateString) {
    const date = new Date(dateString);
    date.setDate(date.getDate() + 28);
    const formattedDate = `${date.getDate().toString().padStart(2, '0')}.${(date.getMonth() + 1).toString().padStart(2, '0')}.${date.getFullYear()}`;
    return formattedDate;
}

function clearSelect() {
    $('#editСoncreting #status').find('option').remove()
}

function setColorWithStatus(status) {
    switch (status) {
        case "2":
            return 'blue-status';
        case "3":
            return 'red-status';
        case "4":
            return 'purple-status';
        default:
            return 'yellow-status';
    }

}