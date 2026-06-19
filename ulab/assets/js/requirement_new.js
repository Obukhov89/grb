let methodList = null
let conditionList = null
let quarryList = null

$.ajax({
    method: 'POST',
    url: '/ulab/requirement/getMethodsAjax',
    dataType: 'json',
    success: function (data) {
        methodList = data
    }
})

$.ajax({
    method: 'POST',
    url: '/ulab/requirement/getTechCondListAjax',
    dataType: 'json',
    success: function (data) {
        conditionList = data
    }
})

$.ajax({
    method: 'POST',
    url: '/ulab/requirement/getQuarryAjax',
    dataType: 'json',
    success: function (data) {
        quarryList = data
    }
})

function formatState(state) {
    const option = $(state.element)
    const color = option.data("color")

    if (!color) {
        return state.text
    }

    return $(`<span style="color: ${color}">${state.text}</span>`)
}

function toggleGroup() {
    let checkedCount = $('.probe-check:checked').length

    if ( checkedCount > 0 ) {
        $('.group-button').removeClass('disabled')
        $('#accordionFlushGroup .collapse').collapse('show')
    } else {
        $('.group-button').addClass('disabled')
        $('#accordionFlushGroup .collapse').collapse('hide')
    }
}


$(function ($) {
    $('.popup-with-form').magnificPopup({
        type: 'inline',
        closeBtnInside:true,
        fixedContentPos: false
    })

    $('#inlineRadio22, #inlineRadio21').change(function () {
        $('#compliance').prop('disabled', !$('#inlineRadio22').prop('checked'))
    })

    $('#inlineRadio31, #inlineRadio32').change(function () {
        $('#another_presented_as').prop('disabled', !$('#inlineRadio32').prop('checked'))
    })

    let $body = $('#workarea-content')

    $body.on('show.bs.collapse', '.collapse', function (e) {
        const target = $(this)
        if ( target.is(e.target) && target.hasClass('empty-data') ) {
            const $materialItem = $(this).parents('.material-item')
            const $probeItem = $(this).parents('.probe-item')
            const delaId = $('#deal_id').val()
            const materialId = $materialItem.data('material_id')
            const numberMaterial = $materialItem.data('number-material')
            const probeId = $probeItem.data('probe_id')

            if ( probeId !== undefined ) { // открыли аккордион с пробой
                const $methodContainer = $probeItem.find('.method-container')

                // $methodContainer.find('.select2').select2({
                //     theme: 'bootstrap-5',
                //     templateResult: formatState,
                //     templateSelection: formatState,
                // })

                $.ajax({
                    method: 'POST',
                    data: {
                        probe_id: probeId,
                        deal_id: delaId
                    },
                    url: '/ulab/requirement/getProbeMethodsAjax',
                    dataType: 'json',
                    success: function (data) {
                        const probeInfo = data[materialId].probe[probeId]
                        const countMethod = $methodContainer.find('.method-block').length

                        $.each(probeInfo.method, function (i, item) {

                            if ( countMethod > 0 && item.ugtp_id === 'new_0' ) {
                                return
                            }

                            if ( item.gost_number === null || item.gost_number < 0 ) {
                                item.gost_number = 0
                            }

                            let gostNumber = parseInt(item.gost_number) + countMethod

                            $methodContainer.append(getHtmlMethod(materialId, probeId, item.ugtp_id, gostNumber, item.id, probeInfo.condition[i], item.assigned_id, item.price))

                            $methodContainer.find('.method-block:last-child').find('.select2').select2({
                                theme: 'bootstrap-5',
                                templateResult: formatState,
                                templateSelection: formatState,
                            })
                        })

                        target.removeClass('empty-data')
                    }
                })
            } else if ( materialId !== undefined ) { // открыли аккордион с материалом
                const $probeBlock = $materialItem.find('.probe-block')
                $materialItem.find('.material-check').prop('disabled', false)

                // $materialItem.find('.change-material.select2').select2({
                //     theme: 'bootstrap-5',
                //     templateResult: formatState,
                //     templateSelection: formatState,
                // })

                $.ajax({
                    method: 'POST',
                    data: {
                        material_id: materialId,
                        deal_id: delaId
                    },
                    url: '/ulab/requirement/getMaterialProbesAjax',
                    dataType: 'json',
                    success: function (data) {

                        let i = 0;
                        $.each(data[materialId].probe, function (id, item) {
                            $probeBlock.append(
                                getHtmlProbe(materialId, numberMaterial, i++, id, '', item.cipher, true, item.name_for_protocol, item.place, item.price, item.quarry_id)
                            )

                            $probeBlock.find('.probe-item:last-child').find('.select2').select2({
                                theme: 'bootstrap-5'
                            })
                        })

                        target.removeClass('empty-data')
                    }
                })
            }
        }
    })

    $('#new-material.select2').select2({
        theme: 'bootstrap-5',
        templateResult: formatState,
        templateSelection: formatState,
    })

    $('#flush-collapse-group').find('select.select2').select2({
        theme: 'bootstrap-5',
        templateResult: formatState,
        templateSelection: formatState,
    })

    $('.change-material').select2({
        theme: 'bootstrap-5',
        templateResult: formatState,
        templateSelection: formatState,
    })

    $body.on('change', '.material-check', function () {
        $(this).parents('.material-item').find('.probe-check').prop("checked", $(this).prop("checked"))
        toggleGroup()
    })

    $body.on('change', '.probe-check', function () {

        let parent = $(this).parents('.material-item'),
            all = true,
            curState = $(this).prop("checked")

        parent.find('.probe-check').each(function () {
            return all = ($(this).prop("checked") === curState)
        })

        if ( all ) {
            parent.find('.material-check').prop({
                indeterminate: false,
                checked: curState
            })
        } else {
            parent.find('.material-check').prop({
                indeterminate: true,
                checked: false
            })
        }
        toggleGroup()
    })

    let countMaterial = $body.find('.material-item').length
    // добавление материала
    $('.add-new-material').click(function () {
        const materialId = $('#new-material').val()
        const materialName = $('#new-material option:selected').text()
        let probeCount = $('#new-count-probe').val()
        let htmlMethod = ''
        let htmlProbe = ''

        if ( materialId == '' ) {
            return false
        }
        if ( !(probeCount >= 1) ) {
            probeCount = 1
        }

        for ( let i = 0; i < probeCount; i++ ) {
            htmlMethod = getHtmlMethod(materialId, 'new_' + i)
            htmlProbe += getHtmlProbe(materialId, countMaterial, i, 'new_' + i, htmlMethod)
        }

        let htmlMaterial = getHtmlMaterial(materialId, countMaterial, materialName, htmlProbe)

        $('.material-block').append(htmlMaterial)

        $body.find('.material-item:last-child .select2').select2({
            theme: 'bootstrap-5',
            templateResult: formatState,
            templateSelection: formatState,
        })

        countMaterial++
    })

    // развернуть материалы
    $body.on('click', '.expand-all-material', function () {
        const methodBlockCount = $('.probe-item').length

        if ( methodBlockCount > 50 ) {
            if ( confirm("Слишком много данных. Большая вероятность, что страница зависнет. Рискнуть и попробовать открыть всё?") ) {
                $('.material-container').find('.collapse').collapse('show')
            }
        } else {
            $('.material-container').find('.collapse').collapse('show')
        }
    })
    // свернуть пробы
    $body.on('click', '.collapse-all-material', function () {
        $('.material-container').find('.collapse').collapse('hide')
    })

    // свернуть пробы
    $body.on('click', '.expand-all', function () {
        // const $block = $(this).parents('div.accordion-body').find('.accordion').find('.collapse.show');
        // $.each($block, function (i, item) {
        //     $(item).collapse('hide')
        // })
        $(this).parents('div.accordion-body').find('.accordion').find('.show').collapse('hide')
    })
    // развернуть пробы
    $body.on('click', '.collapse-all', function () {
        // const $block = $(this).parents('div.accordion-body').find('.accordion').find('.collapse');
        // $.each($block, function (i, item) {
        //     $(item).collapse('show')
        // })
        $(this).parents('div.accordion-body').find('.accordion').find('.collapse').collapse('show')
    })

    // добавить пробу в материал
    $body.on('click', '.add-probe', function () {
        const $materialItem   = $(this).parents('.material-item')
        const materialId      = $materialItem.data('material_id')
        const $probeItem      = $materialItem.find('.probe-item')

        let numberMaterial = $materialItem.data('number-material')

        let num = $probeItem.map(function() {
            return $(this).data('probe_number');
        }).get();

        let countProbe = Math.max.apply(Math, num) + 1;

        $materialItem.find('.probe-block').append(getHtmlProbe(materialId, numberMaterial, countProbe, 'new_' + countProbe, getHtmlMethod(materialId, 'new_' + countProbe)))

        $materialItem.find('.probe-item:last-child').find('.select2').select2({
            theme: 'bootstrap-5',
            templateResult: formatState,
            templateSelection: formatState,
        })
    })

    // добавить испытание к пробе
    $body.on('click', '.add-method-to-probe', function () {
        const $probeItem = $(this).parents('.probe-item')
        const materialId = $(this).parents('.material-item').data('material_id')
        const probeId = $probeItem.data('probe_id')

        let num = $probeItem.find('.method-block').map(function() {
            return $(this).data('gost_number')
        }).get();

        let countMethod = Math.max.apply(Math, num) + 1;

        $probeItem.find('.method-block:last-child').after(getHtmlMethod(materialId, probeId, 'new_' + countMethod, countMethod))
        $probeItem.find('.select2').select2({
            theme: 'bootstrap-5',
            templateResult: formatState,
            templateSelection: formatState,
        })

        updateProbePrice($probeItem)
    })

    // добавление методики группе
    $body.on('click', '.add-group-method', function () {
        const $curBlock = $(this).parents('.method-block')
        const methodId = $curBlock.find('.method-select').val()
        const tuId = $curBlock.find('.tu-select').val()
        const userId = $curBlock.find('.user-select').val()
        const price = $curBlock.find('.price-input').val()

        let $checkProbe = $body.find('.probe-check:checked')

        for (let i = 0; i < $checkProbe.length; i++) {
            const $probeItem = $($checkProbe.get(i)).parents('.probe-item')
            const $methodContainer = $probeItem.find('.method-container')
            const $methodBlock = $methodContainer.find('.method-block')
            const materialId = $($checkProbe.get(i)).parents('.material-item').data('material_id')
            const probeId = $probeItem.data('probe_id')

            let countMethod = 0

            if ( $methodBlock.length === 1 && $methodBlock.find('.method-select').val() == '' ) {
                $methodBlock.remove()
            } else if ( $methodBlock.length > 0 ) {
                let num = $methodBlock.map(function() {
                    return $(this).data('gost_number')
                }).get();

                countMethod = Math.max.apply(Math, num) + 1;
            }

            const htmlMethod = getHtmlMethod(materialId, probeId, 'new_' + countMethod, countMethod, methodId, tuId, userId, price)

            $methodContainer.append(htmlMethod)

            $probeItem.find('.method-block:last-child').find('.select2').select2({
                theme: 'bootstrap-5',
                templateResult: formatState,
                templateSelection: formatState,
            })

            addProbePrice($probeItem, price)
        }

        // alert("Добавлено")
    })

    $body.on('change', '.clear_confirm_change', function () {
        $('#clear_confirm').val('1')
    })

    // скопировать пробу и методики
    $body.on('click', '.copy-probe', function () {
        const $materialItem       = $(this).parents('.material-item')
        const $parentProbeItem    = $(this).parents('.probe-item')
        const $probeItem          = $materialItem.find('.probe-item')
        const $methodBlock        = $parentProbeItem.find('.method-block')
        const materialId          = $materialItem.data('material_id')
        let probePrice = 0

        const numberMaterial = $materialItem.data('number-material')

        let num = $probeItem.map(function() {
            return $(this).data('probe_number')
        }).get();

        let countProbe = Math.max.apply(Math, num) + 1;

        let htmlMethods = ``
        for (let i = 0; i < $methodBlock.length; i++) {
            const $curBlock = $($methodBlock.get(i))

            const methodId = $curBlock.find('.method-select').val()
            const tuId = $curBlock.find('.tu-select').val()
            const userId = $curBlock.find('.user-select').val()
            const price = parseFloat($curBlock.find('.price-input').val())
            probePrice += price

            htmlMethods += getHtmlMethod(materialId, `new_${countProbe}`, 'new_' + i, i, methodId, tuId, userId, price)
        }

        $materialItem.find('.probe-item:last-child').after(
            getHtmlProbe(materialId, numberMaterial, countProbe, 'new_' + countProbe, htmlMethods, '', false, '', '', probePrice)
        )

        $materialItem.find('.probe-item:last-child').find('.select2').select2({
            theme: 'bootstrap-5',
            templateResult: formatState,
            templateSelection: formatState,
        })

        updateMaterialPrice($materialItem)
    })

    // удалить добавленый скриптом материал
    $body.on('click', '.delete-new-material', function () {
        if ( confirm("Удалить объект испытаний и все пробы?") ) {
            $(this).parents('.material-item').remove()

            updateTotalPrice()
        }
    })

    // удалить добавленную скриптом пробу
    $body.on('click', '.delete-new-probe', function () {
        if ( confirm("Удалить пробу?") ) {
            const $probeBlock = $(this).parents('.probe-block')
            const $probeItem = $(this).parents('.probe-item')
            const $materialItem = $probeItem.parents('.material-item')
            let countProbe = $probeBlock.find('.probe-item').length

            if ( countProbe == 1 ) {
                alert("Невозможно удалить единственную пробу объекта испытаний.")
                return false
            }

            $probeItem.remove()

            updateMaterialPrice($materialItem)
        }
    })

    // удалить добавленый скриптом метод
    $body.on('click', '.del-new-method', function () {
        if ( confirm("Удалить испытание?") ) {
            const methodBlock = $(this).parents('.method-block')
            const methodContainer = methodBlock.parents('.method-container')
            const probeItem = methodBlock.parents('.probe-item')
            const probeId = probeItem.data('probe_id')
            const materialId = $(this).parents('.material-item').data('material_id')

            methodBlock.remove()

            if (methodContainer.find('.method-block').length == 0) {
                methodContainer.append(getHtmlMethod(materialId, probeId))

                methodContainer.find('.select2').select2({
                    theme: 'bootstrap-5',
                    templateResult: formatState,
                    templateSelection: formatState,
                })
            }

            updateProbePrice(probeItem)
        }
    })

    // удалить из базы испытание у пробы
    $body.on('click', '.del-permanent-material-gost', function () {
        if ( confirm("Удалить испытание (необратимо)?") ) {
            const methodBlock = $(this).parents('.method-block')
            const methodContainer = methodBlock.parents('.method-container')
            const probeItem = methodBlock.parents('.probe-item')
            const probeId = probeItem.data('probe_id')
            const materialId = $(this).parents('.material-item').data('material_id')

            const ugtpId = $(this).data('gtp_id')
            const tzId = $('#tz_id').val()

            $.ajax({
                method: 'POST',
                url: '/ulab/requirement/deleteProbeMethodAjax',
                data: {
                    id: ugtpId,
                    tz_id: tzId
                },
                dataType: 'json',
                success: function (data) {
                    if ( data.success ) {
                        methodBlock.remove()

                        if (methodContainer.find('.method-block').length == 0) {
                            methodContainer.append(getHtmlMethod(materialId, probeId))

                            methodContainer.find('.select2').select2({
                                theme: 'bootstrap-5',
                                templateResult: formatState,
                                templateSelection: formatState,
                            })
                        }

                        updateProbePrice(probeItem)
                    } else {
                        alert(data.error)
                    }
                }
            })
        }
    })


    // удалить из базы пробу и методики
    $body.on('click', '.del-permanent-probe', function () {
        if ( confirm("Удалить пробу с испытаниями (необратимо)?") ) {
            const $probeBlock = $(this).parents('.probe-block')
            const $probeItem = $(this).parents('.probe-item')
            const $materialItem = $(this).parents('.material-item')
            const probeId = $probeItem.data('probe_id')
            const delaId = $('#deal_id').val()
            let countProbe = $probeBlock.find('.probe-item').length

            if ( countProbe == 1 ) {
                alert("Невозможно удалить единственную пробу объекта испытаний.")
                return false
            }

            $.ajax({
                method: 'POST',
                url: '/ulab/requirement/deleteProbeAjax',
                data: {
                    id: probeId,
                    deal_id: delaId,
                },
                dataType: 'json',
                success: function (data) {
                    if ( data.success ) {
                        $probeItem.remove()

                        updateMaterialPrice($materialItem)
                    } else {
                        alert(data.error)
                    }
                }
            })
        }
    })

    // удалить материал
    $body.on('click', '.delete-material', function () {
        if ( confirm("Удалить объект испытаний и все пробы (необратимо)?") ) {

            const $materialItem = $(this).parents('.material-item')
            const materialId = $materialItem.data('material_id')
            const delaId = $('#deal_id').val()

            $.ajax({
                method: 'POST',
                url: '/ulab/requirement/deleteMaterial',
                data: {
                    id: materialId,
                    deal_id: delaId,
                },
                dataType: 'json',
                success: function (data) {
                    if ( data.success ) {
                        $materialItem.remove()

                        updateTotalPrice()
                    } else {
                        alert(data.error)
                    }
                }
            })
        }
    })


    $body.on('change', '.change-material', function () {
        const $parentBlock = $(this).parents('.material-item')
        const textMaterial = $(this).find('option:selected').text()

        $parentBlock.find('.msg-change-material').text(' (после сохранения объект испытаний сменится на: ' + textMaterial + ')')
    })


    $body.on('change', '.method-select', function () {
        const id = $(this).val()
        const $parent = $(this).parents('.method-block')

        $.ajax({
            method: 'POST',
            async: false,
            data: {
                id: id
            },
            url: '/ulab/requirement/getMethodDataAjax',
            dataType: 'json',
            success: function (data) {
                $parent.find('.price-input').val(parseFloat(data.price)).trigger('input')

                let options = `<option value="">Исполнитель</option>`
                $.each(data.assigned, function (i, item) {
                    options += `<option value="${item.user_id}">${item.short_name}</option>`
                })

                $parent.find('.user-select').html(options)
            }
        })

        $parent.find('.method-link')
            .removeClass('disabled')
            .attr('href', `/ulab/gost/method/${id}`)

    })
    $body.on('change', '.tu-select', function () {
        const id = $(this).val()
        $(this).parents('.method-block').find('.tu-link')
            .removeClass('disabled')
            .attr('href', `/ulab/techCondition/edit/${id}`)
    })

    $body.on('input', '.probe-item .price-input', function () {
        updateProbePrice($(this).parents('.probe-item'))
    })

    // применить скидку
    $body.on('click', '.discount-apply', function () {
        updateTotalPrice()
    })

    // отправить тз на проверку
    $body.on('click', '.sent_tz', function () {
        const delaId = $('#deal_id').val()
        const $block = $('.head-user-block')
        const $btn = $(this)

        $btn.find('i').removeClass().addClass('fa-solid fa-arrows-rotate spinner-animation')
        $btn.addClass('disable')

        $.ajax({
            method: 'POST',
            data: {
                deal_id: delaId
            },
            url: '/ulab/requirement/confirmTzSentAjax',
            dataType: 'json',
        }).always( function (data) {
            $block.find('i').removeClass().addClass('fa-solid fa-hourglass-half').attr('title', 'Ожидание проверки')
            $btn.html(`<i class="fa-regular fa-paper-plane"></i> Передано`)
        })
    })

    // одобрить тз
    $body.on('click', '.approve_tz', function () {
        const tzId = $('#tz_id').val()
        const $block = $('.curr_user')
        const $btn = $(this)

        $btn.find('i').removeClass().addClass('fa-solid fa-arrows-rotate spinner-animation')
        $btn.addClass('disable')
        $('.not_approve_tz').addClass('disable')

        $.ajax({
            method: 'POST',
            data: {
                tz_id: tzId
            },
            url: '/ulab/requirement/confirmTzApproveAjax',
            dataType: 'json',
        }).always( function (data) {
            $block.find('.icon').addClass('text-green').find('i').removeClass().addClass('fa-regular fa-circle-check').attr('title', 'ТЗ потверждено')
            $btn.find('i').removeClass().addClass('fa-regular fa-circle-check')
        })
    })

    // отправить и одобрить тз
    $body.on('click', '.sent_approve_tz', function () {
        const tzId = $('#tz_id').val()
        const delaId = $('#deal_id').val()
        const $headBlock = $('.head-user-block')
        const $block = $('.curr_user')
        const $btn = $(this)

        $btn.find('i').removeClass().addClass('fa-solid fa-arrows-rotate spinner-animation')
        $btn.addClass('disable')
        $('.not_approve_tz').addClass('disable')

        $.ajax({
            method: 'POST',
            data: {
                tz_id: tzId,
                deal_id: delaId,
            },
            url: '/ulab/requirement/confirmTzSentApproveAjax',
            dataType: 'json',
        }).always( function (data) {
            $headBlock.find('i').removeClass().addClass('fa-solid fa-hourglass-half').attr('title', 'Ожидание проверки')
            $block.find('.icon').addClass('text-green').find('i').removeClass().addClass('fa-regular fa-circle-check').attr('title', 'ТЗ потверждено')
            $btn.find('i').removeClass().addClass('fa-regular fa-circle-check')
        })
    })

    // не одобрить тз
    $('body').on('click', '.not_approve_tz_btn', function () {
        const tzId = $('#tz_id').val()
        const $block = $('.curr_user')
        const $btn = $(this)
        const desc = $('#desc_return').val()

        $btn.find('i').removeClass().addClass('fa-solid fa-arrows-rotate spinner-animation')
        $('.not_approve_tz').addClass('disable')
        $('.approve_tz').addClass('disable')

        $.ajax({
            method: 'POST',
            data: {
                tz_id: tzId,
                desc: desc
            },
            url: '/ulab/requirement/confirmTzNotApproveAjax',
            dataType: 'json',
        }).always( function (data) {
            $block.find('.icon').addClass('text-red').find('i').removeClass().addClass('fa-regular fa-circle-xmark')
            $btn.find('i').removeClass().addClass('fa-regular fa-circle-xmark')
            $.magnificPopup.close()
            location.reload()
        })
    })


    function updateProbePrice($probeItem) {
        const $priceInput = $probeItem.find('.price-input')

        let totalPrice = 0

        for (let i = 0; i < $priceInput.length; i++) {
            let val = parseFloat($($priceInput.get(i)).val())

            if ( val > 0 ) {
                totalPrice += val
            }
        }

        $probeItem.data('price', totalPrice)

        updateMaterialPrice($probeItem.parents('.material-item'))
    }


    function addProbePrice($probeItem, price) {
        $probeItem.data('price', parseFloat($probeItem.data('price')) + parseFloat(price))

        updateMaterialPrice($probeItem.parents('.material-item'))
    }


    function updateMaterialPrice($materialItem) {
        const $probeItem = $materialItem.find('.probe-item')
        let totalPrice = 0

        for (let i = 0; i < $probeItem.length; i++) {
            let val = parseFloat($($probeItem.get(i)).data('price'))
            if ( val > 0 ) {
                totalPrice += val
            }
        }

        $materialItem.data('price', totalPrice)

        updateTotalPrice()
    }


    // обновляет цену
    function updateTotalPrice() {
        // const $priceInput = $body.find('.probe-item .price-input')
        const $priceInput = $body.find('.material-item')
        let discountVal = parseFloat($('.discount-input').val())
        let discountType = $('.discount-type').val()

        let totalPrice = 0
        let discountPrice = 0

        for (let i = 0; i < $priceInput.length; i++) {
            // let val = parseFloat($($priceInput.get(i)).val())
            let val = parseFloat($($priceInput.get(i)).data('price'))
            if ( val > 0 ) {
                totalPrice += val
            }
        }

        if ( discountType == 'percent' ) {
            if ( discountVal < 0 ) {
                discountVal = 0
            } else if ( discountVal > 100 ) {
                discountVal = 100
            }

            discountPrice = totalPrice - totalPrice * discountVal / 100
        } else if ( discountType == 'rub' && discountVal >= 0 ) {
            discountPrice = totalPrice - discountVal
        }

        if (discountPrice < 0) {
            discountPrice = 0
        }

        $('#price-total').val(totalPrice.toFixed(2))
        $('#price_discount').val(discountPrice.toFixed(2))
        $('.total').text(discountPrice.toFixed(2) + ' руб.')

        $('#clear_confirm').val('1')

        window.onbeforeunload = function() {
            return "Данные не сохранены. Точно перейти?";
        }
    }

    $('.form-requirement').submit(function () {
        window.onbeforeunload = null
    })
})

