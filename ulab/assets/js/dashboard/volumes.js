$(function ($) {
    let resultStages = [];
    let fixedCol = 8;
    let cellArr = [];
    let countColumns = $('#countColumns').val();
    let workJournalData = [];

    for (let i = 0; i < countColumns; i++) {
        let obj = {
            data: '',
            defaultContent: '',
            orderable: false,
            render: function (data, type, item, meta) {
                let content = '';
                let columnData = meta.settings.aoColumns[meta.col].sTitle;
                console.log(columnData)
                workJournal.forEach(function (key) {
                    if (key.project === item.sectionNumber && key.work_type === item.workType && key.constructive_name === item.structureName) {
                        if (key.formatted_date === columnData) {
                            content = `<div class="text-center">${key.total_volume}</div>`;
                        }
                    }
                });
                return content;
            }
        };
        workJournalData.push(obj);
    }

    let tableJournal = $('#volumes-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type: 'POST',
            url: '/ulab/Dashboard/indexVolumes/',
            dataSrc: function (data) {
                console.log(data)
                workJournal = data?.workJournal
                dashboard = data?.dashboard
                return data?.dashboard
            }
        },
        columns: [
            {
                data: '',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item) {
                    let content = `
                        <div style="margin-right: 10px; width:10%">
                            <div class="fa-solid fa-pen-to-square" style="cursor:pointer;font-size:18px" data-edit-volume></div>
                        </div>
                        `;

                    return content
                }
            },
            {
                data: 'sectionNumber',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {
                    let content = `<input type="number" name="id" id="id" value="${item.id}" hidden>
                    <div class="text-center">
                    ${item.sectionNumber}</div>`;

                    return content;
                }
            },
            {
                data: 'workType',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {
                    let content = `
                    <div class="text-center">
                    ${item.workType}</div>`;

                    return content;
                    }
            },
            {
                data: 'structureName',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {
                    let content = `
                    <div class="text-center">
                    ${item.structureName}</div>`;

                    return content;
                }
            },
            {
                data: 'unitName',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {
                    let content = `
                    <div class="text-center">
                    ${item.unitName}</div>`;

                    return content;
                }
            },
            {
                data: 'projectVolumes',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {
                    let content = `
                    <div class="text-center">
                    ${item.projectVolumes}</div>`;

                    return content;
                }
            },
            {
                data: '',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {

                    let total = 0 ;
                    workJournal.forEach(function (key) {
                        if (key.project === item.sectionNumber && key.work_type === item.workType && key.constructive_name === item.structureName) {
                            total += parseFloat(key.total_volume)
                        }
                    });
                    let content = `
                    <div class="text-center">
                    ${total}</div>`;

                    return content;
                }
            },
            {
                data: '',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {

                    let total = 0;
                    let projectVolumes = parseFloat(item.projectVolumes)
                    workJournal.forEach(function (key) {
                        if (key.project === item.sectionNumber && key.work_type === item.workType && key.constructive_name === item.structureName) {
                            total += parseFloat(key.total_volume)
                        }
                    });
                    let content = `
                    <div class="text-center">
                    ${projectVolumes - total}</div>`;

                    return content;
                }

            }, {
                data: '',
                defaultContent: '',
                orderable: false,
                render: function (data, type, item, meta) {

                    let total = 0;
                    let projectVolumes = parseFloat(item.projectVolumes)
                    workJournal.forEach(function (key) {
                        if (key.project === item.sectionNumber && key.work_type === item.workType && key.constructive_name === item.structureName) {
                            total += parseFloat(key.total_volume)
                        }
                    });
                    let value = parseInt(total / projectVolumes * 100)
                    let content = `
                 <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="${value}" aria-valuemin="0" aria-valuemax="100" style="width:${value}%">${value}%</div>
                 </div>
                `
                    return content;
                }

            },
            ...workJournalData,
            {
                data: '',
                defaultContent: '',
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
        pageLength: 25,
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

    $("body").on('click', '[data-create-volume]', function () {

        let option = `<option value="" selected disabled>Выберите еденицу измерения</option>`
        performAsyncAjaxRequest(`${URI}/dashboard/createVolume/`)
            .then(response => {
                let param = JSON.parse(response);
                console.log(param)
                param['units'].forEach(item => {
                    option += `<option value="${item.id}">${item.name}</option>`;
                })
                $(`#unit`).html(option);
            })
            .catch(error => {

            })
        openMagnificPopup('#createVolume');
    });

    $('body').on('click', '[data-store-volume]', function () {
        let data = $('#createVolume').find('input,select').serializeArray();
        let requiredFields = $('#createVolume').find('input[required], select[required]');
        let emptyFields = [];

        emptyFields = validateRequiredFields(requiredFields, 3000);

        if (emptyFields.length > 0) {
            let alertMessage = 'Пожалуйста, заполните обязательные поля';
            $('.alert').text(alertMessage).removeClass('d-none').show();
            setTimeout(function () {
                $('.alert').hide().addClass('d-none');
            }, 3000);
        } else {
            performAsyncAjaxRequest(`${URI}/dashboard/storeVolumes/`, data)
                .then(response => {
                    tableJournal.ajax.reload();
                    console.log(data)
                    $.magnificPopup.close();

                })
                .catch(error => {

                })
        }
    });

    $('body').on('click', '[data-edit-volume]', function () {
        let id = $(this).closest('tr').find('#id').val();
        $('#editVolume').find('#id').val(id);
        performAsyncAjaxRequest(`${URI}/dashboard/editVolumes/`, {"id": id})
            .then(response => {
                response = JSON.parse(response);
                let current_unit = response['unitId'];
                fillFormInputs(response, 'editVolume')
                openMagnificPopup('#editVolume');
                performAsyncAjaxRequest(`${URI}/dashboard/getUnits/`)
                    .then(response => {
                        let option = ``;
                        response = JSON.parse(response);
                        response.forEach(item => {
                            console.log(item)
                            if (current_unit === item.id) {
                                option += `<option value="${item.id}" selected>${item.name}</option>`
                            }
                            option += `<option value="${item.id}">${item.name}</option>`
                        })
                        console.log(option)
                        $(`#unitName`).html(option);
                    })
            })
            .catch(error => {

            })
    })

    $('body').on('click', '[data-update-volume]', function () {
        let data = $('#editVolume').find('input,select').serializeArray();
        let requiredFields = $('#editVolume').find('input[required]');
        let emptyFields = [];

        emptyFields = validateRequiredFields(requiredFields, 3000);

        if (emptyFields.length === 0) {
            performAsyncAjaxRequest(`${URI}/dashboard/updateVolumes/`, data)
                .then(response => {
                    tableJournal.ajax.reload(false, false, true);
                    console.log(data);
                    $.magnificPopup.close();
                })
                .catch(error => {

                });
        }
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

function performAsyncAjaxRequest(url, data) {
    return new Promise((resolve, reject) => {
        $.ajax({
            url: url,
            method: 'POST',
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
