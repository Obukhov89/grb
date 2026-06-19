$(function () {
    let $body = $('body'),
        $journal = $('#sampleList');

    let journalDataTable = $journal.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.stage = $('#selectStage option:selected').val()
                d.lab = $('#selectLab option:selected').val()
            },
            url : '/ulab/oborud/getSampleListAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'STAGE',
                orderable: false,
                render: function (data, type, item) {
                    return `<div class="stage rounded ${item['bgStage']}" title="${item['titleStage']}"></div>`
                }
            },
            {
                data: 'NAME',
                render: function (data, type, item) {
                    return `<a href="/ulab/oborud/sampleCard/${item.ID}" class="text-decoration-none">${item.NAME}</a>`
                }
            },
            {
                data: 'NUMBER',
            },
            {
                data: 'MANUFACTURE_DATE',
            },
            {
                data: 'EXPIRY_DATE',
            },
            {
                data: 'COMPONENTS',
                orderable: false,
                render: function (data, type, item) {
                    let $components = ``;
                    for (let component of item['components']) {
                        $components += `<div>${component['name']} ${component['certified_value']}</div>`
                    }
                    return $components;
                }
            },
            {
                data: 'HISTORY',
                orderable: false,
                render: function (data, type, item) {
                    return `<a href="#" data-id="${item.ID}" class="control-samples-history"><i class="fa-regular fa-clock"></i></a>`;
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
        })
    });

    /*journal filters*/
    $('.filter-btn-search').on('click', function () {
        $('#journal_filter').addClass('is-open');
        $('.filter-btn-search').hide();
    });

    $('.filter').on('change', function () {
        journalDataTable.ajax.reload();
        journalDataTable.draw();
    });

    $('.filter-btn-reset').on('click', function () {
        location.reload();
    });

    $body.on('click', '.control-samples-history', function () {
        const id = $(this).data('id');
        const $form = $('#history-modal-form');
console.log('click');
        $form.find('.title').empty();
        $form.find('.history-info').empty();

        $.ajax({
            url: "/ulab/oborud/getSampleHistoryAjax/",
            data: {"id": id},
            dataType: "json",
            method: "POST",
            success: function (data) {
                console.log('data', data);
                $form.find('.title').text(`История образца контроля ${data.info.NUMBER}`);

                let html = ``;

                $.each(data.history, function (i, item) {
                    html +=
                        `<div class="row">
                            <div class="col-auto">${item.date}</div>
                            <div class="col">${item.action}</div>
                            <div class="col-auto">${item.short_name}</div>
                        </div>`;
                })

                if ( html === '' ) {
                    html = `История отсутствует`;
                }

                $form.find('.history-info').html(html);

                $.magnificPopup.open({
                    items: {
                        src: '#history-modal-form',
                        type: 'inline',
                    },
                });
            }
        });

        return false;
    });

});