$(function ($) {
    let $body = $('body')

    $('.select2').select2({
        theme: 'bootstrap-5',
        matcher: select2FilterSeparate,
        templateResult: select2FormatState,
        templateSelection: select2FormatState,
    })

    setTimeout(function () {
        $('.material_select').trigger('change')
    }, 1100)


    $body.on('change', '.material_select', function () {
        let id = $(this).val()
        let $tuSelect = $('.tu_select')
        let $tableBody = $('.table_gost tbody')

        if ( id == '' ) {
            $tuSelect.html(`<option value="" selected>Выберете материал</option>`)
            $tableBody.html(`<tr><td colspan="3">Не выбрано ТУ</td></tr>`)
        } else {
            $.ajax({
                url: "/ulab/material/getNewSchemeAjax/",
                data: {"id": id},
                dataType: "json",
                method: "POST",
                success: function (data) {
                    let html = `<option value="">Выбрать ТУ</option>`
                    $.each(data, function (i, item) {
                        html += `<option value="${item.id}">${item.scheme_name}</option>`
                    })
                    $tuSelect.html(html)
                }
            })
        }

        $tuSelect.select2({
            theme: 'bootstrap-5',
            matcher: select2FilterSeparate,
        })
    })


    $body.on('change', '.tu_select', function () {
        let id = $(this).val()
        let $tableBody = $('.table_gost tbody')

        if ( id == '' ) {
            $tableBody.html(`<tr><td colspan="3">Не выбрано ТУ</td></tr>`)
        } else {
            $.ajax({
                url: "/ulab/material/getNewSchemeMethodsAjax/",
                data: {"id": id},
                dataType: "json",
                method: "POST",
                success: function (data) {
                    let html = ``
                    let totalPrice = 0.0
                    $.each(data, function (i, item) {
                        totalPrice += parseFloat(item.price)

                        let gost = `${item.view_gost}`
                        if ( item.is_actual == 0 ) {
                            gost = `<div class="text-danger is-invalid">${item.view_gost}</div>
                                <div class="invalid-feedback">
                                    <i>Методика не актуальна</i>
                                </div>`
                        } else if ( item.in_field == 0 && item.is_extended_field == 0 ) {
                            gost = `<div class="text-dark-blue is-invalid">${item.view_gost}</div>
                                <div class="text-dark-blue invalid-feedback">
                                    <i>Методика не в области аккредитации</i>
                                </div>`
                        } else if ( item.is_confirm == 0 ) {
                            gost = `<div class="text-yellow is-invalid">${item.view_gost}</div>
                                <div class="text-yellow invalid-feedback">
                                    <i>Методика не проверена</i>
                                </div>`
                        }

                        html += `<tr>
                                    <td>
                                        ${gost}
                                    </td>
                                    <td>${item.price}</td>
                                    <td>
                                        <button type="button" class="cursor-pointer btn btn-danger btn-sm btn-square-sm delete-method" data-smm_id="${item.smm_id}"
                                          data-bs-container="body" data-bs-trigger="hover" data-bs-toggle="popover" data-bs-placement="right" data-bs-content="Исключить методику из ТУ">
                                            <i class="fa-solid fa-xmark"></i>
                                        </button>
                                    </td>
                                </tr>`
                    })
                    html += `<tr>
                                <td class="text-end"><strong>Итого</strong></td>
                                <td><strong>${totalPrice}</strong></td>
                                <td></td>
                            </tr>`
                    $tableBody.html(html)

                    $('[data-bs-toggle="popover"]').popover()
                }
            })
        }
    })


    $body.on('click', '.delete-method', function () {
        if ( confirm("Исключить методику из ТУ?") ) {
            let id = $(this).data('smm_id')
            let $tr = $(this).closest('tr')

            $.ajax({
                url: "/ulab/material/deleteNewSchemeMethodsAjax/",
                data: {"id": id},
                // dataType: "json",
                method: "POST",
                success: function (data) {
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
                    console.log(msg)
                }
            })

            $tr.remove()
        }
    })
})