function getHtmlMaterial(materialId, countMaterial, materialName, htmlProbe) {
    return `<div class="accordion-item material-item" data-number-material="${countMaterial}" data-material_id="${materialId}" data-price="0">
                <h2 class="accordion-header" id="flush-heading${countMaterial}">
                    <div class="accordion-button ps-0 collapsed" data-bs-toggle="collapse" data-bs-target="#flush-collapse${countMaterial}" aria-expanded="false" aria-controls="flush-collapse${countMaterial}">
                        <input class="form-check-input ms-3 me-3 material-check" type="checkbox" data-bs-toggle="collapse" data-bs-target="#qq">
                        ${materialName}
                    </div>
                </h2>
                <div id="flush-collapse${countMaterial}" class="accordion-collapse collapse" aria-labelledby="flush-heading${countMaterial}" >
                    <div class="accordion-body">
                    
                        <div class="row justify-content-end mb-3">
                            <div class="col-auto">
                                <button type="button" class="btn btn-success btn-square add-probe" title="Добавить пробу">
                                    <i class="fa-solid fa-plus icon-fix"></i>
                                </button>
                            </div>
    
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary btn-square expand-all" title="Свернуть все пробы">
                                    <i class="fa-solid fa-angles-up"></i>
                                </button>
                            </div>
    
                            <div class="col-auto">
                                <button type="button" class="btn btn-primary btn-square collapse-all" title="Развернуть все пробы">
                                    <i class="fa-solid fa-angles-down"></i>
                                </button>
                            </div>
    
                            <div class="col-auto">
                                <button type="button" class="btn btn-danger btn-square delete-new-material" title="Удалить объект испытаний и все пробы">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="line-dashed"></div>
    
                        <div class="accordion probe-block" id="accordionPanelsStayOpen${countMaterial}">
                            ${htmlProbe}
                        </div>
                    </div>
                </div>
            </div>`
}

