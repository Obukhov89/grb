$(function ($) {
    let $body = $("body")


    $('.select2').select2({
        theme: 'bootstrap-5',
        width: 'resolve',
    })

    let $journal = $('#journal_xml')

    if ( $journal.length > 0 ) {
        /*journal requests*/
        let journalDataTable = $journal.DataTable({
            bAutoWidth: false,
            autoWidth: false,
            fixedColumns: false,
            processing: true,
            serverSide: true,
            ajax: {
                type: 'POST',
                data: function ( d ) {
                    d.protocol_id = $('#protocol_id').val()
                },
                url: '/ulab/fsa/getListXmlAjax/',
                dataSrc: function (json) {
                    return json.data
                },
            },
            columns: [
                {
                    data: 'date',
                    width: '100px'
                },
                {
                    data: 'xml_file',
                    orderable: false,
                    render: function (data, type, item) {
                        return `<a class="results-link"
                           href="/ulab/upload/fsa/protocols/${item['file_xml']}" download>
                           ${item['file_xml']}
                        </a>`
                    }
                },
                {
                    data: 'sig_file',
                    orderable: false,
                    render: function (data, type, item) {
                        if ( item['file_sig'] !== '' ) {
                            return `<a class="results-link"
                               href="/ulab/upload/fsa/protocols/${item['file_sig']}" download>
                               ${item['file_sig']}
                            </a>`
                        } else {
                            return `
                                <label 
                                    title="Если файл подписи уже есть, его можно загрузить" 
                                    class="btn btn-link"
                                >
                                    Загрузить файл подписи
                                    <input type="file" data-id="${item.id}" class="d-none upload_sig">
                                </label>
                            `
                        }
                    }
                },
                {
                    data: 'button',
                    width: '120px',
                    orderable: false,
                    render: function (data, type, item) {
                        if ( item['file_sig'] === '' ) {
                            return `<a class="btn btn-primary" href="/ulab/fsa/sigProtocol/${item.id}" title="Перейти на страницу подписи">Подписать</a>`
                        } else {
                            return `<a class="btn btn-success" href="/ulab/fsa/sendProtocol/${item.id}">Отправить</a>`
                        }
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
            order: [[0, "desc"]],
            colReorder: true,
            dom: 'fBrt<"bottom"lip>',
            buttons: [],
            bSortCellsTop: true,
            scrollX: true,
            fixedHeader: false,
        });

        journalDataTable.columns().every(function () {
            $(this.header()).closest('thead').find('.search:eq(' + this.index() + ')').on('keyup change clear', function () {
                journalDataTable
                    .column($(this).parent().index())
                    .search(this.value)
                    .draw();
            })
        })

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
    }


    $body.on('change', '.upload_sig', function () {
        let protocolId = $(this).data('id')
        let file = $(this)[0].files[0]

        let formData = new FormData()

        formData.append("protocol_id", protocolId)

        formData.append("file", file, file.name)
        formData.append("upload_file", true)


        $.ajax({
            url: '/ulab/fsa/uploadSigProtocolAjax/',
            data: formData,
            method: 'POST',
            contentType: false,
            processData: false,
            cache: false,
            async: false,
            dataType: "json",
            success: function (data) {
                console.log(data)
                if ( data.success ) {
                    showSuccessMessage(`Файл загружен. Перезагрузите страницу`)

                    journalDataTable.ajax.reload()
                    journalDataTable.draw()
                    location.reload()
                } else {
                    showErrorMessage('Ошибка: ' + data.error)
                }
            },
            error: function () {
                console.log('error')
            }
        })
    })
})
