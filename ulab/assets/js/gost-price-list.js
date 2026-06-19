
$(function () {
    let $journal = $('#journal_gost')

    /*journal requests*/
    let journalDataTable = $journal.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            type : 'POST',
            data: function ( d ) {
                d.stage = $('#selectStage option:selected').val()
                d.lab = $('#selectLab option:selected').val()
                d.everywhere = $('#filter_everywhere').val()
            },
            url : '/ulab/gost/getJournalAjax/',
            dataSrc: function (json) {
                return json.data
            }
        },
        columns: [
            {
                data: 'num_oa'
            },
            {
                data: 'reg_doc',
                render: function (data, type, item) {
                    return `<a href="/ulab/gost/edit/${item.gost_id}">${item.reg_doc}</a>`
                }
            },
            {
                data: 'clause'
            },
            {
                data: 'year',
            },
            {
                data: 'name',
                render: function (data, type, item) {
                    if ( item.method_id === null ) {
                        return `Методик не добавлено`
                    }
                    return `<a href="/ulab/gost/method/${item.method_id}">${item.name}</a>`
                }
            },
            {
                data: 'test_method',
                render: $.fn.dataTable.render.ellipsis(32, true)
            },
            {
                data: 'price'
            },
            {
                data: 'input',
                orderable: false,
                render: function (data, type, item) {
                    return `<input type="number" min="0" step="0.01" class="form-control bg-white new_price" value="">`
                }
            },
            {
                data: 'button',
                orderable: false,
                render: function (data, type, item) {
                    return `<button class="btn btn-primary save_price" type="button" data-method_id="${item.method_id}">Сохранить</button>`
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
        order: [[ 0, "desc" ]],
        colReorder: true,
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
        fixedHeader:   true,
    });

    journalDataTable.columns().every( function () {
        $(this.header()).closest('thead').find('.search:eq('+ this.index() +')').on( 'keyup change clear', function () {
            journalDataTable
                .column( $(this).parent().index() )
                .search( this.value )
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


    $('body').on('click', '.save_price', function () {
        let $container = $(this).closest('tr')
        let methodId = $(this).data('method_id')
        let newPrice = $container.find('.new_price').val()

        if ( newPrice != '' && newPrice !== undefined ) {
            $.ajax({
                method: 'POST',
                url: '/ulab/gost/setNewPriceAjax/',
                data: {
                    method_id: methodId,
                    new_price: newPrice
                },
                dataType: "json",
                success: function (data) {
                    if ( data.success ) {
                        showSuccessMessage('Цена успешно обновилась')
                        journalDataTable.ajax.reload()
                        journalDataTable.draw()
                    } else {
                        showErrorMessage(data.error)
                    }
                }
            })
        } else {
            showErrorMessage("Новая цена не может быть пустой")
        }
    })

    $('.get-price').on('click', function () {
        let stage = $('#selectStage option:selected').val(),
            lab = $('#selectLab option:selected').val(),
            url = '/ulab/generator/getPrice',
            form = $(`<form action="${url}" method="post">
                <input type="text" name="lab" value="${lab}" />
                <input type="text" name="stage" value="${stage}" />
                </form>`);

        $('body').append(form);
        form.submit();

    })
    $('.set-price').on('click', function () {

        $.magnificPopup.open({
            items: {
                src: '#setPriceForm'
            },
            type: 'inline',
            closeBtnInside: true,
            fixedContentPos: false,
            closeOnBgClick: false,
        })

    })

    $('#requiredCheckBox').on('change', function (){
        let file = $('#setPriceForm').find('#formFile').val(),
            subButton = $('#setPriceForm').find('.submitButton')

        if ($(this).prop('checked')) {
            if (file == '') {
                alert('Проверьте загруженный файл')
                $(this).prop('checked', false)
            } else {
                subButton.removeAttr('disabled')
            }
        }

    })

    $('.submitButton').on('click', function (){
        var formData = new FormData();
        formData.append('importXLSX', $('#formFile')[0].files[0]);

        $.ajax({
            url : '/ulab/generator/setPrice',
            type : 'POST',
            dataType: "json",
            data : formData,
            processData: false,  // tell jQuery not to process the data
            contentType: false,  // tell jQuery not to set contentType
            success : function(data) {

                console.log(data, data.count)
                let alert = `<div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                    <div>
                        Изменено ${data['count']} из ${data['countAll']}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`

                $('.requiredCheckBox').after(alert)

                $('#setPriceForm').find('.submitButton').prop('disabled', true)
                $('#requiredCheckBox').prop('disabled', true)
            }
        });
    })
})