function getHtmlProbe(materialId, countMaterial, countProbe, probeId, htmlMethod, cipherProbe = '', isGetAjax = false, nameForProtocol = '', place = '', price = 0, defaultQuarry = '') {
    const cipher = cipherProbe === '' ? `Не присвоен шифр #${countProbe+1}` : cipherProbe
    const markClass = isGetAjax ? 'empty-data' : ''
    const deleteClass = isGetAjax ? 'del-permanent-probe' : 'delete-new-probe'
    let optionQuarry = ``

    $.each(quarryList, function (i, item) {
        let selected = ``

        if ( item.ID == defaultQuarry ) {
            selected = 'selected'
        }

        // optionQuarry += `<option value="${item.id}" ${selected}>${item.name}</option>`
        optionQuarry += `<option value="${item.ID}" ${selected}>${item.NAME}</option>`
    })

    return `<div class="accordion-item probe-item" data-probe_number="${countProbe}" data-probe_id="${probeId}" data-price="${price}">
                <h2 class="accordion-header" id="panelsStayOpen-heading${countMaterial}-${countProbe}">
                    <div class="accordion-button ps-0 collapsed bg-pele-green" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse${countMaterial}-${countProbe}" aria-expanded="false" aria-controls="panelsStayOpen-collapse${countMaterial}-${countProbe}">
                        <input class="form-check-input ms-3 me-3 probe-check" type="checkbox" data-bs-toggle="collapse" data-bs-target="#qq">
                        ${cipher}
                    </div>
                </h2>
                <div id="panelsStayOpen-collapse${countMaterial}-${countProbe}" class="accordion-collapse collapse ${markClass}" aria-labelledby="panelsStayOpen-heading${countMaterial}-${countProbe}">
                    <div class="accordion-body method-block-block">
                    
                        <div class="row justify-content-end mb-3">
                            <div class="col-auto">
                                <button type="button" class="btn btn-success btn-square add-method-to-probe" title="Добавить испытание">
                                    <i class="fa-solid fa-plus icon-fix"></i>
                                </button>
                            </div>
                        
                            <div class="col-auto">
                                <button type="button" class="btn btn-success btn-square copy-probe" title="Скопировать пробу и все методикы">
                                    <i class="fa-regular fa-copy icon-fix"></i>
                                </button>
                            </div>
                        
                            <div class="col-auto">
                                <button type="button" class="btn btn-danger btn-square ${deleteClass}" title="Удалить пробу и методики">
                                    <i class="fa-solid fa-minus icon-fix"></i>
                                </button>
                            </div>
                        </div>
                        
                        <div class="line-dashed"></div>
                        
                        <div class="row mb-3">

                            <input type="hidden" name="material[${materialId}][probe][${probeId}][probe_number]" value="${countProbe}" class="probe-number-input">
                            <input type="hidden" name="material[${materialId}][probe][${probeId}][material_number]" value="${countMaterial}" class="material-number-input">

                            <div class="col">
                                <label class="form-label mb-1">Группа объекта испытаний</label>
                                <select class="form-control select2" name="material[${materialId}][probe][${probeId}][group]">
                                    <option value="">Без группы</option>
                                </select>
                            </div>

                            <div class="col">
                                <label class="form-label mb-1">Маркировка заказчика (информация об объекте испытания)</label>
                                <input type="text" name="material[${materialId}][probe][${probeId}][name_for_protocol]" class="form-control" value="${nameForProtocol}">
                            </div>

                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6 d-none">
                                <label class="form-label mb-1">Место отбора</label>
                                <!--input type="text" name="material[${materialId}][probe][${probeId}][place]" class="form-control" value="${place}"-->
                            </div>
                            
                            <div class="col-6">
                                <label class="form-label mb-1">Карьер</label>
                                <div class="input-group">
                                    <select class="form-control select2" name="material[${materialId}][probe][${probeId}][quarry_id]">
                                        <option value="">Нет карьера</option>
                                        ${optionQuarry}
                                    </select>
                                </div>
                            </div>

                            <div class="col d-none">
                                <label class="form-label mb-1">Схема</label>
                                <div class="input-group">
                                    <select class="form-control select2" id="">
                                        <option value="1">Нет схемы / ручной ввод</option>
                                    </select>
                                    <button type="button" class="btn btn-primary">Применить</button>
                                </div>
                            </div>
                        </div>

                        <div class="line-dashed"></div>
                        
                        <div class="row">
                            <div class="col-4">
                                <label class="form-label mb-1">Методика испытаний</label>
                            </div>
                            <div class="col-4">
                                <label class="form-label mb-1">Нормативная документация</label>
                            </div>
                            <div class="col-2">
                                <label class="form-label mb-1">Исполнитель</label>
                            </div>
                            <div class="col">
                                <label class="form-label mb-1">Цена</label>
                            </div>
                        </div>
                        
                        <div class="method-container">
                            ${htmlMethod}
                        </div>
                    </div>
                </div>
            </div>`
}

