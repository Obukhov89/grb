$(function ($) {
    let body = $('body'),
        hiddenElements = $('input,textarea,select').filter('[required]'),
        usersByLab = []

    const methodsId = ['2', '5961', '976', '260', '604', '673', '598', '3277', '14', '1246', '3379', '3380', '1252',
        '3666', '3667', '652', '1254', '25', '1231', '1114', '1118', '172', '1130', '1132', '3058', '1092', '2997',
        '1098', '1106', '2655', '66', '1075', '105', '106', '107', '955', '957', '882', '884', '885', '886', '887',
        '890', '558', '562', '310', '325', '870', '2775', '2776', '860', '2911', '3021', '438', '592', '3006', '3007',
        '3008', '590', '2734', '2735', '594', '595', '596', '1896', '669', '467', '468', '3097', '2961', '2962',
        '2761', '495', '1831', '1832', '601', '2892', '1841', '611', '612', '622', '3019', '1856', '3022', '625',
        '627', '2879', '614', '616', '618', '620', '621', '651', '2748', '893', '894', '895', '896', '898', '1154',
        '1209', '1210', '962', '939', '940', '941', '918', '2777', '910', '911', '912', '913', '914', '915', '916',
        '929', '189', '219', '1463', '1508', '2367', '357', '358', '359', '998', '2881', '2875', '3080', '307', '564',
        '312', '58', '314', '1084', '2372', '1115', '611', '347', '3205', '3217', '763', '441', '2702', '334', '2692',
        '339', '340', '341', '342', '1256', '880', '881', '882', '884', '885', '886', '887', '955', '353', '969',
        '41', '54', '43', '3101', '2659', '53', '45', '44', '50', '49', '56', '46', '40', '3229', '801', '803', '647',
        '454', '1862', '6', '128', '1366', '317', '3466', '319', '320', '10', '7', '1011', '1012', '2782', '1015',
        '1017', '1019', '1021', '1023', '1025', '2783', '2784', '331', '2905', '2680', '608', '3541', '3330', '315',
        '673', '676', '684', '124']

    const priceEditsAllowed = [1, 9, 58, 62, 56, 83, 61, 17, 43, 101, 67, 43, 45, 60]

    hiddenElements.each(function( index, element ) {
        if ($( this ).val()) {
            return
        }

        let panel = $( this ).parents('.panel'),
            panelBody = panel.children('.panel-body'),
            fa = panel.find('.fa')

        if (fa.hasClass('d-none')) {
            fa.removeClass('d-none')
        }
    })

    // body.on('change', '.methods', function (e) {
    //     let id = $(this).parent('.tdGost').find('.methods-id').val(),
    //         options = '',
    //         input = $(e.target),
    //         wrapperMaterial = input.closest('.wrapper-material'),
    //         list = $(this).parents('.tr-gost').find('.conditions')
    //     $.ajax({
    //         method: 'POST',
    //         url: '/ulab/requirement/getTuForGostAjax',
    //         data: {
    //             id: id
    //         },
    //         dataType: 'json',
    //         success: function (data) {
    //             wrapperMaterial.find(`#conditions_list${id}`).remove()
    //             if (data) {
    //                 if (data.length !== 0) {
    //                     $.each(data, function (index, element) {
    //                         options += `<option data-value="${element.ID}"
    //                                             data-type="${element.NORM_TEXT}">${element.view_gost}</option>`
    //                     })
    //
    //                     let wrapperDatalist = `<datalist id="conditions_list${id}">
    //                                         ${options}
    //                                         </datalist>`;
    //
    //                     wrapperMaterial.append(wrapperDatalist)
    //
    //                     list.attr("list", "conditions_list" + id)
    //                 } else {
    //                     list.attr("list", "conditions_list")
    //                 }
    //             }
    //         }
    //     })
    // })


    $('.price').each(function (index, item) {
        let inputPrice = $(item),
            price = inputPrice.val(),
            currentUserId = +$('.user-id').val()

        if ($.inArray(price, methodsId) === -1 && $.inArray(currentUserId, priceEditsAllowed) === -1) {
            inputPrice.prop('readonly', true)
        } else {
            inputPrice.prop('readonly', false)
        }
    })


    body.on('input', '.material-count, .material', function (e) {
        let input = $(e.target),
            value = $(e.target).val()
            view = $(`#${input.attr('id')}-view`)

        view.text(value)
    })


    body.on('change', '.material', function (e) {
        let input = $(e.target),
            hiddenInput = $('#' + input.attr('id') + '-hidden'),
            materialId = hiddenInput.val().trim(),
            wrapperMaterial = input.closest('.wrapper-material'),
            options = ''

        wrapperMaterial.find('.wrapper-gost').remove()

        $.ajax({
            method: 'POST',
            url: '/ulab/requirement/getGostsGroupAjax',
            data: {
                id: materialId
            },
            dataType: 'json',
            success: function (data) {

                if (data) {
                    $.each(data, function (index, element) {
                        console.log('index', index)
                        console.log('element', element.GOST)
                        options += `<option value="${element.GOST}">${element.GOST}</option>`
                    })

                    let wrapperGost = `<div class="row wrapper-gost align-items-end">
                                            <div class="form-group col-sm-6">
                                                <lable for="gostToMaterial">Гост</lable>
                                                <div class="row align-items-center wrapper-items-gost">
                                                    <div class="col">
                                                        <select class="form-select gosts-group" id="gostToMaterial">
                                                            <option value="" selected disabled></option>
                                                            ${options}
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group col-auto">
                                                <button type="button" class="btn btn-primary add-gost mb-0">
                                                    <svg class="icon" width="15" height="15">
                                                        <use xlink:href="/ulab/assets/images/icons.svg#add"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>`

                    wrapperMaterial.find('.wrapper-add-material').append(wrapperGost)
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
                    msg = '5 Requested JSON parse failed.';
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
    })

    body.on('click', '.btn-add-material', function () {
        let currentMaterial = $(this).closest('.wrapper-material'),
            materialsCounter = $('.wrapper-materials').data('materialsCounter'),
            countMaterials = $('.wrapper-material').length,
            colAddMaterial = $('.col-add-material')

        if (materialsCounter) {
            countMaterials = materialsCounter
        }

        countMaterials++

        $('.wrapper-materials').data('materialsCounter', countMaterials)

        let newMaterial = `<div class="wrapper-material mb-5">
                <div class="row g-3 align-items-center mb-2 material-data">
                    <div class="col-auto">
                        <span class="material-number" data-material-number="${countMaterials}">${countMaterials}</span>
                    </div>
                    <div class="col-auto col-material-name">
                        <button type="button" class="material-name p-2 border-0" id="material${countMaterials}-view"
                                data-bs-toggle="collapse" data-bs-target="#collapseMaterial${countMaterials}" aria-expanded="false"
                                aria-controls="collapseMaterial${countMaterials}">Материал не выбран <span class='redStars'>*<span></button>
                    </div>
                    <div class="col-auto">|</div>
                    <div class="col-auto">
                        <label for="inputMaterialCount" class="col-form-label">образцов</label>
                    </div>
                    <div class="col-auto me-auto">
                        <span class="form-control border-0" id="amount${countMaterials}-view">1</span>
                    </div>
                    <div class="col-auto">
                        <button type="button" name="add_material" class="btn btn-primary btn-add-material w-100 mw-100">Добавить материал</button>
                    </div>
                    <div class="col-auto">
                        <button type="button" name="del_material" class="btn btn-danger del-material">Удалить материал</button>
                    </div>
                </div>

                <div class="wrapper-add-material mb-3 mt-3 flex-column collapse" id="collapseMaterial${countMaterials}">
                    <div class="row">
                        <div class="form-group col-sm-6">
                            <lable for="material">Материал <span class='redStars'>*<span></lable>
                            <input type="hidden" id="material${countMaterials}-hidden" name="material[${countMaterials}][id]" value="">
                            <input class="form-control material" id="material${countMaterials}" type="text" name="material[${countMaterials}][name]"
                                   list="materials" value="" autocomplete="off" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-sm-6 amount">
                            <lable for="amount">Количество проб/образцов <span class='redStars'>*<span></lable>
                            <input class="form-control number-only material-count mw-100" id="amount${countMaterials}" type='text'
                                   name='amount[${countMaterials}]' value="1" required>
                        </div>
                    </div>

                    <div class="row wrapper-gost d-none">
                        <div class="form-group col-sm-6">
                            <lable for="gostToMaterial">Гост</lable>
                            <div class="row align-items-center wrapper-items-gost">
                                <div class="col">
                                    <select class="form-select gosts-group" id="gostToMaterial">
                                        <option selected disabled></option>
                                        <?php foreach ($material['gosts'] as $gost): ?>
                                            <option><?= $gost['GOST'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" class="hidden-id-gost" value=''>
                                </div>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-primary add-gost">
                                        <svg class="icon" width="15" height="15">
                                            <use xlink:href="/ulab/assets/images/icons.svg#add"/>
                                        </svg>
                                    </button>
                                </div>
                                <div class="col-auto col-del-gost">
                                    <button type="button" class="btn shadow-none">
                                        <svg class="icon" width="15" height="15">
                                            <use xlink:href="#"></use>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--./wrapper-add-material-->

                <table class="table table-gosts">
                <thead>
                    <tr class="table-secondary align-middle">
                        <td scope="col" class="col-methods">Методики испытаний</td>
                        <td scope="col" class="col-methods-link"></td>
                        <td scope="col" class="col-conditions">Тех. условия</td>
                        <td scope="col" class="col-price">Исполнитель</td>
                        <td scope="col" class="col-price">Цена</td>
                        <td scope="col" class="col-no-price">Без цены</td>
                        <td scope="col" class="col-not-match"></td>
                        <td scope="col" class="col-btn-methods">
                            <button type="button" class="btn btn-danger del-gosts btn-square" type="button" name="del_gosts">
                                <i class="fa-solid fa-minus icon-fix"></i>
                            </button>
                        </td>
                        <td class="col-btn-methods"></td>
                    </tr>
                </thead>
                <tbody class="tbody-gost">
                    <tr class="align-middle tr-gost">
                        <td class="tdGost">
                            <input class="form-control methods" id="material${countMaterials}-methods0" list="methods_list" type="text" name="methods[${countMaterials}][0][name]" value="" autocomplete="off" required>
                            <input type="hidden" name="methods[${countMaterials}][0][id]" id="material${countMaterials}-methods0-hidden" class="methods-id" value="">
                        </td>
                        <td class="text-center td-method-link">
                            <a class="link-tab method-link" target='_blank'>
                                <svg class="icon" width="35" height="35">
                                    <use xlink:href="/ulab/assets/images/icons.svg#tab"/>
                                </svg>
                            </a>
                        </td>
                        <td class="tdGost">
                            <input class="form-control conditions" id="material${countMaterials}-conditions0" list="conditions_list" type="text" name="conditions[${countMaterials}][0][name]" value="-- | --" autocomplete="off" required>
                            <input type="hidden" name="conditions[${countMaterials}][0][id]" id="material${countMaterials}-conditions0-hidden" class="conditions_id" value="2522">
                        </td>
                        <td class="tdGost">
                            <select class="form-select assign_method" id="assign_method${countMaterials}-unit0" name="assign_method[${countMaterials}][0]">                    
                                <option value="">Выбрать сотрудника</option>
                            </select>
                        </td>
                        <td>
                            <input type="number" class="form-control price"
                                   name="price[${countMaterials}][0]" value="" step="0.01" required>
                           <div class="form-control text-center dash d-none">
                                <svg class="icon" width="15" height="15">
                                    <use xlink:href="/ulab/assets/images/icons.svg#del"/>
                                </svg>
                           </div>
                        </td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" class="form-check-input mt-0 no-price" name="no_price" value="">
                                <span class="slider"></span>
                            </label>
                        </td>
                        <td class="td-not-match"></td>
                        <td>
                            <button class="btn btn-primary mt-0 add-methods btn-square" type="button">
                                <i class="fa-solid fa-plus icon-fix"></i>
                            </button>
                        </td>
                        <td>
                            <button type="button" class="btn shadow-none">
                                <svg class="icon" width="15" height="15">
                                    <use xlink:href="#"></use>
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>`

        if (!currentMaterial.length) {
            wrapperBtn = $(this).closest('.wrapper-buttons')
            wrapperBtn.before(newMaterial)
        } else {
            currentMaterial.after(newMaterial)
        }

        if ($('.wrapper-material').length && colAddMaterial.hasClass('d-block')) {
            colAddMaterial.removeClass('d-block').addClass('d-none')
        }
    })

    body.on('click', '.del-material', function () {
        let wrapperMaterial = $(this).closest('.wrapper-material'),
            wrapperMaterials = $(this).closest('.wrapper-materials'),
            colAddMaterial = $('.col-add-material')

        wrapperMaterial.remove()

        if (!$('.wrapper-material').length && $(colAddMaterial).hasClass('d-none')) {
            $(colAddMaterial).removeClass('d-none').addClass('d-block')
        }
    })

    body.on('click', '.del-permanent-material', function () {

        if ( confirm("Подтверждаете удаление материала из заявки?") ) {

            let $button = $(this)
            let dealId = $button.data('deal_id')
            let materialId = $button.data('material_id')
            let mtrId = $button.data('mtr_id')
            let number = $button.data('number')

            $.ajax({
                method: 'POST',
                url: '/ulab/requirement/deletePermanentMaterialAjax',
                data: {
                    deal_id: dealId,
                    material_id: materialId,
                    mtr_id: mtrId,
                    number: number,
                },
                dataType: 'html',
                success: function () {
                    let wrapperMaterial = $button.closest('.wrapper-material'),
                        wrapperMaterials = $button.closest('.wrapper-materials'),
                        colAddMaterial = $('.col-add-material')

                    wrapperMaterial.remove()

                    if (!$('.wrapper-material').length && $(colAddMaterial).hasClass('d-none')) {
                        $(colAddMaterial).removeClass('d-none').addClass('d-block')
                    }
                }
            })
        }
    })


    body.on('click', '.del-permanent-material-gost', function () {

        let $button = $(this)
        let gtpId = $button.data('gtp_id')
        let dealId = $button.data('deal_id')
        let materialId = $button.data('material_id')
        let number = $button.data('number')
        let numberGost = $button.data('number_gost')

        if ( confirm("Подтверждаете удаление методики у материала?") ) {

            $.ajax({
                method: 'POST',
                url: '/ulab/requirement/deletePermanentMaterialGostAjax',
                data: {
                    gtp_id: gtpId,
                    deal_id: dealId,
                    material_id: materialId,
                    number: number,
                    numberGost: numberGost,
                },
                dataType: 'html',
                success: function (data) {
                    console.log(data)
                    let trGost = $button.closest('.tr-gost')

                    trGost.remove()
                }
            })
        }
    })


    body.on('change', '.methods', function () {
        let methodId = $(this).parent('.tdGost').find('.methods-id').val(),
            td = $(this).parents('.tr-gost').find('.assign_method'),
            option = ''

        $(this).removeClass('is-invalid')
        $(this).next('.is-invalid').remove()

        $.ajax({
            method: 'POST',
            url: '/ulab/requirement/getUlabAssignedByGostIdAjax',
            data: {
                id: methodId
            },
            dataType: 'json',
            success: function (data) {
                console.log(data)
                data.forEach(function (val) {
                    option += `<option value="${val.id}">${val.name}</option>`
                })
                td.append(option)
            }
        })
    })


    body.on('change', '.methods', function (e) {
        let inputMethods = $(e.target),
            method = inputMethods.val(),
            trGost = inputMethods.closest('.tr-gost'),
            methodId = trGost.find('.methods-id').val(),
            methodLink = trGost.find('.method-link'),
            inputPrice = trGost.find('.price'),
            dash = trGost.find('.dash'),
            noPrice = trGost.find('.no-price')

        let price = $(`#${inputMethods.attr('list')} option`).filter(function () {
            return $(this).val() === method
        }).data('price')

        inputMethods.data('price', price)
        methodLink.attr('href', `/ulab/gost/method/${methodId}`)
        inputPrice.val(price)
        inputPrice.removeClass('d-none')
        noPrice.prop('checked', false)

        if (!dash.hasClass('d-none')) {
            dash.addClass('d-none')
        }
    })

    body.on('click', '.add-methods', function () {
        let trGost = $(this).closest('.tr-gost'),
            gostsCounter = $(this).closest('.tbody-gost').data('gostsCounter'),
            reg = /\d+/,
            methodId = trGost.find('.methods').attr('id').split('-'),
            materialNumber = methodId[0].match(reg),
            countMethods = $(this).closest('.tbody-gost').find('.tr-gost').length

        if (gostsCounter) {
            countMethods = gostsCounter
        }

        countMethods++

        $(this).closest('.tbody-gost').data('gostsCounter', countMethods)
        
        trGost.after(
            `<tr class="align-middle tr-gost">
                <td class="tdGost">
                    <input class="form-control methods" id="material${materialNumber[0]}-methods${countMethods}" list="methods_list" type="text" name="methods[${materialNumber[0]}][${countMethods}][name]" value="" autocomplete="off" required>
                    <input type="hidden" name="methods[${materialNumber[0]}][${countMethods}][id]" id="material${materialNumber[0]}-methods${countMethods}-hidden" class="methods-id" value="">
                </td>
                <td class="text-center td-method-link">
                    <a class="link-tab method-link" target='_blank'>
                        <svg class="icon" width="35" height="35">
                            <use xlink:href="/ulab/assets/images/icons.svg#tab"/>
                        </svg>
                    </a>
                </td>
                <td class="tdGost">
                    <input class="form-control conditions" id="material${materialNumber[0]}-conditions${countMethods}" list="conditions_list" type="text" name="conditions[${materialNumber[0]}][${countMethods}][name]" value="-- | --" autocomplete="off" required>
                    <input type="hidden" name="conditions[${materialNumber[0]}][${countMethods}][id]" id="material${materialNumber[0]}-conditions${countMethods}-hidden" class="conditions_id" value="2522">
                </td>
                <td class="tdGost">
                    <select class="form-select assign_method" id="assign_method${materialNumber[0]}-unit${materialNumber[0]}" name="assign_method[${materialNumber[0]}][${countMethods}]">                    
                        <option value="">Выбрать сотрудника</option>
                    </select>
                </td>
                <td>
                    <input type="number" class="form-control price"
                           name="price[${materialNumber[0]}][${countMethods}]" value="" step="0.01" required>
                   <div class="form-control text-center dash d-none">
                        <svg class="icon" width="15" height="15">
                            <use xlink:href="/ulab/assets/images/icons.svg#del"/>
                        </svg>
                   </div>
                </td>
                <td>
                    <label class="switch">
                        <input type="checkbox" class="form-check-input mt-0 no-price" name="no_price">
                        <span class="slider"></span>
                    </label>
                </td>
                <td class="td-not-match"></td>
                <td>
                    <button class="btn btn-primary mt-0 add-methods btn-square" type="button">
                        <i class="fa-solid fa-plus icon-fix"></i>
                    </button>
                </td>
                <td>
                    <button class="btn btn-danger mt-0 del btn-square" type="button">
                        <i class="fa-solid fa-minus icon-fix"></i>
                    </button>
                </td>
            </tr>`
        )
    })

    body.on('click', '.tr-gost button.del', function () {
        let trGost = $(this).closest('.tr-gost')

        trGost.remove()
    })

    body.on('click', '.del-gosts', function () {
        let wrapperMaterial = $(this).closest('.wrapper-material'),
            trGost = $(wrapperMaterial).find('.tr-gost'),
            tbodyGost = $(wrapperMaterial).find('.tbody-gost'),
            materialNumber = $(wrapperMaterial).find('.material-number').data('materialNumber')


        wrapperMaterial.find('.del-gost').closest('.wrapper-gost').remove()
        wrapperMaterial.find('.add-gost').closest('.wrapper-gost').find('.gosts-group option:first').prop('selected', true)

        trGost.remove()

        tbodyGost.html(
            `<tr class="align-middle tr-gost">
                <td class="tdGost">
                    <input class="form-control methods" id="material${materialNumber}-methods0" list="methods_list" type="text" name="methods[${materialNumber}][0][name]" value="" autocomplete="off" required>
                    <input type="hidden" name="methods[${materialNumber}][0][id]" id="material${materialNumber}-methods0-hidden" class="methods-id" value="">
                </td>
                <td class="text-center td-method-link">
                    <a class="link-tab method-link" target='_blank'>
                        <svg class="icon" width="35" height="35">
                            <use xlink:href="/ulab/assets/images/icons.svg#tab"/>
                        </svg>
                    </a>
                </td>
                <td class="tdGost">
                    <input class="form-control conditions" id="material${materialNumber}-conditions0" list="conditions_list" type="text" name="conditions[${materialNumber}][0][name]" value="-- | --" autocomplete="off" required>
                    <input type="hidden" name="conditions[${materialNumber}][0][id]" id="material${materialNumber}-conditions0-hidden" class="conditions_id" value="2522">
                </td>
                <td>
                    <input type="number" class="form-control price"
                           name="price[${materialNumber}][0]" value="" step="0.01" required>
                   <div class="form-control text-center dash d-none">
                        <svg class="icon" width="15" height="15">
                            <use xlink:href="/ulab/assets/images/icons.svg#del"/>
                        </svg>
                   </div>
                </td>
                <td>
                    <label class="switch">
                        <input type="checkbox" class="form-check-input mt-0 no-price" name="no_price">
                        <span class="slider"></span>
                    </label>
                </td>
                <td class="td-not-match"></td>
                <td>
                    <button class="btn btn-primary mt-0 add-methods btn-square" type="button">
                        <i class="fa-solid fa-plus icon-fix"></i>
                    </button>
                </td>
                <td>
                    <button type="button" class="btn shadow-none">
                        <svg class="icon" width="15" height="15">
                            <use xlink:href="#"></use>
                        </svg>
                    </button>
                </td>
            </tr>`
        )
    })


    body.on('click', '.add-gost', function () {
        console.log('add-gost')

        let wrapperGost = $(this).parents('.wrapper-gost'),
            cloneWrapperGost = $(wrapperGost).clone(true)

        cloneWrapperGost.find('.add-gost').replaceWith(
            `<button type="button" class="btn btn-danger del-gost mb-0">
                <svg class="icon" width="15" height="15">
                    <use xlink:href="/ulab/assets/images/icons.svg#del"/>
                </svg>
            </button>`
        );

        cloneWrapperGost.find('.gosts-group').removeData('prev');

        wrapperGost.after(cloneWrapperGost)
    })

    body.on('click', '.del-gost', function () {
        let wrapperGost = $(this).parents('.wrapper-gost'),
            wrapperMaterial = $(this).closest('.wrapper-material'),
            gostName = wrapperGost.find('.gosts-group').val()

        wrapperMaterial.find(`[data-gost="${gostName}"]`).remove()
        wrapperGost.remove()
    })

    body.on('change', '.gosts-group', function () {
        let gostName = $(this).val(),
            prevGostName = $(this).data('prev'),
            dealId = $('.deal-id').val(),
            wrapperMaterial = $(this).closest('.wrapper-material')


        let selectedGostCopy = $(this).closest('.wrapper-material').find('.gosts-group').not(this).filter(function() {
            return gostName === $(this).val()
        })

        if (selectedGostCopy.length) {

            $.magnificPopup.open({
                items: {
                    src: `<div id="modal-gost-error" class="bg-light col-md-4 m-auto p-3 position-relative">
                            <div class="title mb-3 h-2">
                                Внимание
                            </div>
                    
                            <div class="line-dashed-small"></div>
                    
                            <div class="mb-3">
                                ${selectedGostCopy.val()} уже выбран!
                            </div>
                        </div>`,
                    type: 'inline',
                    fixedContentPos: false
                }
            })


            if (prevGostName) {
                $(`option[value="${prevGostName}"]`, this).prop('selected', true)
            } else {
                $('option:first', this).prop('selected', true)
            }

            return false
        }


        $(this).data('prev', gostName)

        let trGostLast = wrapperMaterial.find('.tr-gost:last'),
            tbodyGost = wrapperMaterial.find('.tbody-gost'),
            reg = /\d+/,
            methodElement = trGostLast.find('.methods'),
            methodValue = methodElement.val(),
            methodId = methodElement.attr('id').split('-'),
            materialNumber = methodId[0].match(reg)


        $.ajax({
            method: 'POST',
            url: '/ulab/requirement/getMethodListAjax',
            data: {
                gost: gostName,
                deal_id: dealId
            },
            dataType: 'json',
            success: function (data) {
                if (!data.length) {
                    return false
                }

                wrapperMaterial.find(`[data-gost="${prevGostName}"]`).remove()

                let countMethods = wrapperMaterial.find('.tbody-gost .tr-gost').length,
                    methodsAmount = countMethods

                if (!methodValue && countMethods === 1) {
                    methodsAmount = 0
                }

                $.each(data, function(i, item) {
                    let trGost = `<tr class="align-middle tr-gost" data-gost="${gostName}">
                        <td class="tdGost">
                            <input class="form-control methods" id="material${materialNumber[0]}-methods${methodsAmount + i}" list="methods_list" type="text" name="methods[${materialNumber[0]}][${methodsAmount + i}][name]" value="${item.view_gost}" data-price="${item.PRICE}" autocomplete="off" required>
                            <input type="hidden" name="methods[${materialNumber[0]}][${methodsAmount + i}][id]" id="material${materialNumber[0]}-methods${methodsAmount + i}-hidden" class="methods-id" value="${item.ID}">
                        </td>
                        <td class="text-center td-method-link">
                            <a class="link-tab method-link" target='_blank'>
                                <svg class="icon" width="35" height="35">
                                    <use xlink:href="/ulab/assets/images/icons.svg#tab"/>
                                </svg>
                            </a>
                        </td>
                        <td class="tdGost">
                            <input class="form-control conditions" id="material${materialNumber[0]}-conditions${methodsAmount + i}" list="conditions_list" type="text" name="conditions[${materialNumber[0]}][${methodsAmount + i}][name]" value="-- | --" autocomplete="off" required>
                            <input type="hidden" name="conditions[${materialNumber[0]}][${methodsAmount + i}][id]" id="material${materialNumber[0]}-conditions${methodsAmount + i}-hidden" class="conditions_id" value="2522">
                        </td>
                        <td>
                            <input type="number" class="form-control price"
                                   name="price[${materialNumber[0]}][${methodsAmount + i}]" value="${item.PRICE}" step="0.01" required>
                           <div class="form-control text-center dash d-none">
                                <svg class="icon" width="15" height="15">
                                    <use xlink:href="/ulab/assets/images/icons.svg#del"/>
                                </svg>
                           </div>
                        </td>
                        <td>
                            <label class="switch">
                                <input type="checkbox" class="form-check-input mt-0 no-price" name="no_price">
                                <span class="slider"></span>
                            </label>
                        </td>
                        <td class="td-not-match"></td>
                        <td>
                            <button class="btn btn-primary mt-0 add-methods btn-square" type="button">
                                <i class="fa-solid fa-plus icon-fix"></i>
                            </button>
                        </td>
                        <td>
                            <button class="btn btn-danger mt-0 del btn-square" type="button">
                                <i class="fa-solid fa-minus icon-fix"></i>
                            </button>
                        </td>
                    </tr>`

                    if (!methodValue && countMethods === 1 && i === 0 || countMethods === 0 && i === 0) {
                        trGost = $(trGost)

                        trGost.find('.del').replaceWith(
                            `<button type="button" class="btn shadow-none">
                                <svg class="icon" width="15" height="15">
                                    <use xlink:href="#"></use>
                                </svg>
                            </button>`
                        );
                    }

                    tbodyGost.append(trGost)

                    if (!methodValue && countMethods === 1) {
                        trGostLast.remove()
                    }
                });
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
                    msg = '6 Requested JSON parse failed.';
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
    })


    body.on('change', '.no-price', function (e) {
        let noPrice = $(e.target),
            trGost = noPrice.closest('.tr-gost'),
            price = trGost.find('.price'),
            dash = trGost.find('.dash'),
            prevPrice = price.data('prevPrice')

        price.data('prev-price', price.val())

        if (noPrice.prop('checked')) {
            price.addClass('d-none')
            price.val(0)
            dash.removeClass('d-none')
        } else {
            price.removeClass('d-none')
            price.val(prevPrice)
            dash.addClass('d-none')
        }
    })


    let getLabUserContent = function(usersByLab) {
        let options = ''

        if (!usersByLab) {
            return content
        }

        $.each(usersByLab, function (index, value) {
            let i = 0

            $.each(value, function (ind, val) {
                if (i === 0) {
                    options += `<optgroup label="${val.department_name}">`
                }

                options += `<option value="${val.user_id}">${val.user_name}</option>`

                i++
            })

            options += `</optgroup>`
        })

        content = `<div class="row">
                        <div class="form-group col">
                            <select class="form-select lab-users" id="lab_users" name="lab_users">
                                ${options}
                            </select>
                        </div>
                    </div>`

        return content

        //Для вывода всех списков
        /*let content = ''

        if (!usersByLab) {
            return content
        }

        $.each(usersByLab, function (index, value) {
            let departmentName = '',
                options = ''

            $.each(value, function (ind, val) {
                departmentName = val.department_name
                options += `<option value="${val.user_id}">${val.user_name}</option>`
            })


            content += `<div class="row">
                        <div class="form-group col">
                            <label for="lab_users${index}">${departmentName}</label>
                            <select class="form-select lab-users${index}" id="lab_users${index}" name="lab_users${index}" value="">
                                ${options}
                            </select>
                        </div>
                    </div>`
        })

        return content*/
    }

    /* проверяет соответствие методики и ответсвенных */
    let methodCheck = function(tzId, dealId, methodsId) {
        let isConform = true

        usersByLab = [];

        $.ajax({
            method: "POST",
            //url: "/alarm_for_tz.php",
            url: "/ulab/requirement/isConfirmMethodAjax",
            data: {
                methods_id: methodsId,
                id: tzId
            },
            async: false,
            dataType: "json",
            success: function (data) {
                let content = ''

                if (!$(data).length) {
                    return false
                }

                isConform = false
                usersByLab = data['users_by_lab']

                $('.not-match').remove()

                $.each(data['not_match'], function (index, value) {
                    console.log('value', index)
                    let methodsId = $(`.methods-id[value="${index}"]`),
                        labsId = value.join('_')

                    $.each(methodsId, function (ind, val) {
                        let tdNotMatch = $(val).closest('.tr-gost').find('.td-not-match')

                        tdNotMatch.html(
                            `<button type="button" 
                                    class="btn btn-outline-primary text-nowrap text-truncate mt-0 me-3 not-match"
                                    data-labs-id="${labsId}">
                                    Не соответсвует
                            </button>`
                        )
                    })
                })


                //content = getLabUserContent(usersByLab)
                //${content}

                $.magnificPopup.open({
                    items: {
                        src: `<div id="modal-not-match" class="bg-light col-md-4 m-auto p-3 position-relative">
                            <div class="title mb-3 h-2">
                                Внимание
                            </div>
                    
                            <div class="line-dashed-small"></div>
                    
                            <div class="mb-3">
                                Методика не соответствует лаборатории
                            </div>
                            
                            <!--<div class="line-dashed-small"></div>

                            <button type="button" class="btn btn-primary btn-add-assigned">Отправить</button>-->
                        </div>`,
                        type: 'inline',
                        fixedContentPos: false
                    }
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
                    msg = '0 Requested JSON parse failed.';
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

        return isConform
    }

    body.on('click', '.not-match', function () {
        let labsId = $(this).data('labsId').toString().split('_'),
            modalFormNotMatch = $('#modal_form_not_match'),
            contentNotMatch = modalFormNotMatch.find('.content-not-match'),
            content = '',
            usersLab = {}

        $.each(labsId, function (index, value) {
            if (!usersByLab[value]) {
                return
            }

            usersLab[value] = usersByLab[value]
        })

        content = getLabUserContent(usersLab)

        contentNotMatch.html(content)

        $.magnificPopup.open({
            modal: true,
            items: {
                src: modalFormNotMatch,
                type: 'inline',
                fixedContentPos: false
            }
        })
    })

    $('#modal_form_not_match').submit(function (e) {
        e.preventDefault()

        let modalFormNotMatch = $(e.target),
            selectedOption = modalFormNotMatch.find('select option:selected'),
            selectedOptionValue = modalFormNotMatch.find('select option:selected').val(),
            tzId = $('.tz-id').val(),
            dealId = $('.deal-id').val()

        $.ajax({
            method: 'POST',
            url: '/add_user_for_tz.php',
            data: {
                id_user: selectedOptionValue,
                edit: tzId,
                id: dealId
            },
            dataType: "json",
            success: function (data) {
                $.magnificPopup.close()

                if (data['resultUpdate']) {

                    $.magnificPopup.open({
                        items: {
                            src: `<div class="bg-light col-md-4 m-auto p-3 position-relative">
                                    <div class="title mb-3 h-2">
                                        Внимание
                                    </div>
                            
                                    <div class="line-dashed-small"></div>
                            
                                    <div class="mb-3">
                                        Сотрудник добавлен в ответственные, для подтверждения изменений сохраните ТЗ
                                    </div>
                                </div>`,
                            type: 'inline',
                            fixedContentPos: false
                        }
                    })
                } else {
                    $.magnificPopup.open({
                        items: {
                            src: `<div class="bg-light col-md-4 m-auto p-3 position-relative">
                                    <div class="title mb-3 h-2">
                                        Внимание
                                    </div>
                            
                                    <div class="line-dashed-small"></div>
                            
                                    <div class="mb-3">
                                        Сотрудник в ответственные не добавлен
                                    </div>
                                </div>`,
                            type: 'inline',
                            fixedContentPos: false
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
        })

        return false
    })


    body.on('click', '#save', function (event) {
        let hiddenElements = $('input,textarea,select').filter('[required]:hidden')

        hiddenElements.each(function( index, element ) {
            if ($( this ).val()) {
                return
            }

            let panel = $( this ).parents('.panel'),
                panelBody = panel.children('.panel-body'),
                fa = panel.find('.fa'),
                wrapperAddMaterial = $( this ).parents('.wrapper-add-material')


            if (fa.hasClass('fa-chevron-up')) {
                fa.removeClass('fa-chevron-up').addClass('fa-chevron-down')
                panelBody.slideDown(500)
            }

            if (!wrapperAddMaterial) {
                return
            }

            if (!wrapperAddMaterial.hasClass('show')) {
                wrapperAddMaterial.addClass('show')
            }
        })
    })

    $('#form_requirement').submit(function (event) {
        let tzId = $('.tz-id').val(),
            dealId = $('.deal-id').val(),
            isNoPrice = false,
            currentUserId = +$('.user-id').val(),
            formGroupLabs = '',
            modalFormAddLab = $('#modal-form-add-lab'),
            labs = $('#modal-form-add-lab').find('.labs')

        const forbiddenCreateNewGost = [56]

        //Проверка методики без цены
        $('.price').each(function (index, item) {
            let price = $(item).val(),
                trGost = $(item).closest('.tr-gost'),
                noPrice = trGost.find('.no-price')

            if (price && price !== '0' && price !== '') {
                return
            }

            if (!noPrice.prop('checked')) {
                isNoPrice = true

                $('.alert-title').text('Внимание!')
                $('.alert-content').text('Пожалуйста, укажите стоимость или поставьте отметку об отсутствии цены')

                $.magnificPopup.open({
                    items: {
                        src: $('#alert_modal'),
                        type: 'inline',
                        fixedContentPos: false
                    }
                })

                return false
            }
        })

        if (isNoPrice) {
            event.preventDefault()
            return false
        }


        /*if (!$('.hidden-is-discount').val() && $('.input-discount').val()) {
            var save = confirm('У Вас есть скидка, Вы ее не применили. Хотите сохранить');
            if (!save) {
                event.preventDefault();
                return true;
            }
        }*/


        let inputMethodsId = $('.methods-id').filter(function () {
            return $(this).val() === '';
        })

        if (inputMethodsId.length && $.inArray(currentUserId, forbiddenCreateNewGost) !== -1) {
            event.preventDefault()
            $('.alert-title').text('Внимание!')
            $('.alert-content').text('Вам закрыт доступ на создание новых ГОСТов!')

            $.magnificPopup.open({
                items: {
                    src: $('#alert_modal'),
                    type: 'inline',
                    fixedContentPos: false
                }
            })

            return false
        }

        if (inputMethodsId.length && !labs.length) {
            event.preventDefault()

            inputMethodsId.each(function (index, element) {
                let methodId = $(element).attr('id').replace('-hidden', ''),
                    methodValue = $(`#${methodId}`).val(),
                    reg = /\d+/,
                    methodData = methodId.split('-'),
                    materialNumber = methodData[0].match(reg),
                    methodsNumber = methodData[1].match(reg)

                    formGroupLabs += `<div class="form-group">
                                        <label for="labs">${methodValue}</label>
                                        <select class="form-select labs" name="labs[${materialNumber}][${methodsNumber}]">
                                            <option value="LFHI">Лаборатория физико-химических испытаний</option>
                                            <option value="LSM">Лаборатория строительных материалов</option>
                                            <option value="LFMI">Лаборатория физико-механических испытаний</option>
                                            <option value="DSL">Дорожно-строительная лаборатория</option>
                                            <option value="OSK">Отдел строительного контроля</option>
                                        </select>
                                    </div>`
            })

            modalFormAddLab.find('.content-add-lab').html(formGroupLabs)

            $.magnificPopup.open({
                modal: true,
                items: {
                    src: modalFormAddLab,
                    type: 'inline',
                },
                callbacks: {
                    open: function () {
                        let $content = $(this.content),
                            formRequirement = $('#form_requirement')

                        $content.on('click', '.btn-add-lab', function() {
                            modalFormAddLab = $(modalFormAddLab).addClass('mfp-hide')

                            formRequirement.append(modalFormAddLab)

                            $.magnificPopup.close()

                            formRequirement.submit()
                        })
                    }
                }
            })
        }

        if (inputMethodsId.length && !labs.length) {
            return false
        }


        let methodsId = $('.methods-id').map(function (index, value) {
            if ($(value).val()) {
                return $(value).val()
            }
        }).get()

        let isConform = methodCheck(tzId, dealId, methodsId)

        if (!isConform) {
            return false
        }
    })


    $('.popup-with-form2').magnificPopup({
        items: {
            src: '#taken-modal-form',
            type: 'inline'
        },
        disableOn: function() {
            if($('.taken').prop('checked')) {
                return false;
            }
            return true;
        },
        fixedContentPos: false
    })

    body.on('change', '.taken', function () {
        if($('.taken').prop('checked')) {
            return false;
        }

        let tzId = $('.tz-id').val()

        $.ajax({
            method: 'POST',
            url: '/taken.php',
            data: {
                id_tz_del: tzId
            },
            success: function(data) {
                console.log('data', data)
                if (!data) {
                    $('.taken-request').text('')
                }
            }
        });
        $('.taken-request').text('');
    })

    $('#taken-modal-form').submit(function (event) {
        let inputTzId = $(this).find('input[name=tz_id]'),
            inputTakenRequests = $(this).find('input[name=taken_requests]'),
            takenRequest = $('.taken-request')

        let tzId = inputTzId.val(),
            takenRequests = inputTakenRequests.val()

        let takenId = $('#taken_requests option').filter(function() {
            return this.value === takenRequests;
        }).data('id')

        if (!takenId) {
            event.preventDefault()
            return false
        }


        $.ajax({
            method: 'POST',
            url: '/taken.php',
            data: {
                id_tz: tzId,
                id_deal: takenId
            },
            success: function (data) {
                console.log('data', data)
                //console.log('typeof data === \'string\'', typeof data === 'string')
                //console.log('data instanceof String', data instanceof String)

                //if (typeof data === 'string' || data instanceof String) {
                if (data) {
                    takenRequest.text(data)
                    $('.taken').prop('checked', true)
                    $.magnificPopup.close()
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
                    msg = '2 Requested JSON parse failed.';
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

        return false
    })


    /* Применить скидку */
    body.on('click', '.btn-discount', function (e) {
        let inputDiscount = $('.discount')
            discountPrice = inputDiscount.val()

        if (discountPrice > 50) {
            $('.alert-title').text('Внимание!')
            $('.alert-content').text('Скидка не может превышать 50%')

            inputDiscount.val(50)

            $.magnificPopup.open({
                items: {
                    src: $('#alert_modal'),
                    type: 'inline',
                    fixedContentPos: false
                }
            })

            return false
        }

        $('.methods').each(function (index, element) {
            let method = $(element),
                dataPrice = method.data('price'),
                trGost = method.closest('.tr-gost'),
                price = trGost.find('.price'),
                noPrice = trGost.find('.no-price')

            if (!noPrice.prop('checked')) {
                price.val(dataPrice - (dataPrice / 100) * discountPrice)
            }
        })

        $('.hidden-is-discount').val(1)
    })


    function checkLeader1(idTz, btn) {
        var data;
        $.ajax({
            method: 'POST',
            async: false,
            url: '/check_methods_and_responsib.php',
            type: 'json',
            data: {
                id_tz: idTz,
                transfer: 'transfer',
                btn : btn
            },
            success: function(dataTransfer) {

                if (dataTransfer) {
                    var parseDataTransfer = jQuery.parseJSON(dataTransfer);

                    data = parseDataTransfer;
                }
            }
        });

        return data;
    }


    //TODO: Переделать метод
    function checkLeader(tzId, btn) {
        var data;
        $.ajax({
            method: 'POST',
            async: false,
            url: '/check_methods_and_responsib.php',
            type: 'json',
            data: {id_tz: tzId, transfer: 'transfer', btn : btn},
            success: function(dataTransfer) {

                console.log('dataTransfer', dataTransfer)

                if (dataTransfer) {
                    var parseDataTransfer = jQuery.parseJSON(dataTransfer);

                    data = parseDataTransfer;
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
                    msg = '3 Requested JSON parse failed.';
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

        return data;
    }

    /* Подтверждение ТЗ*/
    /* Передать */
    body.on('click', '.btn-transfer', function () {
        console.log('btn-transfer')

        let tzId = $('.tz-id').val(),
            dealId = $('.deal-id').val()

        let methodsId = $('.methods-id').map(function (index, value) {
            if ($(value).val()) {
                return $(value).val()
            }
        }).get()

        let isConform = methodCheck(tzId, dealId, methodsId)

        console.log('isConform', isConform)

        if (!isConform || !tzId) {
            return false
        }

        let transferCheckLeader = checkLeader(tzId, 'btn_transfer')

        console.log(transferCheckLeader)

        if (transferCheckLeader['error']) {

            $('.alert-title').text('Внимание!')
            $('.alert-content').text(transferCheckLeader['error'])

            $.magnificPopup.open({
                items: {
                    src: $('#alert_modal'),
                    type: 'inline',
                    fixedContentPos: false
                }
            })

            return false
        }

        if (transferCheckLeader['transfer']) {
            $('.btn-transfer').prop('disabled', true)

            $('.footer-confirm').html('<strong>Заявка передана на рассмотрение!</strong>') //TODO: убрать?
        }
    })

    /* Утвердить */
    body.on('click', '.btn-approve', function () {
        console.log('btn-approve')

        let tzId = $('.tz-id').val(),
            dealId = $('.deal-id').val()

        let methodsId = $('.methods-id').map(function (index, value) {
            if ($(value).val()) {
                return $(value).val()
            }
        }).get()

        let isConform = methodCheck(tzId, dealId, methodsId)

        if (!isConform) {
            $('.btn-approve').prop('disabled', false)
            return false
        }


        let confirmCheckLeader = checkLeader(tzId, 'btn_confirm')

        if (confirmCheckLeader['error']) {
            $('.alert-title').text('Внимание!')
            $('.alert-content').text(confirmCheckLeader['error'])

            $.magnificPopup.open({
                items: {
                    src: $('#alert_modal'),
                    type: 'inline',
                    fixedContentPos: false
                }
            })

            $('.btn-approve').prop('disabled', false)

            return false
        }

        if (confirmCheckLeader['transfer']) {
            $.ajax({
                method: 'POST',
                url: '/check_methods_and_responsib.php',
                data: {
                    id_tz: tzId,
                    tz_confirm: 1,
                    checbox_confirm: 'confirm'
                },
                success: function(dataConfirm) {

                    console.log('dataConfirm', dataConfirm)

                    if (dataConfirm) {
                        $('.footer-confirm').html(`<strong>${dataConfirm}</strong>`)
                        // если ТЗ проверено, блокируем кнопку - "Снять заявку переданную на рассмотрение руководителю лаборатории"
                        $('.btn-no-transfer').prop('disabled', true)
                        $('.btn-approve').prop('disabled', true)

                    } else {
                        $('.footer-confirm').text('')
                        $('.btn-no-transfer').prop('disabled', false)
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
                        msg = '4 Requested JSON parse failed.';
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
    })

    /* Вернуть */
    body.on('click', '.btn-no-transfer', function () {
        let tzId = $('.tz-id').val()

        $.ajax({
            method: 'POST',
            url: '/check_methods_and_responsib.php',
            data: {
                id_tz: tzId,
                transfer: 'return',
                btn: 'btn_no_transfer'
            },
            dataType: 'json',
            success: function(dataReturn) {
                if (dataReturn['return']) {
                    $('.footer-confirm').html(`<strong>${dataReturn['return']}</strong>`)
                    $('.btn-no-transfer').prop('disabled', true)
                    $('.btn-approve').prop('disabled', true)
                }
            }
        })
    })

})
