// Морозостойкость ГОСТ 10060
$(function ($) {
    const body = $('body');

    const COEFFICIENT0_9 = 0.9;
    const COEFFICIENT_ALPHA = {
        2: 1.13,
        3: 1.69,
        4: 2.06,
        5: 2.33,
        6: 2.5
    };
    const COEFFICIENT_CRITERION = {
        3: 4.30,
        4: 3.182,
        5: 2.776,
        6: 2.570
    };

    /** Сообщение об ошибки */
    function getMessageErrorContent(messageError = "") {
        if (!messageError) {
            return false;
        }

        return `<div class="messages">
              <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                  <div>
                      ${messageError}
                  </div>
                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
          </div>`;
    }

    /** Сбросить значения */
    function resetValue(items) {
        items.each(function (index, item) {
            $(this).val("");
        });
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

    /** Получить числовой массив данных элементов */
    function getElementsDataArrayNumeric(fieldsSelector) {
        return $(fieldsSelector).map(function (index, value) {
            const val = $(value).val();
            if (val !== null && val !== '') {
                // Заменяем запятую на точку и преобразуем в число
                const numericValue = parseFloat(val.replace(',', '.'));
                return isNaN(numericValue) ? 0 : numericValue;
            }
        }).get()
    }

    /** Расчёт потери массы, % */
    function getMassLoss(massBefore, massAfter) {
        return (massBefore-massAfter)*100/massBefore;
    }

    /** Проверить на заполненость полей */
    function checkEmptyFields(fieldsSelector, errorWrapperSelector, resetSelector) {
        const wrapper = $(errorWrapperSelector),
            resetElement = $(resetSelector);

        let valueEmpty = $(fieldsSelector).filter(function () {
            return $(this).val() === null || $(this).val() === '';
        })

        wrapper.find(".messages").remove();

        if (valueEmpty.length) {
            let messageError =
                "Внимание! Не все поля заполнены!";

            let messageErrorContent = getMessageErrorContent(messageError)

            wrapper.prepend(messageErrorContent);
            resetValue(resetElement);
            return false;
        }

        return true;
    }

    /** Выбор марки морозостойкости */
    body.on('change', '#mark', function (e) {
        const option = $(this).find('option:selected'),
            frostWrapper =  $('#frostWrapper'),
            intermediateWrapper =  frostWrapper.find('.intermediate-wrapper'),
            controlWrapper =  frostWrapper.find('.control-wrapper'),
            cycleIntermediate =  frostWrapper.find('#cycleIntermediate'),
            basicWrapper =  frostWrapper.find('.basic-wrapper'),
            cycleControl =  frostWrapper.find('#cycleControl'),
            averageMassLoss1 =  frostWrapper.find('#averageMassLoss1'),
            massLoss1 =  frostWrapper.find('.mass-loss1'),
            mainMass1 =  frostWrapper.find('.main-mass1');

        let intermediate = option.data('intermediate'),
            control = option.data('control'),
            mark = option.val();

        // очистка рассчитанных данных
        const inputReadonly = $("#frostWrapper input[readonly]:not('.cycle')");
        resetValue(inputReadonly);

        $('.ratio').prop('selectedIndex', 0);
        if ( !$('.ratio1').hasClass('border-danger') ) {
            $('.ratio1').addClass('border-danger');
        }
        if ( !$('.ratio2').hasClass('border-danger') ) {
            $('.ratio2').addClass('border-danger');
        }

        // разблокировка кнопок
        frostWrapper.find('.calculate').prop('disabled', false);
        frostWrapper.find('.save').prop('disabled', false);

        // отображение блоков рассчёта промежуточных и контрольных проверок
        if (mark === '' || mark === null) {
            if ( !controlWrapper.hasClass('d-none') ) {
                controlWrapper.addClass('d-none')
            }
            if ( !intermediateWrapper.hasClass('d-none') ) {
                intermediateWrapper.addClass('d-none')
            }
            if ( !basicWrapper.hasClass('d-none') ) {
                basicWrapper.addClass('d-none')
            }
        } else if (intermediate !== '') {
            cycleIntermediate.val(intermediate);
            cycleControl.val(control);

            controlWrapper.removeClass('d-none');
            intermediateWrapper.removeClass('d-none');
            basicWrapper.removeClass('d-none');

            $('.ratio1').prop('disabled', false);
        } else {
            cycleControl.val(control);

            controlWrapper.removeClass('d-none');
            basicWrapper.removeClass('d-none');

            if ( !intermediateWrapper.hasClass('d-none') ) {
                intermediateWrapper.addClass('d-none')
            }

            $('.ratio1').prop('disabled', true);
        }
    });

    /** Расчитать */
    body.on('click', '.calculate', function (e) {
        e.preventDefault();
        let num = $(this).data('number');

        const frostWrapper =  $('#frostWrapper'),
            selectRatio = $(`select.ratio${num}`);

        // Потеря массы, %
        let arrMassBefore = getElementsDataArrayNumeric(`#frostWrapper .mass-before${num}`);
        let arrMassAfter = getElementsDataArrayNumeric(`#frostWrapper .mass-after${num}`);

        let checkMassBefore =
            checkEmptyFields(`.head-wrapper-${num} .mass-before${num}`, `.tests-wrapper-${num}`, `.head-wrapper-${num} input[readonly]`);
        if (!checkMassBefore) {
            return false;
        }

        let checkAfterBefore =
            checkEmptyFields(`.head-wrapper-${num} .mass-after${num}`, `.tests-wrapper-${num}`, `.head-wrapper-${num} input[readonly]`);
        if (!checkAfterBefore) {
            return false;
        }


        let arrMassLoss = [];
        for (const key in arrMassBefore) {
            let massBefore = arrMassBefore[key],
                massAfter = arrMassAfter[key];

            let massLoss = getMassLoss(massBefore, massAfter);
            arrMassLoss.push(round(massLoss, 2));

            frostWrapper.find(`.mass-loss${num}`).eq(key).val(round(massLoss, 2).toFixed(2));
        }


        // Среднее значение потери массы, % (Среднее уменьшение массы образцов)
        let averageMassLoss = average(arrMassLoss);

        frostWrapper.find(`#averageMassLoss${num}`).val(round(averageMassLoss, 2).toFixed(2));
        if (num === 2 && $('.main_damage2').val() !== 'Отсутствуют') {
            frostWrapper.find(`.main-mass${num}`).val('-');
        } else {
            frostWrapper.find(`.main-mass${num}`).val(round(averageMassLoss, 2).toFixed(2));
        }


        // Средняя прочность при сжатии
        let controlStrength = getElementsDataArrayNumeric(`#frostWrapper .control_strength${num}`);

        let mainStrength = getElementsDataArrayNumeric(`#frostWrapper .main_strength${num}`);
        if (num === 2 && $('.main_damage2').val() !== 'Отсутствуют') {
            frostWrapper.find(`.main_strength${num}`).val('-');
        }

        let controlMedium = average(controlStrength);
        let mainMedium = average(mainStrength);


        let controlMediumRound = round(controlMedium, 1);
        let mainMediumRound = round(mainMedium, 1);

        frostWrapper.find(`.control_medium${num}`).val(controlMediumRound.toFixed(1));
        if (isNaN(mainMediumRound) || !isFinite(mainMediumRound) || $('.main_damage2').val() !== 'Отсутствуют') {
            frostWrapper.find(`.main_medium${num}`).val('-');
        } else {
            frostWrapper.find(`.main_medium${num}`).val(mainMediumRound.toFixed(1));
        }


        // Наиб. разность (Разность max-min), Wm, МПа
        let controlDifference = Math.max( ...controlStrength ) - Math.min( ...controlStrength );
        let mainDifference = Math.max( ...mainStrength ) - Math.min( ...mainStrength );

        let controlDifferenceRound = round(controlDifference, 1);
        let mainDifferenceRound = round(mainDifference, 1);

        frostWrapper.find(`.control_difference${num}`).val(controlDifferenceRound.toFixed(1));
        if (isNaN(mainDifferenceRound) || !isFinite(mainDifferenceRound)) {
            frostWrapper.find(`.main_difference${num}`).val('-');
        } else {
            frostWrapper.find(`.main_difference${num}`).val(mainDifferenceRound.toFixed(1));
        }


        // Среднеквадр откл. σ=наиб.разн/Коэффиц
        let coefficientAlpha = COEFFICIENT_ALPHA[arrMassBefore.length];
        let controlRmsDeviation = controlDifferenceRound / coefficientAlpha;
        let mainRmsDeviation = mainDifferenceRound / coefficientAlpha;
        // let controlRmsDeviation = controlDifference / coefficientAlpha;
        // let mainRmsDeviation = mainDifference / coefficientAlpha;

        let controlRmsDeviationRound = round(controlRmsDeviation, 2);
        let mainRmsDeviationRound = round(mainRmsDeviation, 2);

        frostWrapper.find(`.control_rms${num}`).val(controlRmsDeviationRound.toFixed(2));
        if (isNaN(mainRmsDeviationRound) || !isFinite(mainRmsDeviationRound)) {
            frostWrapper.find(`.main_rms${num}`).val('-');
        } else {
            frostWrapper.find(`.main_rms${num}`).val(mainRmsDeviationRound.toFixed(2));
        }


        // КОЭФ.ВАР. V= σ /Rср
        let controlVariation = controlRmsDeviationRound / controlMediumRound;
        let mainVariation = mainRmsDeviationRound / mainMediumRound;
        // let controlVariation = controlRmsDeviation / controlMedium;
        // let mainVariation = mainRmsDeviation / mainMedium;
        frostWrapper.find(`.control_variation${num}`).val(round(controlVariation, 3).toFixed(3));
        if (isNaN(mainVariation) || !isFinite(mainVariation)) {
            frostWrapper.find(`.main_variation${num}`).val('-');
        } else {
            frostWrapper.find(`.main_variation${num}`).val(round(mainVariation, 3).toFixed(3));
        }

        // Нижняя граница доверительного интервала Xmin1=Хср-2,57*σ, МПа
        let coefficientCriterion = COEFFICIENT_CRITERION[arrMassBefore.length];
        let controlBottomLine = controlMediumRound - (coefficientCriterion * controlRmsDeviationRound);
        let mainBottomLine = mainMediumRound - (coefficientCriterion * mainRmsDeviationRound);
        // let controlBottomLine = controlMedium - (coefficientCriterion * controlRmsDeviation);
        // let mainBottomLine = mainMedium - (coefficientCriterion * mainRmsDeviation);

        let controlBottomLineRound = round(controlBottomLine, 2);
        let mainBottomLineRound = round(mainBottomLine, 2);

        frostWrapper.find(`.control_bottom_line${num}`).val(controlBottomLineRound.toFixed(2));
        if (isNaN(mainBottomLineRound) || !isFinite(mainBottomLineRound)) {
            frostWrapper.find(`.main_bottom_line${num}`).val('-');
        } else {
            frostWrapper.find(`.main_bottom_line${num}`).val(mainBottomLineRound.toFixed(2));
        }


        // 0,9Xmin1, МПа
        let xMin_0_9 = controlBottomLineRound * COEFFICIENT0_9;
        // let xMin_0_9 = controlBottomLine * COEFFICIENT0_9;
        frostWrapper.find(`.x_min${num}_0_9`).val(round(xMin_0_9, 2).toFixed(2));


        // выполняется/не выполняется
        let ratio = mainBottomLineRound !== Infinity && mainBottomLineRound >= xMin_0_9 ? 1 : 0;
        // let ratio = mainBottomLine !== Infinity && mainBottomLine >= xMin_0_9 ? 1 : 0;
        frostWrapper.find(`.ratio${num} option[value="${ratio}"]`).prop('selected', true);

        if (ratio) {
            selectRatio.removeClass('border-danger');
            if ( !selectRatio.hasClass('border-secondary') ) {
                selectRatio.addClass('border-secondary');
            }
        } else {
            selectRatio.removeClass('border-secondary');
            if ( !selectRatio.hasClass('border-danger') ) {
                selectRatio.addClass('border-danger');
            }
        }
    });

    /** Блокировать кнопку при сохранении */
    $('#measurementModalForm').on('submit', function() {
        const frostWrapper =  $('#frostWrapper');

        frostWrapper.find('.calculate').prop('disabled', true);
        $(this).find('.save').replaceWith(
            `<button class="btn btn-primary" type="button" disabled>
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Сохранение...
            </button>`
        );
    });

    /** Удалить */
    body.on('click', '.delete-intermediate', function (e) {
        $(this).closest('.added-intermediate').remove();

        const inputReadonly = $(".intermediate-wrapper input[readonly]:not('#cycleIntermediate')");
        resetValue(inputReadonly);

        $('.ratio').prop('selectedIndex', 0);
        if ( !$('.ratio1').hasClass('border-danger') ) {
            $('.ratio1').addClass('border-danger');
        }
        if ( !$('.ratio2').hasClass('border-danger') ) {
            $('.ratio2').addClass('border-danger');
        }
    });

    body.on('click', '.delete-control', function (e) {
        $(this).closest('.added-control').remove();

        const inputReadonly = $(".basic-wrapper input[readonly]:not('#cycleControl')");
        resetValue(inputReadonly);

        $('.ratio2').prop('selectedIndex', 0);
        if ( !$('.ratio2').hasClass('border-danger') ) {
            $('.ratio2').addClass('border-danger');
        }
    });

    /** Добавить */
    body.on('click', '.add-intermediate', function () {
        const tbody = $(this).closest('tbody');

        let countAddedIntermediate = tbody.find('.added-intermediate').length;

        if (countAddedIntermediate >= 4) {
            return false;
        }

        let tr = $(this).closest('tr'),
            cloneTr = $(tr).clone(true)

        cloneTr.find('.add-intermediate').replaceWith(
            `<button class="btn btn-danger mt-0 delete-intermediate btn-square" type="button">
                <i class="fa-solid fa-minus icon-fix"></i>
            </button>`
        );

        cloneTr.addClass('added-intermediate');
        cloneTr.find('input').val('');

        tbody.append(cloneTr);
    });

    body.on('click', '.add-control', function () {
        const tbody = $(this).closest('tbody');

        let countAddedControl = tbody.find('.added-control').length;

        if (countAddedControl >= 4) {
            return false;
        }

        let tr = $(this).closest('tr'),
            cloneTr = $(tr).clone(true)

        cloneTr.find('.add-control').replaceWith(
            `<button class="btn btn-danger mt-0 delete-control btn-square" type="button">
                <i class="fa-solid fa-minus icon-fix"></i>
            </button>`
        );

        cloneTr.addClass('added-control');
        cloneTr.find('input').val('');

        tbody.append(cloneTr);
    });

    /** Изменение промежуточных данных */
    body.on("input", ".head-wrapper-1 input:not([readonly])", function () {
        const inputReadonly = $(".measurement-wrapper input[readonly]:not('.cycle')");
        resetValue(inputReadonly);
        $('.ratio').prop('selectedIndex', 0);
        if ( !$('.ratio1').hasClass('border-danger') ) {
            $('.ratio1').addClass('border-danger');
        }
        if ( !$('.ratio2').hasClass('border-danger') ) {
            $('.ratio2').addClass('border-danger');
        }
    });

    body.on("input", ".main-wrapper-1 input:not([readonly])", function () {
        const mainWrapperReadonly = $(".main-wrapper-1 input[readonly]"),
            basicWrapperReadonly = $(".basic-wrapper input[readonly]:not('.cycle')");
        resetValue(mainWrapperReadonly);
        resetValue(basicWrapperReadonly);
        $('.ratio').prop('selectedIndex', 0);
        if ( !$('.ratio1').hasClass('border-danger') ) {
            $('.ratio1').addClass('border-danger');
        }
        if ( !$('.ratio2').hasClass('border-danger') ) {
            $('.ratio2').addClass('border-danger');
        }
    });

    /** Изменение контрольных данных */
    body.on("input", ".head-wrapper-2 input:not([readonly])", function () {
        const headWrapper = $(".head-wrapper-2 input[readonly]"),
            mainWrapper = $(".main-wrapper-2 input[readonly]");
        resetValue(headWrapper);
        resetValue(mainWrapper);

        $('.ratio2').prop('selectedIndex', 0);
        if ( !$('.ratio2').hasClass('border-danger') ) {
            $('.ratio2').addClass('border-danger');
        }
    });

    body.on("input", ".main-wrapper-2 input:not([readonly])", function () {
        const inputReadonly = $(".main-wrapper-2 input[readonly]");
        resetValue(inputReadonly);
        $('.ratio2').prop('selectedIndex', 0);
        if ( !$('.ratio2').hasClass('border-danger') ) {
            $('.ratio2').addClass('border-danger');
        }
    });
    if ($('.main_damage2').val() === 'Отсутствуют') {
        $('.main_strength2').attr('readonly', false);
        $('.com').val('');
        $('.com').hide();
        $('[for="ratio2"]').show();
    } else {
        $('.main-mass2').val('-');
        $('.main_strength2').val('-');
        $('.main_medium2').val('-');
        $('.main_difference2').val('-');
        $('.main_rms2').val('-');
        $('.main_variation2').val('-');
        $('.main_bottom_line2').val('-');
        $('.main_strength2').attr('readonly', true);
        $('.com').show();
        $('[for="ratio2"]').hide();
    }
    $('.main_damage2').on('change', function() {
        var selectedValue = $(this).val();
        if (selectedValue === 'Отсутствуют') {
            $('.main_strength2').attr('readonly', false);
            $('.com').val('');
            $('.com').hide();
            $('[for="ratio2"]').show();
            $('.ratio2').val('1');
        } else {
            $('.main-mass2').val('-');
            $('.main_strength2').val('-');
            $('.main_medium2').val('-');
            $('.main_difference2').val('-');
            $('.main_rms2').val('-');
            $('.main_variation2').val('-');
            $('.main_bottom_line2').val('-');
            $('.main_strength2').attr('readonly', true);
            $('.com').show();
            $('[for="ratio2"]').hide();
            $('.ratio2').val('2');
        }
    });
});