function getHtmlMethod(materialId = 'new', probeId = 'new', methodId = 'new', gostNumber = 0, defaultMethod = 0, defaultTu = 0, defaultUser = 0, defaultPrice = 0) {
    let optionMethod = ``
    let optionCondition = ``
    let optionAssigned = ``

    let disabledMethod = 'disabled'
    let disabledTu = 'disabled'

    let delBtnClass = ''

    $.each(methodList, function (i, item) {
        let dataColor = ''
        let selected = ``

        if ( item.is_confirm == 0 ) {
            dataColor = `data-color="#dfdf11"`
        }

        if ( item.is_actual == 0 ) {
            dataColor = `data-color="#F00"`
        }

        if ( item.id == defaultMethod ) {
            disabledMethod = ''
            selected = 'selected'

            $.ajax({
                method: 'POST',
                async: false,
                data: {
                    id: defaultMethod
                },
                url: '/ulab/requirement/getMethodDataAjax',
                dataType: 'json',
                success: function (data) {
                    $.each(data.assigned, function (i, item) {
                        let selectedUser = ''

                        if ( item.user_id == defaultUser ) {
                            selectedUser = 'selected'
                        }

                        optionAssigned += `<option value="${item.user_id}" ${selectedUser}>${item.short_name}</option>`
                    })
                }
            })
        }

        optionMethod += `<option value="${item.id}" ${dataColor} ${selected}>${item.view_gost}</option>`
    })

    $.each(conditionList, function (i, item) {
        let selected = ``

        if ( item.id == defaultTu ) {
            disabledTu = ''
            selected = 'selected'
        }

        optionCondition += `<option value="${item.id}" ${selected}>${item.view_name}</option>`
    })

    if ( isNaN(Number(methodId)) ) { // new
        delBtnClass = 'del-new-method'
    } else {
        delBtnClass = 'del-permanent-material-gost clear_confirm_change'
    }

    return `<div class="row justify-content-between method-block mb-2" data-gost_number="${gostNumber}">
                <input type="hidden" class="gost-number-input" name="material[${materialId}][probe][${probeId}][method][${methodId}][gost_number]" value="${gostNumber}">
                <div class="col-4">
                    <div class="input-group">
                        <select class="form-control select2 method-select" name="material[${materialId}][probe][${probeId}][method][${methodId}][new_method_id]">
                            <option value=""></option>
                            ${optionMethod}
                        </select>
                        <a class="btn btn-outline-secondary method-link ${disabledMethod}" target="_blank" title="Перейти в методику" href="/ulab/gost/method/${defaultMethod}">
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </a>
                    </div>
                </div>
                <div class="col-4">
                    <div class="input-group">
                        <select class="form-control select2 tu-select" name="material[${materialId}][probe][${probeId}][method][${methodId}][tech_condition_id]">
                            <option value="">--</option>
                            ${optionCondition}
                        </select>
                        <a class="btn btn-outline-secondary tu-link ${disabledTu}" target="_blank" title="Перейти в ТУ" href="/ulab/techCondition/edit/${defaultTu}">
                            <i class="fa-solid fa-right-to-bracket"></i>
                        </a>
                    </div>
                </div>
                <div class="col-2">
                    <select class="form-control user-select" name="material[${materialId}][probe][${probeId}][method][${methodId}][assigned_id]">
                        <option value="">Исполнитель</option>
                        ${optionAssigned}
                    </select>
                </div>
                <div class="col">
                    <div class="input-group">
                        <input class="form-control price-input" name="material[${materialId}][probe][${probeId}][method][${methodId}][price]" type="number" min="0" step="0.01" value="${defaultPrice}">
                        <span class="input-group-text">₽</span>
                    </div>
                </div>
                <div class="col-auto">
                    <button
                            class="btn btn-danger mt-0 ${delBtnClass} btn-square float-end"
                            data-gtp_id="${methodId}"
                            type="button"
                    >
                        <i class="fa-solid fa-minus icon-fix"></i>
                    </button>
                </div>
            </div>`
}
