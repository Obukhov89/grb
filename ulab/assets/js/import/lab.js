$(function ($) {
    let body = $('body')

    let $journal = $('#journal_labs')

    /*journal labs*/
    let activeAjaxRequests = 0;
    let journalDataTable = $journal.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            url : '/ulab/import/getLabJournalAjax/',
            dataSrc: function (json) {
                return json.data
            },
            beforeSend: () => {
                activeAjaxRequests++;
                if (activeAjaxRequests > 0)
                    $('#ajax-loading-message').show()
            },
            complete: () => {
                activeAjaxRequests--;

                if (activeAjaxRequests <= 0)
                    $('#ajax-loading-message').hide()
            }
        },
        columns: [
            {
                data: 'NAME',
                orderable: true,
            },
            {
                data: 'FULL_NAME',
                width: '300px',
                orderable: false
            },
            {
                data: 'ID',
                orderable: false,
                render: function (data, type, item) {
                    return `<button type="button" class="btn btn-fill btn-square lab-edit" title="${item['NAME']}" 
                                data-bs-placement="left" data-bs-toggle="tooltip" data-bs-trigger="hover"
                                data-dept-id="${data}">
                            <i class="fa-solid fa-pencil icon-fix"></i>
                        </button>`;
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
        order: [[ 0, "asc" ]],
        dom: 'frtB<"bottom"lip>',
        bSortCellsTop: true,
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
    });

    journalDataTable.on('draw.dt', function () {
        const tooltipTriggerList = document.querySelectorAll('.lab-edit[data-bs-toggle="tooltip"]');
        tooltipTriggerList.forEach(tooltipTriggerEl => {
            const tooltip = new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });

    let searchTimeouts = {};
    journalDataTable.columns().every(function () {
        let columnIndex = this.index();

        $(this.header()).closest('thead').find('.search:eq(' + columnIndex + ')').on('keyup change clear', function () {
            clearTimeout(searchTimeouts[columnIndex]);

            let inputElement = this;
            searchTimeouts[columnIndex] = setTimeout(function () {
                journalDataTable
                    .column($(inputElement).parent().index())
                    .search(inputElement.value)
                    .draw();
            }, 50);
        });
    });

    /*journal filters*/
    $('.filter-btn-search').on('click', function () {
        $('#journal_filter').addClass('is-open')
        $('.filter-btn-search').hide()
    })

    $(document).on('click', function(event) {
        if (!$(event.target).closest('#filter_search').length && $('#filter_everywhere').val() === '') {
            $('#journal_filter').removeClass('is-open');
            $('.filter-btn-search').show();
        }
    });

    $('.filter').on('change', function () {
        journalDataTable.ajax.reload()
        journalDataTable.draw()
    })

    $('.filter-btn-reset').on('click', function () {
        location.reload()
    });
})