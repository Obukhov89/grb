$(function ($) {
    $.ajax({
        url: '/ulab/grain/getGostAjax/',
        data: {},
        method: 'POST',
        dataType: 'json',
        success: function (data) {
            $.each(data.gosts, function (i, v) {
                $('#gost').append(
                    `
                        <option value="${v['ID']}" data-price="${v['PRICE']}" data-type="${v['NORM_TEXT']}">${v['GOST']} ${v['GOST_PUNKT']} | ${v['SPECIFICATION']}</option>
                    `
                )
            })

            $.each(data.groups, function (i, v) {
                $('#group').append(
                    `
                        <option value="${v['ID']}">${v['NAME']}</option>
                    `
                )

                $.each(v['current_group'], function (index, value) {
                    if(!value) value = '--'

                    $('#group').append(
                        `
                        <option value="${v['ID']}-${index}">${value}</option>
                        `
                    )
                })
            })

            $('#gost').select2({
                theme: 'bootstrap-5',
                width: 'resolve',
            })

            $('#group').select2({
                theme: 'bootstrap-5',
                width: 'resolve',
            })

            $('#add-zern').on('click', function () {
                $.magnificPopup.open({
                    items: {
                        src: '#new_zern',
                        type: 'inline',
                    },
                    fixedContentPos: false,
                    closeOnBgClick: false
                })

                $('#client-select').select2({
                    theme: 'bootstrap-5',
                    width: 'resolve',
                })
            })
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

    let journalGrain = $('#journal_grain').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            url : '/ulab/grain/getListProcessingAjax/',
            dataSrc: function (json) {
                return json.data;
            },
        },
        columns: [
            {
                data: 'material_name',
                render: function (data, type, item) {
                    return `<a class="results-link" href="/ulab/grain/card/${item.ID}" target="_blank">${item.NAME}</a>`
                }
            }
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
        lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
        pageLength: 25,
        dom: 'fBrt<"bottom"lip>',
        colReorder: true,
        bSortCellsTop: true,
        order: [[0, "asc"]]
    })

    journalGrain.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            journalGrain
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
        })
    })
    
    $('.filter-btn-search').on('click', function () {
        $('#journal_requests_filter').addClass('is-open')
        $('.filter-btn-search').hide()
    })

    $('.filter').on('change', function () {
        journalGrain.ajax.reload()
        journalGrain.draw()
    })
})