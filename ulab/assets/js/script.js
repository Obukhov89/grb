const dataTablesSettings = {
    'buttons':[
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
}


/**
 *
 * @param state
 * @returns {void|define.amd.jQuery|HTMLElement|*}
 */
function select2FormatState(state) {
    const option = $(state.element)
    const color = option.data("color")

    if (!color) {
        return state.text
    }

    return $(`<span style="color: ${color}">${state.text}</span>`)
}


/**
 * фильтр для select2 - нестрогий поиск
 * @param params
 * @param data
 * @returns {null|*}
 */
function select2FilterSeparate(params, data) {
    if ($.trim(params.term) === '') {
        return data
    }

    let terms = (params.term).split(" ")

    for (var i = 0; i < terms.length; i++) {
        if (((data.text).toUpperCase()).indexOf((terms[i]).toUpperCase()) == -1)
            return null
    }
    return data
}


$(function ($) {

    $('.popup-help').magnificPopup({
        type: 'iframe',
        mainClass: 'white-popup'
    })

    $('.popup-with-form').magnificPopup({
        type: 'inline',
        closeBtnInside: true,
        fixedContentPos: false,
        closeOnBgClick: false,
        focus: '#focus-blur-loop-select',
        midClick: true
    })



    // panel collapsible
    // $('.panel .panel-heading').click(function () {
    //     let el = $(this).parents(".panel").children(".panel-body")
    //     if ($(this).find('a').hasClass("fa-chevron-up")) {
    //         $(this).find('a').removeClass("fa-chevron-up").addClass("fa-chevron-down")
    //         el.slideUp(500)
    //     } else {
    //         $(this).find('a').removeClass("fa-chevron-down").addClass("fa-chevron-up")
    //         el.slideDown(500)
    //     }
    //
    //     return false
    // })

    let $body = $("body")
    $body.on("change keyup input click", "input.number-only", function () {
        if (this.value.match(/[^0-9]/g)) {
            this.value = this.value.replace(/[^0-9]/g, '')
        }
    })

    $body.on('click', '.panel .panel-heading', function () {
        let el = $(this).parents(".panel").children(".panel-body")
        if ($(this).find('a').hasClass("fa-chevron-up")) {
            $(this).find('a').removeClass("fa-chevron-up").addClass("fa-chevron-down")
            el.slideUp(500)
        } else {
            $(this).find('a').removeClass("fa-chevron-down").addClass("fa-chevron-up")
            el.slideDown(500)
        }

        return false
    })


    // //счет оферта
    // $('input[name="order_type"]').change(function () {
    //     let check = $(this).val(),
    //         order = $body.find('.order-type'),
    //         select = order.find('select[name="NUM_DOGOVOR"]')
    //
    //     if (check == 2) {
    //         order.addClass('visually-hidden')
    //         select.find('option').prop('selected', false)
    //     } else {
    //         order.removeClass('visually-hidden')
    //     }
    // });

    // копирует в буффер из дата элемента
    $body.on('click', 'a[data-text-clipboard]', function () {
        const $parent = $(this).parent()
        const text = $(this).data('text-clipboard')
        const $temp = $("<div>")

        $parent.append($temp)

        $temp.attr("contenteditable", true)
            .html(text).select()
            .on("focus", function() { document.execCommand('selectAll',false,null); })
            .focus();
        document.execCommand("copy");
        $temp.remove();

        $(this).addClass('clipboard-tooltip')
        $(this).append(`<span class="tooltiptext rounded">Скопировано</span>`)

        return false
    })

    $body.on('mouseover', 'a[data-text-clipboard]', function () {
        $(this).removeClass('clipboard-tooltip')
        $(this).find('.tooltiptext').remove()
    })
    // $body.on('submit', 'form', function () {
    //     $(this).find('input[type=submit], button[type=submit]').addClass('disabled')
    // })
    $body.on('click', '.disable-after-click', function () {
        $(this).addClass('disabled')
    })

    $body.on("click", "button.add_email", function () {
        let $formGroupContainer = $(this).parents('.form-group')
        let countAddedEmail = $('.added_mail').length + 1

        if (countAddedEmail > 1) {
            $formGroupContainer = $('.form-horizontal .added_mail').last()
        }

        $formGroupContainer.after(
            `<div class="form-group row added_mail">
                <label class="col-sm-2 col-form-label">Дополнительный E-mail ${countAddedEmail}</label>
                <div class="col-sm-8">
                    <input type="email" name="addEmail[]" class="form-control" placeholder="_@_._">
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-danger remove_this btn-add-del" type="button"><i class="fa-solid fa-minus icon-fix"></i></button>
                </div>
            </div>`
        )
    })

    let countAddedMaterial = $('.added_material').length + 1
    $body.on("click", "button.add_material", function () {
        let $formGroupContainer = $(this).parents('.form-group')
        countAddedMaterial++

        $formGroupContainer.after(
            `<div class="form-group row">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input id="material${countAddedMaterial}" type="text" list="materials" name="material[${countAddedMaterial}][name]" class="form-control" required>
                        <span class="input-group-text">Кол-во:</span>
                        <input type="number" name="material[${countAddedMaterial}][count]" class="form-control material-count" min="1" step="1" required value="1">
                    </div>
                    <input type="hidden" name="material[${countAddedMaterial}][id]" class="material_id" id="material${countAddedMaterial}-hidden">
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-danger remove_this btn-add-del" type="button"><i class="fa-solid fa-minus icon-fix"></i></button>
                </div>
            </div>`
        )
    })

    $body.on("click", "button.add_assigned", function () {
        let $formGroupContainer = $(this).parents('.form-group')
        let countAddedAssigned = $('.added_assigned').length + 1

        $formGroupContainer.after(
            `<div class="form-group row">
                <label class="col-sm-2 col-form-label">Ответственный</label>
                <div class="col-sm-8">
                    <input id="assigned${countAddedAssigned}" class="form-control" type="text" list="assigneds" name="ASSIGNED[]" size="20" required>
                    <input id="assigned${countAddedAssigned}-hidden" name="id_assign[]" type="hidden" value="">
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-danger remove_this btn-add-del" type="button"><i class="fa-solid fa-minus icon-fix"></i></button>
                </div>
            </div>`
        )
    })

    $body.on("click", ".form-group button.remove_this", function () {
        let $formGroupContainer = $(this).parents('.form-group')

        $formGroupContainer.remove()
    })

    $body.on('input', 'input[list]', function(e) {
        let $input = $(e.target),
            $options = $('#' + $input.attr('list') + ' option'),
            $hiddenInput = $('#' + $input.attr('id') + '-hidden'),
            label = $input.val().trim()

        for (let i = 0; i < $options.length; i++) {
            let $option = $options.eq(i)

            if ($option.text().trim() === label) {
                $hiddenInput.val( $option.attr('data-value') )
                break
            } else {
                $hiddenInput.val('')
            }
        }
    })

    let companyId = $('input[name="company_id"]').val()
    if ( companyId !== '' ) {
        console.log($(`#company_list option[data-value="${companyId}"]`).text())
        $('#company').val($(`#company_list option[data-value="${companyId}"]`).text())
    }

    let $inputMaterial = $('input.material_id')
    $inputMaterial.each(function (i, item) {
        let $item = $(item)
        let materialId = $item.val()

        if (materialId !== '') {
            let reg = /\d+/
            let inputId = $item.attr('id').match(reg)

            if (inputId !== null) {
                $(`#material${inputId[0]}`).val($(`#materials option[data-value="${materialId}"]`).text())
            }
        }
    })

    let $inputAssigned = $('input[name^="id_assign"]')
    $inputAssigned.each(function (i, item) {
        let $item = $(item)
        let assignedId = $item.val()

        if (assignedId !== '') {
            let reg = /\d+/
            let inputId = $item.attr('id').match(reg)

            if (inputId !== null) {
                $(`#assigned${inputId[0]}`).val($(`#assigneds option[data-value="${assignedId}"]`).text())
            }
        }
    })

    let orderType = 1

    $body.on('click', 'input[name=order_type]', function () {
        orderType = $(this).val()
    })

    $('#company').on('change', function () {
        // clear form
        let $selectContract = $('select[name="NUM_DOGOVOR"]')
        $selectContract.empty().append(`<option value=""></option>`)
        $('input.clearable').val('')

        let text = $(this).val()
        if ( text !== '' ) {
            // let companyId = $(`#company_list option:contains(${text})`).data('value')
            let companyId = $(`#company-hidden`).val()

            if ( companyId !== undefined ) {
                $.ajax({
                    url: "/ulab/request/getRequisiteAjax/",
                    data: {"company_id": companyId},
                    dataType: "json",
                    method: "POST",
                    success: function (data) {
                        $('input[name="CompanyFullName"]').val(data.RQ_COMPANY_FULL_NAME)
                        $('input[name="INN"]').val(data.RQ_INN)
                        $('input[name="OGRNIP"]').val(data.RQ_OGRNIP)
                        $('input[name="OGRN"]').val(data.RQ_OGRN)
                        $('input[name="ADDR"]').val(data.RQ_ACCOUNTANT)
                        $('input[name="ACTUAL_ADDRESS"]').val(data.address[1].ADDRESS_1)
                        $('input[name="mailingAddress"]').val(data.RQ_COMPANY_NAME)
                        $('input[name="EMAIL"]').val(data.RQ_FIRST_NAME)
                        $('input[name="POST_MAIL"]').val(data.RQ_EMAIL)
                        $('input[name="PHONE"]').val(data.RQ_PHONE)
                        $('input[name="CONTACT"]').val(data.RQ_NAME)
                        $('input[name="KPP"]').val(data.RQ_KPP)
                        $('input[name="Position2"]').val(data.RQ_COMPANY_REG_DATE)
                        $('input[name="PositionGenitive"]').val('')
                        $('input[name="DirectorFIO"]').val(data.RQ_DIRECTOR)
                        $('input[name="RaschSchet"]').val(data.RQ_ACC_NUM)
                        $('input[name="KSchet"]').val(data.RQ_COR_ACC_NUM)
                        $('input[name="l_schet"]').val(data.COMMENTS)
                        $('input[name="BIK"]').val(data.RQ_BIK)
                        $('input[name="BankName"]').val(data.RQ_BANK_NAME)
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

                console.log(orderType)
                console.log(companyId)

                if(orderType === 1) {
                    $.ajax({
                        url: "/ulab/request/getContractsAjax/",
                        data: {"company_id": companyId},
                        dataType: "json",
                        method: "POST",
                        success: function (data) {
                            for (const i in data) {
                                if (data.hasOwnProperty(i)) {
                                    $selectContract.append(
                                        `<option value="${data[i].ID}">${data[i].CONTRACT_TYPE ?? 'Договор'} №${data[i].NUMBER} от ${data[i].DATE}${data[i].endless_text}</option>`
                                    )
                                }
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
                            console.log(msg)
                        }
                    })
                }else if(orderType === 2) {
                    $.ajax({
                        url: "/ulab/request/getContractsOfferAjax/",
                        data: {"company_id": companyId},
                        dataType: "json",
                        method: "POST",
                        success: function (data) {
                            for (const i in data) {
                                if (data.hasOwnProperty(i)) {
                                    $selectContract.append(
                                        `<option value="${data[i].ID}">${data[i].CONTRACT_TYPE ?? 'Счет-оферта'} №${data[i].NUMBER} от ${data[i].DATE}</option>`
                                    )
                                }
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
                            console.log(msg)
                        }
                    })
                }
            }
        }
    })

    $('input[name="INN"]').on('input', function () {
        let inn = $(this).val()
        let $innHelp = $('#innHelp')

        let length = inn.length

        if ( length === 9 || length === 10 || length === 12 ) {
            $innHelp.html(
                `Идет поиск по ИНН. Подождите...
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>`
            ).removeClass('text-green').removeClass('text-red')

            $.ajax({
                url: "/ulab/request/checkCompanyByInnAjax/",
                data: {"INN": inn},
                dataType: "json",
                method: "POST",
                success: function (data) {
                    if ( data !== false ) {
                        $innHelp.text('Найдено в системе.').addClass('text-green')
                        let text = $(`#company_list option[data-value=${data}]`).text()
                        if ( confirm(`Компания с таким ИНН уже существует. Название: ${text}. Применить данные этой компании?`) ) {
                            $('#company-hidden').val(data)
                            $('#company').val(text).trigger('change')
                        }
                    } else {
                        $.ajax({
                            url: "/ulab/request/getCompanyByInnAjax/",
                            data: {"INN": inn},
                            dataType: "json",
                            method: "POST",
                            success: function (data) {
                                if (data && data.name_short !== undefined && data.name_short !== null) {
                                    $innHelp.text('Найдено в сети Интернет.').addClass('text-green')
                                    if ( confirm(`Найдена компания с таким ИНН. Название: ${data.name_short}. Применить данные этой компании?`) ) {
                                        $('#company').val(data.name_short)
                                        $('input[name="CompanyFullName"]').val(data.name)
                                        $('input[name="KPP"]').val(data.kpp)
                                        $('input[name="ADDR"]').val(data.adress)
                                        $('input[name="Position2"]').val(data.position_name)
                                        $('input[name="DirectorFIO"]').val(data.official_name)
                                        $('input[name="OGRN"]').val(data.ogrn)
                                    }
                                } else {
                                    $innHelp.text('Компаний с таким ИНН не найдено').addClass('text-red')
                                }
                            }
                        })
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
                    console.log(msg)
                }
            })
        } else {
            $innHelp.html(
                `Необходимо 9, 10 или 12 цифр. Введено: ${length}`
            ).removeClass('text-green').removeClass('text-red')
        }
    })

    $('.check-ip').change(function () {
        let disabled = $( ".check-ip:checked" ).length === 0
        $('#ogrnip').prop( "disabled", disabled )
    })

    $('.reload').on('click', function () {
        let url = $(this).data('href')
        window.open (url, '_blank')
        setTimeout( function() {location.reload()});
    })
})


/**
 * Округление
 * @param num
 * @param decimalPlaces
 * @returns {number}
 */
function round(num, decimalPlaces = 0) {
    if (num < 0) {
        return -round(-num, decimalPlaces);
    }
    let p = Math.pow(10, decimalPlaces);
    let n = num * p;
    let f = n - Math.floor(n);
    let e = Number.EPSILON * n;

    return (f >= 0.5 - e) ? Math.ceil(n) / p : Math.floor(n) / p;
}

/**
 * Нахождение среднего арифметического
 * @param nums
 * @returns {number|boolean}
 */
function average(nums) {

    if ( nums.length === 0 || nums.length === undefined ) {
        return false
    }
    return nums.reduce((a, b) => (a + b)) / nums.length
}

/**
 * Нахождение количество дней между 2-мя датами
 * @param start
 * @param end
 * @returns {number}
 */
function getNumberOfDays(start, end) {
    const date1 = new Date(start);
    const date2 = new Date(end);

    // Один день в миллисекундах
    const oneDay = 1000 * 60 * 60 * 24;

    // Расчет разницы во времени между двумя датами
    const diffInTime = date2.getTime() - date1.getTime();

    const diffInDays = Math.round(diffInTime / oneDay);

    return diffInDays;
}

function showErrorMessage(msg) {
    $('.messages').append(
        `<div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
            </svg>
            <div>
                ${msg}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`
    )
}

function showSuccessMessage(msg) {
    $('.messages').append(
        `<div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
            </svg>
            <div>
                ${msg}
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>`
    )

    window.setTimeout(function () {
        $(".alert").alert('close');
    }, 2000)
}

function bufferToBase64(buf) {
    let binstr = Array.prototype.map.call(buf, function (ch) {
        return String.fromCharCode(ch);
    }).join('');
    return btoa(binstr);
}


/**
 *
 * @param filePath
 * @param imgUrl
 * @param imgParams
 * @param newFilePath
 * @param text
 * @param textParams - json {x: 50, y: 50, size: 24, }
 * @returns {Promise<string>}
 */
async function addImgToPdf(filePath, imgUrl, imgParams, newFilePath = '', text = '', textParams = null) {
    const { PDFDocument, StandardFonts, grayscale, rgb, degrees } = PDFLib;
    const existingPdfBytes = await fetch(filePath).then(res => res.arrayBuffer());

    const pdfDoc = await PDFDocument.load(existingPdfBytes);
    const pages = pdfDoc.getPages();
    const firstPage = pages[0];

    const pngImageBytes = await fetch(imgUrl).then((res) => res.arrayBuffer());
    const pngImage = await pdfDoc.embedPng(pngImageBytes);

    firstPage.drawImage(pngImage, imgParams);

    if ( text !== '' && textParams !== null ) {
        textParams.font = await pdfDoc.embedFont(StandardFonts.TimesRoman)
        textParams.color = rgb(0, 0, 0)

        firstPage.drawText(text, textParams)
    }

    const pdfBytes = await pdfDoc.save();

    let base64 = bufferToBase64(pdfBytes);

    if (newFilePath != '') {
        filePath = newFilePath;
    }

    $.ajax({
     //   url: '/ulab/file/bytePdfToServerAjax',
        url: '/ulab/secondment/bytePdfToServerAjax',
        data: {
            file: base64, path: filePath
        },
        async: false,
        method: 'POST',
        success: function (json) {
            // console.log(json)
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

    return base64
}

/**
 *
 * @param filePath
 * @param text
 * @param textParams - json {x: 50, y: 50, size: 24, }
 * @returns {Promise<void>}
 */
async function addTextToPdf(filePath, text, textParams) {
    const { PDFDocument, StandardFonts, grayscale, rgb, degrees } = PDFLib;
    const existingPdfBytes = await fetch(filePath).then(res => res.arrayBuffer());

    const pdfDoc = await PDFDocument.load(existingPdfBytes);

    const timesRomanFont = await pdfDoc.embedFont(StandardFonts.TimesRoman)
    const pages = pdfDoc.getPages();
    const firstPage = pages[0];

    textParams.font = timesRomanFont
    textParams.color = rgb(0, 0, 0)

    firstPage.drawText(text, textParams)

    const pdfBytes = await pdfDoc.save();

    let base64 = bufferToBase64(pdfBytes);

    $.ajax({
        url: '/ulab/file/bytePdfToServerAjax',
        data: {
            file: base64, path: filePath
        },
        async: false,
        method: 'POST',
        success: function (json) {
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
}


/**
 *
 * @param filePath
 * @param textAndParams - array json [{text: '', params: {x: 50, y: 50, size: 12, color: rgb(0, 0, 0)} }]
 * @returns {Promise<void>}
 */
async function addTextListToPdf(filePath, textAndParams) {
    const { PDFDocument, StandardFonts, grayscale, rgb, degrees } = PDFLib;
    const existingPdfBytes = await fetch(filePath).then(res => res.arrayBuffer());

    const pdfDoc = await PDFDocument.load(existingPdfBytes);

    const timesRomanFont = await pdfDoc.embedFont(StandardFonts.TimesRoman)
    const pages = pdfDoc.getPages();
    const firstPage = pages[0];

    $.each(textAndParams, function (i, item) {
        item.params.font = timesRomanFont

        firstPage.drawText(item.text, item.params)
    })

    const pdfBytes = await pdfDoc.save();

    let base64 = bufferToBase64(pdfBytes);

    $.ajax({
        url: '/ulab/file/bytePdfToServerAjax',
        data: {
            file: base64, path: filePath
        },
        method: 'POST',
        success: function (json) {
            console.log(json)
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
}



