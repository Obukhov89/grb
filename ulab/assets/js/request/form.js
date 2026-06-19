$(function ($) {
    let materialList = ''

    $.ajax({
        method: 'POST',
        url: '/ulab/request/getMaterialsAjax',
        dataType: 'json',
        success: function(data) {
            materialList = data
        }
    })

    $('.popup-with-form').magnificPopup({
        type: 'inline',
        closeBtnInside:true,
        fixedContentPos: false
    })

    let $body = $("body")

    $('.select2').select2({
        theme: 'bootstrap-5'
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
        let $formGroupContainer = $(this).parents('.material-block').find('.form-group:last-child')
        countAddedMaterial++

        let optionsMaterial = ''
        $.each(materialList, function (i, item) {
            optionsMaterial += `<option value="${item.id}">${item.name}</option>`
        })

        $formGroupContainer.after(
            `<div class="form-group row added_material">
                <label class="col-sm-2 col-form-label"></label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <select name="material[${countAddedMaterial}][material_id]" id="material${countAddedMaterial}" class="form-control select2">
                            <option value="">Выбрать материал</option>
                            ${optionsMaterial}
                        </select>
                        <span class="input-group-text">Кол-во:</span>
                        <input type="number" name="material[${countAddedMaterial}][count]" class="form-control material-count" min="1" step="1" required value="1">
                        <span class="input-group-text">Схема:</span>
                        <select name="material[${countAddedMaterial}][scheme_id]" id="scheme${countAddedMaterial}" class="form-control select2">
                            <option value="1">Нет схемы / ручной ввод</option>
                        </select>
                        <button class="btn btn-outline-secondary btn-square" type="button" title="Показать схему">
                            <i class="fa-solid fa-table-list icon-fix-2"></i>
                        </button>
                    </div>
                </div>
                <div class="col-sm-2">
                    <button class="btn btn-danger remove_this btn-add-del" type="button"><i class="fa-solid fa-minus icon-fix"></i></button>
                </div>
            </div>`
        )

        $body.find('.added_material:last-child .select2').select2({
            theme: 'bootstrap-5'
        })
    })


    $body.on("click", ".form-group button.remove_this", function () {
        let $formGroupContainer = $(this).parents('.form-group')

        $formGroupContainer.remove()
    })

    let companyId = $('input[name="company_id"]').val()
    if ( companyId !== '' ) {
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

    $('#company').on('change', function () {
        // clear form
        let $selectContract = $('select[name="NUM_DOGOVOR"]')
        $selectContract.empty().append(`<option value=""></option>`)
        $('input.clearable').val('')

        let $editCompanyBtn = $('.edit-company')

        let companyId = $(this).val()
        if ( companyId !== '' ) {
            $editCompanyBtn
                .attr('href', `/crm/company/details/${companyId}/`)
                .show()

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
        } else {
            $editCompanyBtn
                .attr('href', `#`)
                .hide()
        }
    })

    $('input[name="RQ_INN"]').on('input', function () {
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
})