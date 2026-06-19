$(function () {
    let $body = $('body'),
        $journal = $('#methodComponentList');

    $('#selectMethods, #selectComponents').select2({
        theme: 'bootstrap-5',
        width: 'resolve',
    });

    let journalDataTable = $journal.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.stage = $('#selectStage option:selected').val();
                d.lab = $('#selectLab option:selected').val();
            },
            url : '/ulab/vlk/getMethodListAjax/',
            dataSrc: function (json) {
                return json.data;
            }
        },
        columns: [
            {
                "className":      'details-control',
                "orderable":      false,
                "data":           null,
                "defaultContent": ''
            },
            {
                data: 'name',
                render: function (data, type, item) {
                    return `<a href="/ulab/gost/method/${item.um_id}" class="text-decoration-none" target="_blank">
                                ${item.name}
                            </a>`
                }
            },
            {
                data: 'clause'
            },
            {
                data: 'reg_doc'
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
        order: [[ 1, "desc" ]],
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
    });

    journalDataTable.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            journalDataTable
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        });
    });

    /*journal filters*/
    $('.filter-btn-search').on('click', function () {
        $('#journal_filter').addClass('is-open')
        $('.filter-btn-search').hide();
    });

    $('.filter').on('change', function () {
        journalDataTable.ajax.reload();
        journalDataTable.draw();
    });

    $('.filter-btn-reset').on('click', function () {
        location.reload();
    });

    // Добавить прослушиватель событий для открытия и закрытия сведений
    $('#methodComponentList tbody').on('click', 'td.details-control', function () {
        let tr = $(this).closest('tr');
        let row = journalDataTable.row(tr);

        if (row.child.isShown()) {
            // Эта строка уже открыта - закрываем
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            // Открыть эту строку
            createChild(row);
            tr.addClass('shown');
        }
    });

    $body.on('click', '.method-component-history', function () {
        const umcId = $(this).data('umc');
        const $form = $('#history-modal-form');

        $form.find('.title').empty();
        $form.find('.history-info').empty();

        if (umcId) {
            $.ajax({
                url: "/ulab/vlk/getMethodComponentHistoryAjax/",
                data: {"umc_id": umcId},
                dataType: "json",
                method: "POST",
                success: function (data) {
                    $form.find('.title').text(`История метрологической характеристики "${data.uc_name}"`);

                    let html = `<div class="row">
                                <div class="col-auto">${data.create_at_ru}</div>
                                <div class="col">Добавление метрологической характеристики</div>
                                <div class="col-auto">${data.short_name}</div>
                            </div>`;

                    $form.find('.history-info').html(html);

                    $.magnificPopup.open({
                        items: {
                            src: '#history-modal-form',
                            type: 'inline',
                        },
                    });
                }
            });
        }

        return false;
    });

    /**
     * Добавить образец контроля c метрологической характеристикой к методике
     */
    $body.on('click', '.add-st-component', function (e) {
        e.preventDefault();

        const methodComponentModalForm = $('#methodComponentModalForm');

        methodComponentModalForm.find('.remove-condition').remove();
        methodComponentModalForm.find('select').val('').trigger('change');

        $.magnificPopup.open({
            items: {
                src: methodComponentModalForm,
                type: 'inline',
                fixedContentPos: false
            },
            closeOnBgClick: false
        });
    });

    /**
     * Сохранить связь образеца контроля c метрологической характеристикой и методики
     */
    $body.on('submit', '#methodComponentModalForm', function () {
        let $btn = $(this);

        $btn.find('i').addClass('spinner-animation');
        $btn.addClass('disabled');
    });

    /**
     * Открепить метрологическую хар-ку от методики
     */
    $body.on('click', '.delete-component', function () {
        let umcId = $(this).data('umc');

        let $btn = $(this);

        if (!confirm('Вы действительно хотите открепить метрологическую хар-ку?')) {
            return false;
        }

        $btn.find('i').addClass('spinner-animation');
        $btn.addClass('disabled');

        $.ajax({
            method: 'POST',
            url: '/ulab/vlk/deleteMethodComponentAjax',
            data: {
                umc_id: umcId,
            },
            dataType: "json",
            success: function (data) {
                if (data['success']) {
                    location.reload();
                } else {
                    showErrorMessage(data['error']);
                    window.scrollTo(0, 0);

                    return false;
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
                    msg = '1 Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                console.log(msg)
            }
        });
    });

});

function createChild(row) {
    // row — исходный объект данных
    let rowData = row.data();
    let methodId = rowData.um_id;
    let tr = ``;

    $.ajax({
        method: 'POST',
        url: '/ulab/vlk/getComponentsByMethodAjax',
        data: {
            method_id: methodId,
        },
        dataType: 'json',
        success: function (data) {
            if (data.length) {
                $.each(data, function (i, val) {
                    tr += `<tr>
                              <td>
                                <div class="stage rounded ${val['bgStage']}" title="${val['titleStage']}"></div>
                              </td>
                              <td>
                                <a href="/ulab/oborud/sampleCard/${val['ss_id']}" class="text-decoration-none">${val['NAME']}</a>
                              </td>
                              <td>
                                ${val['NUMBER']}
                              </td>
                              <td>
                                ${val['uc_name']}
                              </td>
                              <td>
                                ${val['certified_value']}
                              </td>
                              <td>
                                <a class="no-decoration" href="/ulab/vlk/measuring/${val['umc_id']}" title="Результаты измерений">
                                    <svg class="icon" width="35" height="35">
                                        <use xlink:href="/ulab/assets/images/icons.svg#edit"></use>
                                    </svg>
                                </a>
                              </td>
                              <td>
                                <a href="#" data-umc="${val['umc_id']}" class="method-component-history"><i class="fa-regular fa-clock"></i></a>
                              </td>
                              <td>
                                <button class="btn btn-danger text-nowrap delete-component" type="button" 
                                    data-umc="${val['umc_id']}">
                                    Открепить
                                    <i class="fa-solid fa-arrows-rotate"></i>
                                </button>
                              </td>
                            </tr>`;
                });

                let table = $(
                    `<table class="table table-striped align-middle mb-0">
                       <thead>
                         <tr class="table-secondary">
                            <th scope="col"></th>
                            <th scope="col">Образец контроля</th>
                            <th scope="col">Номер</th>
                            <th scope="col">Метрологическая характеристика</th>
                            <th scope="col">Аттестованное значение</th>
                            <th scope="col">Измерения</th>
                            <th scope="col">История</th>
                            <th scope="col"></th>
                         </tr>
                       </thead>
                      <tbody>
                        ${tr}
                      </tbody>
                    </table>`
                );

                // Отобразить дочернюю строку
                row.child(table).show();
            } else {
                row.child('<div class="text-center align-middle">Отсутствуют образцы контроля</div>').show();
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
    });
}