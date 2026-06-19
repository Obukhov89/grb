// Неразрушающий контроль (Прочность на сжатие ГОСТ 17624 п. 7)
$(function ($) {
    const body = $('body');
    $('#measurementModalForm').attr('enctype', 'multipart/form-data');
    function handleAjaxError(jqXHR, exception) {
        let msg = '';
        if (jqXHR.status === 0) {
            msg = 'Not connect. Verify Network.';
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
            msg = 'Uncaught Error: ' + jqXHR.responseText;
        }
        console.error(msg);
    }

    /** Функция для переключения модального окна загрузки */
    function toggleLoading(show) {
        $('#loadingModal').toggleClass('d-none', !show);
    }

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

    /** Функция для расчёта граничного коэффициента вариации (Vm) **/
    function getLimitingVm(n) {
        // Из ГОСТ 18105-2018 п 5.5
        let vmTable = [
            { n: 3,  Vm: 4 },
            { n: 5,  Vm: 6 },
            { n: 8,  Vm: 7.5 },
            { n: 10, Vm: 8 },
            { n: 15, Vm: 9 },
            { n: 20, Vm: 10 },
            { n: 30, Vm: 12 } // для n ≥ 30 принимаем значение 12
        ];

        // Если n меньше или равно минимальному значению таблицы, возвращаем минимальное Vm
        if (n <= vmTable[0].n) {
            return vmTable[0].Vm;
        }
        // Если n больше или равно максимальному значению (30), возвращаем последнее значение Vm
        if (n >= vmTable[vmTable.length - 1].n) {
            return vmTable[vmTable.length - 1].Vm;
        }

        for (var i = 0; i < vmTable.length - 1; i++) {
            // Если n точно совпадает с табличным значением, возвращаем соответствующее Vm
            if (n === vmTable[i].n) {
                return vmTable[i].Vm;
            }
            // Если n находится между двумя табличными значениями, выполняем линейную интерполяцию
            if (n > vmTable[i].n && n < vmTable[i + 1].n) {
                var n1 = vmTable[i].n,
                    Vm1 = vmTable[i].Vm,
                    n2 = vmTable[i + 1].n,
                    Vm2 = vmTable[i + 1].Vm;

                // Линейная интерполяция
                var interpolatedVM = Vm1 + (Vm2 - Vm1) * (n - n1) / (n2 - n1);
                return interpolatedVM;
            }
        }
    }

    // /** Выбор схемы испытаний */
    // body.off('change', '.concrete_strength_17624_7 #scheme').on('change', '.concrete_strength_17624_7 #scheme', function (e) {
    //     const concreteStrength = $('.concrete_strength_17624_7'),
    //         measurementJournal = concreteStrength.find('#measurementJournal'),
    //         calculationData = $('.concrete_strength_17624_7 #calculationData');
    //
    //     const schemesAnchor = {
    //         'v': {
    //             'journal': '/ulab/nk/graduationList/',
    //             'measurements': '/ulab/nk/getGraduationListAjax/',
    //         },
    //         'g': {
    //             'journal': '/ulab/nk/matchCoefficientList/',
    //             'measurements': '/ulab/nk/getMatchCoefficientsAjax/',
    //         },
    //     }
    //
    //     let scheme = $(this).val();
    //
    //     // Сылка на журнал листов измерений
    //     if (scheme === 'v' && schemesAnchor[scheme]) {
    //         measurementJournal.attr('href', schemesAnchor[scheme]['journal']);
    //         measurementJournal.removeClass('icon-disabled');
    //     } else {
    //         measurementJournal.attr('href', '#');
    //         measurementJournal.addClass('icon-disabled');
    //     }
    //
    //     // Очищаем список листов измерений
    //     $('#measurementList').find('option').remove();
    //
    //     // Очищаем лист измерения
    //     calculationData.remove();
    //
    //     if (scheme === 'v') {
    //         $('#measurementList').append(`<option value="" selected="" disabled="">Выберите лист измерения</option>`);
    //         $('#measurementList').closest('.col').show();
    //     } else {
    //         $('.concrete_strength_17624_7 #measurementList').append(`<option value="" selected="" data-scheme="${scheme}" disabled="">Выберите лист измерения</option>`);
    //         $('.concrete_strength_17624_7 #measurementList').closest('.col').hide();
    //
    //         $('.concrete_strength_17624_7 #measurementList').trigger('change')
    //
    //         return false;
    //     }
    //
    //     if (schemesAnchor[scheme]) {
    //         $.ajax({
    //             method: 'POST',
    //             url: schemesAnchor[scheme]['measurements'],
    //             dataType: 'json',
    //             beforeSend: function() {
    //                 $('#loadingModal').removeClass('d-none');
    //             },
    //             success: function (data) {
    //                 // Добавляем список листов измерений
    //                 if (data.length) {
    //                     $.each(data, function(key, value) {
    //                         let ruDate = (new Date(value['date'])).toLocaleDateString('ru-RU');
    //                         $('#measurementList').append(`<option value="${value['id']}" data-scheme="${scheme}">№ ${value['id']} - ${value['object']}, от ${ruDate}</option>`);
    //                     });
    //                 }
    //                 $('#loadingModal').addClass('d-none');
    //             },
    //             error: function (jqXHR, exception) {
    //                 let msg = '';
    //                 if (jqXHR.status === 0) {
    //                     msg = 'Not connect.\n Verify Network.';
    //                 } else if (jqXHR.status === 404) {
    //                     msg = 'Requested page not found. [404]';
    //                 } else if (jqXHR.status === 500) {
    //                     msg = 'Internal Server Error [500].';
    //                 } else if (exception === 'parsererror') {
    //                     msg = 'Requested JSON parse failed.';
    //                 } else if (exception === 'timeout') {
    //                     msg = 'Time out error.';
    //                 } else if (exception === 'abort') {
    //                     msg = 'Ajax request aborted.';
    //                 } else {
    //                     msg = 'Uncaught Error.\n' + jqXHR.responseText;
    //                 }
    //                 console.error(msg)
    //             }
    //         });
    //     }
    // });

    // /** Выбор листа измерения */
    // body.off('change', '.concrete_strength_17624_7 #measurementList').on('change', '.concrete_strength_17624_7 #measurementList', function (e) {
    //     const calculationDataWrapper = $('.concrete_strength_17624_7 #calculationDataWrapper'),
    //         concreteStrength = $('.concrete_strength_17624_7 #concreteStrength'),
    //         btnWrapper = concreteStrength.find('.btn-wrapper'),
    //         option = $(this).find('option:selected');
    //
    //     let measurementId = $(this).val(),
    //         scheme = option.data('scheme'),
    //         ugtpId = $(this).data('ugtp');
    //         //ugtpId = $('#ugtpId').val();
    //
    //     const schemesAnchor = {
    //         'v': {
    //             'measurement': '/ulab/nk/getGraduationAjax/',
    //         },
    //         'g': {
    //             'measurement': '/ulab/result/getUgtpAjax/',
    //         },
    //     }
    //
    //     // Очищаем лист измерения
    //     $('.concrete_strength_17624_7 #calculationData').remove();
    //
    //     if (schemesAnchor[scheme]) {
    //         $.ajax({
    //             method: 'POST',
    //             url: schemesAnchor[scheme]['measurement'],
    //             data: {
    //                 measurement_id: measurementId,
    //                 ugtp_id: ugtpId,
    //             },
    //             dataType: 'json',
    //             beforeSend: function() {
    //                 $('#loadingModal').removeClass('d-none');
    //             },
    //             success: function (result) {
    //                 let countList = Object.keys(result).length;
    //
    //                 if (countList) {
    //                     if (scheme === 'v') {
    //                         let htmlForSchemeV = getHtmlForSchemeV(result, ugtpId);
    //                         calculationDataWrapper.append(htmlForSchemeV);
    //                     } else {
    //                         result['data'] = [];
    //                         let htmlForSchemeG = getHtmlForSchemeG(result, ugtpId);
    //                         calculationDataWrapper.append(htmlForSchemeG);
    //                     }
    //                 }
    //                 $('#loadingModal').addClass('d-none')
    //             },
    //             error: function (jqXHR, exception) {
    //                 let msg = '';
    //                 if (jqXHR.status === 0) {
    //                     msg = 'Not connect.\n Verify Network.';
    //                 } else if (jqXHR.status === 404) {
    //                     msg = 'Requested page not found. [404]';
    //                 } else if (jqXHR.status === 500) {
    //                     msg = 'Internal Server Error [500].';
    //                 } else if (exception === 'parsererror') {
    //                     msg = 'Requested JSON parse failed.';
    //                 } else if (exception === 'timeout') {
    //                     msg = 'Time out error.';
    //                 } else if (exception === 'abort') {
    //                     msg = 'Ajax request aborted.';
    //                 } else {
    //                     msg = 'Uncaught Error.\n' + jqXHR.responseText;
    //                 }
    //                 console.error(msg)
    //             }
    //         });
    //     }
    // });

    /**
     * Добавить конструкцию
     */
    body.off('click', '.concrete_strength_17624_7 .add-construction').on('click', '.concrete_strength_17624_7 .add-construction', function () {
        let constructionRow = $(this).closest('.construction-row'),
            constructionRowFirst = $('.concrete_strength_17624_7 .construction-row:first'),
            cloneConstructionRow = $(constructionRow).clone(true),
            constructionWrapper = constructionRow.closest('.construction-wrapper'),
            constructionRowCount = constructionWrapper.find('.construction-row').length;

        const inputReadonly = cloneConstructionRow.find('input');
        resetValue(inputReadonly);

        constructionRow.find('.name-wrapper').attr('rowspan', ++constructionRowCount);
        constructionRowFirst.find('.construction-strength-wrapper').attr('rowspan', constructionRowCount);
        constructionRowFirst.find('.concrete-class-wrapper').attr('rowspan', constructionRowCount);

        cloneConstructionRow.find('.name-wrapper').remove();
        cloneConstructionRow.find('.construction-strength-wrapper').remove();
        cloneConstructionRow.find('.concrete-class-wrapper').remove();

        cloneConstructionRow.find('.add-construction').replaceWith(
            `<button type="button" class="btn btn-danger del-construction mt-0 btn-square">
                 <i class="fa-solid fa-minus icon-fix"></i>
            </button>`
        );

        constructionWrapper.append(cloneConstructionRow);
    })

    /**
     * Удалить конструкцию
     */
    body.off('click', '.concrete_strength_17624_7 .del-construction').on('click', '.concrete_strength_17624_7 .del-construction', function () {
        let constructionRow = $(this).closest('.construction-row'),
            constructionRowFirst = $('.construction-row:first'),
            constructionWrapper = constructionRow.closest('.construction-wrapper'),
            constructionRowCount = constructionWrapper.find('.construction-row').length;

        constructionRowFirst.find('.name-wrapper').attr('rowspan', constructionRowCount);
        constructionRowFirst.find('.construction-strength-wrapper').attr('rowspan', constructionRowCount);
        constructionRowFirst.find('.concrete-class-wrapper').attr('rowspan', constructionRowCount);

        constructionRow.remove();

        $(".concrete_strength_17624_7 #calculationData input:not([type='file']):not([readonly]):not('.do-not-clean')").trigger('input');
    })

    /** Блокировать кнопку при сохранении */
    $('#measurementModalForm').on('submit', function() {
        const frostWrapper =  $('#frostWrapper');

        frostWrapper.find('.calculate').prop('disabled', true);
        $(this).find('.save').replaceWith(
            `<button class="btn btn-primary" type="button" disabled>
                <span class="spinner-grow spinner-grow-sm spinner-save" role="status" aria-hidden="true"></span>
                Сохранение...
            </button>`
        );
    });

    body.off('click', '.concrete_strength_17624_7 .calculate-v').on('click', '.concrete_strength_17624_7 .calculate-v', function (e) {
        e.preventDefault();
        const ugtpId = $('.concrete_strength_17624_7 .ugtp-id').val();

        const calculationData = $('.concrete_strength_17624_7 #calculationData'),
            inputSingleValue1 = calculationData.find('.single-value-1'),
            inputSingleValue2 = calculationData.find('.single-value-2'),
            inputMean = calculationData.find('.mean'),
            inputAreaStrength = calculationData.find('.area-strength'),
            inputResultValue = calculationData.find(`[name="form_data[${ugtpId}][result_value]"]`),
            inputConcreteClass = calculationData.find(`[name="form_data[${ugtpId}][concrete_class]"]`),
            inputS1 = calculationData.find(`[name="form_data[${ugtpId}][S1]"]`),
            inputS4 = calculationData.find(`[name="form_data[${ugtpId}][S4]"]`),
            inputS2 = calculationData.find(`[name="form_data[${ugtpId}][S2]"]`),
            inputSm = calculationData.find(`[name="form_data[${ugtpId}][Sm]"]`),
            inputVm = calculationData.find(`[name="form_data[${ugtpId}][Vm]"]`),
            inputPercent = calculationData.find(`[name="form_data[${ugtpId}][percent]"]`),
            inputKt = calculationData.find(`[name="form_data[${ugtpId}][Kt]"]`),
            inputR = calculationData.find('#R'),
            inputV = calculationData.find('#V');

        const TWENTY_EIGHT = 28;

        //let Kt = +calculationData.find(`[name="form_data[${ugtpId}][Kt]"]`).val(),
        let S3 = +calculationData.find(`[name="form_data[${ugtpId}][S3]"]`).val(),
            B = +calculationData.find(`[name="form_data[${ugtpId}][class]"]`).val(),
            dayToTest = +calculationData.find(`[name="form_data[${ugtpId}][day_to_test]"]`).val(),
            r = +calculationData.find(`[name="form_data[${ugtpId}][r]"]`).val(),
            method = calculationData.find(`[name="form_data[${ugtpId}][method]"]`).val();
 
        let areaStrengthCount = calculationData.find('.area-strength').length;

        //meanCalculation(inputSingleValue1, inputSingleValue2, inputMeans);

        // Проверка заполненость полей "Показания СИ (Среднее значение на контролируемом участке)"
        let checkAfterBefore =
            checkEmptyFields(
                `.mean`,
                `#concreteStrength`,
                `#calculationData input[readonly]:not('.do-not-clean')`
            );
        if (!checkAfterBefore) {
            return false;
        }

        // Прочность бетона (Контролируе-мого участка), МПа
        let arrAreaStrength = [];
        inputMean.each(function (i, item) {
            let mean = +$(item).val(),
                R = +inputR.val(),
                V = +inputV.val();

            let areaStrength = R * mean + V;
            let roundAreaStrength = round(areaStrength, 1);

            arrAreaStrength.push(areaStrength);
            $(inputAreaStrength.get(i)).val(roundAreaStrength.toFixed(1));

        });

        // Прочность бетона (Конструкции), МПа
        let averAreaStrength = average(arrAreaStrength);
        let roundAverAreaStrength = round(averAreaStrength, 1);
        inputResultValue.val(roundAverAreaStrength.toFixed(1));
        //let roundAverAreaStrength = round(averAreaStrength, 2);
        //inputResultValue.val(roundAverAreaStrength.toFixed(2));

        // Фактический класс бетона конструкции по прочности на сжатие
        //let concreteClass = averAreaStrength / Kt;
        //let roundConcreteClass = round(concreteClass, 1);
        //inputConcreteClass.val(roundConcreteClass.toFixed(1));

        let S1 = getStandardDeviation(arrAreaStrength);
        inputS1.val(round(S1, 2).toFixed(2));

        let coefficient = method.split('_')[1];
        let S4 = coefficient * averAreaStrength;
        inputS4.val(round(S4, 2).toFixed(2));

        let S2 = Math.sqrt((S3*S3)+(S4*S4));
        inputS2.val(round(S2, 2).toFixed(2));

        let Sm = (S1+(S2/(Math.sqrt(areaStrengthCount-1))))*(1/(0.7 * r + 0.3));
        inputSm.val(round(Sm, 2).toFixed(2));

        let Vm = Sm * 100 / averAreaStrength;
        inputVm.val(round(Vm, 1).toFixed(1));

        let Kt = calculateKt(round(Vm, 1));
        let roundKt = (typeof Kt === 'number' && !isNaN(Kt)) ? round(Kt, 3).toFixed(3) : null;
        inputKt.val(roundKt)

        // Фактический класс бетона конструкции по прочности на сжатие
        let concreteClass = averAreaStrength / round(Kt, 3);
        let roundConcreteClass = round(concreteClass, 1);
        console.log(averAreaStrength);
        console.log(Kt);
        inputConcreteClass.val(roundConcreteClass.toFixed(1));

        // Факт. Прочность бетона в %
        let percent = (concreteClass * 100 / B);
        inputPercent.val(round(percent));
    });

    body.off('click', '.concrete_strength_17624_7 .calculate-g').on('click', '.concrete_strength_17624_7 .calculate-g', function (e) {
        e.preventDefault();

        const ugtpId = $('.concrete_strength_17624_7 .ugtp-id').val();

        const calculationData = $('.concrete_strength_17624_7 #calculationData'),
            $directMethod = calculationData.find('.direct-method'),
            $indirectMethod = calculationData.find('.indirect-method'),
            $ratioValue = calculationData.find('.ratio-value'),
            $ratioСheck = calculationData.find('.ratio-check'),
            $coefficientCheck = calculationData.find('.coefficient-check'),
            $coeff = calculationData.find('#coeff'),
            $coeffLower = calculationData.find('#coeffLower'),
            $coeffUpper = calculationData.find('#coeffUpper'),
            $adjustedValue = calculationData.find('.adjusted-value'),
            $instrumentReadings = calculationData.find('.instrument-readings'),
            $resultValue = calculationData.find(`[name="form_data[${ugtpId}][result_value]"]`),
            $concreteClass = calculationData.find(`[name="form_data[${ugtpId}][concrete_class]"]`),
            $sumOfSquares = calculationData.find(`[name="form_data[${ugtpId}][sum_of_squares]"]`),
            $variance = calculationData.find(`[name="form_data[${ugtpId}][variance]"]`),
            $Sm = calculationData.find(`[name="form_data[${ugtpId}][Sm]"]`),
            $Vm = calculationData.find(`[name="form_data[${ugtpId}][Vm]"]`),
            $complianceGost = calculationData.find(`[name="form_data[${ugtpId}][compliance_gost]"]`),
            $countInstrumentReadings = calculationData.find('#countInstrumentReadings'),
            $viewVm = calculationData.find('#viewVm');

        let countInstrumentReadings = $instrumentReadings.length;

        // Прямым методом
        let checkDirectMethod =
            checkEmptyFields(
                `.direct-method`,
                `#concreteStrength`,
                `#calculationData input[readonly]:not('.do-not-clean')`
            );
        if (!checkDirectMethod) {
            return false;
        }

        // Косвенным методом
        let checkIndirectMethod =
            checkEmptyFields(
                `.indirect-method`,
                `#concreteStrength`,
                `#calculationData input[readonly]:not('.do-not-clean')`
            );
        if (!checkIndirectMethod) {
            return false;
        }

        // Частное значение, Rос/Rкосв
        let arrRatioValue = [];
        $directMethod.each(function (i, item) {
            let directMethod = +$(item).val(),
                indirectMethod = +$($indirectMethod[i]).val();

            let ratioValue = directMethod / indirectMethod;
            let roundRatioValue = round(ratioValue, 2);

            arrRatioValue.push(ratioValue);
            $($ratioValue.get(i)).val(roundRatioValue.toFixed(2));
        });

        // Коэффициент совпадения (Кс)
        let coeff = average(arrRatioValue);
        let roundCoeff = round(coeff, 2);
        $coeff.val(roundCoeff.toFixed(2));

        // Нижняя граница (0.85*Кс)
        let coeffLower = 0.85 * coeff;
        let roundCoeffLower = round(coeffLower, 2);
        $coeffLower.val(roundCoeffLower.toFixed(2));

        // Верхняя граница (1.15*Кс)
        let coeffUpper = 1.15 * coeff;
        let roundCoeffUpper = round(coeffUpper, 2);
        $coeffUpper.val(roundCoeffUpper.toFixed(2));


        for (const key in arrRatioValue) {
            let ratioValue = +arrRatioValue[key];

            // 0,7≤Rос/Rкосв≤1,3
            let ratioCheck = (ratioValue >= 0.7 && ratioValue <= 1.3) ? 'Соответствует' : 'Не соответствует';

            // 0,85*Кс≤Rос/Rкосв≤1,15*Кс
            let coefficientCheck = (ratioValue >= 0,85 * coeff && ratioValue <= 1.15 * coeff) ?
                'Соответствует' : 'Не соответствует';

            $($ratioСheck.get(key)).val(ratioCheck);
            $($coefficientCheck.get(key)).val(coefficientCheck);
        }

        // Показания СИ
        let checkInstrumentReadings =
            checkEmptyFields(
                `.instrument-readings`,
                `#concreteStrength`,
                `#calculationData input[readonly]:not('.do-not-clean')`
            );
        if (!checkInstrumentReadings) {
            return false;
        }

        // Значение с учетом коэффициента сопадения
        let arrAdjustedValue = [];
        $instrumentReadings.each(function (i, item) {
            let instrumentReadings = +$(item).val();

            let adjustedValue = instrumentReadings * coeff;
            let roundAdjustedValue = round(adjustedValue, 1);

            arrAdjustedValue.push(adjustedValue);
            $($adjustedValue.get(i)).val(roundAdjustedValue.toFixed(1));
        });

        // Среднее значение прочности бетона конструкции
        let averageStrength = average(arrAdjustedValue);
        let roundAverageStrength = round(averageStrength, 1);
        $resultValue.val(roundAverageStrength.toFixed(1));

        // Фактический класс бетона конструкции по прочности на сжатие
        let concreteClass = averageStrength * 0.8;
        let roundConcreteClass = round(concreteClass, 1);
        $concreteClass.val(roundConcreteClass.toFixed(1));

        // Рассчитываем сумму квадратов разностей
        let sumOfSquares = 0;
        for (const value of arrAdjustedValue) {
            sumOfSquares += Math.pow(value - averageStrength, 2);
        }
        let roundSumOfSquares = round(sumOfSquares, 2);
        $sumOfSquares.val(roundSumOfSquares.toFixed(2));

        let variance = sumOfSquares / (countInstrumentReadings - 1);
        let roundVariance = round(variance, 2);
        $variance.val(roundVariance.toFixed(2));

        let Sm = Math.sqrt(variance);
        let roundSm = round(Sm, 2);
        $Sm.val(roundSm.toFixed(2));

        let Vm = Sm * 100 / averageStrength;
        let roundVm = round(Vm, 2);
        $Vm.val(roundVm.toFixed(2));

        // Расчёт граничного коэффициента вариации (Vm)
        let limitingVm = getLimitingVm(countInstrumentReadings);
        let floatLimitingVm = parseFloat(limitingVm);

        // Соответствие п. 5.5 ГОСТ 18105
        let complianceGost = Vm <= floatLimitingVm ? 'Соответствует' : 'Не соответствует';

        $countInstrumentReadings.text(countInstrumentReadings);
        $viewVm.text(floatLimitingVm);
        $complianceGost.val(complianceGost);
    });

    /** Рандомайзер */
    body.off('click', '.concrete_strength_17624_7 .generate').on("click", ".concrete_strength_17624_7 .generate", function () {
        const inputReadonly = $(".concrete_strength_17624_7 #calculationData input[readonly]:not('.do-not-clean')");
        const min = parseInt($('#min').val());
        const max = parseInt($('#max').val());

        let mean = $('.mean');

        if (isNaN(min) || isNaN(max) || min >= max) {
            alert('Пожалуйста, введите корректные значения.');
            return;
        }

        const randomValues = [];
        for (let i = 0; i < mean.length; i++) {
            randomValue = Math.floor(Math.random() * (max - min + 1)) + min;
            randomValues.push((randomValue / 10).toFixed(1));
        }

        resetValue(inputReadonly);

        mean.each(function (index) {
            $(this).val(randomValues[index]);
        });
    });

    /**
     * Выбор схемы испытаний
     */
    $('body')
        .off('change', '.concrete_strength_17624_7 #scheme')
        .on('change', '.concrete_strength_17624_7 #scheme', function () {
            const $container = $('.concrete_strength_17624_7'),
                $measurementJournal = $container.find('#measurementJournal'),
                $measurementList = $container.find('#measurementList'),
                $calculationData = $container.find('#calculationData'),
                $calculationDataWrapper = $container.find('#calculationDataWrapper');

            const scheme = $(this).val();

            // Очистка предыдущих данных
            $measurementList.empty();
            $calculationData.remove();
            $calculationDataWrapper.empty();

            if (scheme === 'v') {
                // Для схемы "v": активируем ссылку на журнал и показываем список измерений
                $measurementJournal.attr('href', '/ulab/nk/graduationList/').removeClass('icon-disabled');
                $measurementList.append('<option value="" selected disabled>Выберите лист измерения</option>');
                $measurementList.closest('.col').show();

                loadMeasurementListForV();
            } else if (scheme === 'g') {
                // Для схемы "g": отключаем ссылку на журнал и скрываем список измерений
                $measurementJournal.attr('href', '#').addClass('icon-disabled');
                $measurementList.closest('.col').hide();

                const ugtpId = $container.find('.ugtp-id').val() || null;

                loadMeasurementForm('/ulab/result/getUgtpAjax/', scheme, ugtpId);
            } else {
                $measurementJournal.attr('href', '#')
                    .addClass('icon-disabled');
                $measurementList.append('<option value="" selected disabled>Выберите лист измерения</option>');
                $measurementList.closest('.col').show();
            }
        });

    /**
     * Выбор листа измерения (актуально для схемы "v")
     */
    $('body')
        .off('change', '.concrete_strength_17624_7 #measurementList')
        .on('change', '.concrete_strength_17624_7 #measurementList', function () {
            const $container = $('.concrete_strength_17624_7'),
                $calculationDataWrapper = $container.find('#calculationDataWrapper');

            const $selectedOption = $(this).find('option:selected');
            const measurementId = $(this).val();
            const scheme = $selectedOption.data('scheme') || 'v';
            const ugtpId = $(this).data('ugtp') || $('#ugtpId').val() || null;

            $container.find('#calculationData').remove();
            $calculationDataWrapper.empty();

            loadMeasurementForm('/ulab/nk/getGraduationAjax/', scheme, ugtpId, measurementId);
        });

    /** Загрузка списка измерений (только для схемы 'v') */
    function loadMeasurementListForV() {
        const $measurementList = $('.concrete_strength_17624_7 #measurementList');
        toggleLoading(true);
        $.ajax({
            method: 'POST',
            url: '/ulab/nk/getGraduationListAjax/',
            dataType: 'json',
            success: function (data) {
                if (Array.isArray(data) && data.length > 0) {
                    data.forEach(item => {
                        const ruDate = new Date(item.date).toLocaleDateString('ru-RU');
                        $measurementList.append(
                            `<option value="${item.id}" data-scheme="v">№ ${item.id} - ${item.object}, от ${ruDate}</option>`
                        );
                    });
                }
            },
            error: function (jqXHR, exception) {
                handleAjaxError(jqXHR, exception);
            },
            complete: function () {
                toggleLoading(false);
            }
        });
    }

    // Загрузка формы листа измерения
    // Для scheme "v" measurementId приходит из выбора, для "g" может быть null
    function loadMeasurementForm(url, scheme, ugtpId, measurementId = null) {
        const $calculationDataWrapper = $('.concrete_strength_17624_7 #calculationDataWrapper');
        toggleLoading(true);
        $.ajax({
            method: 'POST',
            url: url,
            dataType: 'json',
            data: {
                measurement_id: measurementId,
                ugtp_id: ugtpId
            },
            success: function (result) {
                if (Object.keys(result).length) {
                    let htmlContent = '';
                    if (scheme === 'v') {
                        htmlContent = getHtmlForSchemeV(result, ugtpId);
                    } else if (scheme === 'g') {
                        htmlContent = getHtmlForSchemeG(result, ugtpId);
                    }
                    $calculationDataWrapper.append(htmlContent);
                }
            },
            error: function (jqXHR, exception) {
                handleAjaxError(jqXHR, exception);
            },
            complete: function () {
                toggleLoading(false);
            }
        });
    }



    /**
     * @deprecated
     * Расчитать среднее значение на контролируемом участке
     * @param inputSingleValue1
     * @param inputSingleValue2
     * @param inputMeans
     */
    function meanCalculation(inputSingleValue1, inputSingleValue2, inputMeans)
    {
        inputSingleValue1.each(function (index, item) {
            let singleValue1 = $(item).val(),
                singleValue2 = $(inputSingleValue2[index]).val(),
                inputMean = $(inputMeans[index]);

            const decimalPlaces = 1;

            // Если одно из значений пусто, то расчёт среднего не производим
            if (singleValue1 === '' || singleValue2 === '') {
                return;
            }

            singleValue1 = +singleValue1;
            singleValue2 = +singleValue2;

            let mean = (singleValue1 + singleValue2) / 2;
            inputMean.val(round(mean, decimalPlaces).toFixed(decimalPlaces));
        });
    }

    /**
     * Метод получения стандартного отклонения из массива
     * @param array
     * @returns {number}
     */
    function getStandardDeviation(array) {
        const n = array.length;
        const mean = array.reduce((a, b) => a + b) / n;
        return Math.sqrt(array.map(x => Math.pow(x - mean, 2)).reduce((a, b) => a + b) / (n - 1));
    }

    /**
     * Рассчитывает Коэффициент Кт
     * @param Vm
     * @returns {null|number}
     */
    function calculateKt(Vm) {
        // Vm(H10)

        if (typeof Vm !== 'number' || isNaN(Vm)) {
            console.error('Ошибка: Vm должно быть числом');
            return null;
        }

        const A = [6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16];
        const B = [1.07, 1.08, 1.09, 1.11, 1.14, 1.18, 1.23, 1.28, 1.33, 1.38, 1.43];

        // Упрощенный поиск индекса
        let H77 = -1;

        // Ищем наибольший индекс, где A[i] <= Vm
        for (let i = A.length - 1; i >= 0; i--) {
            if (A[i] <= Vm) {
                H77 = i;
                break;
            }
        }

        // Если не нашли подходящий индекс
        if (H77 === -1) {

            // Используем первые две точки для экстраполяции вниз
            let x1 = A[0];
            let x2 = A[1];
            let y1 = B[0];
            let y2 = B[1];

            // Линейная экстраполяция: y = y1 + (x - x1) / (x2 - x1) * (y2 - y1)
            let y = y1 + (Vm - x1) / (x2 - x1) * (y2 - y1);

            // Ограничиваем минимальное значение (опционально)
            if (y < 0) {
                console.warn('Расчетное значение меньше 0, ограничиваем минимальным значением');
                return 0;
            }

            return y;
        }

        // Если это последний элемент массива
        if (H77 === A.length - 1) {
            console.error('Ошибка: Vm больше максимального значения в массиве A');
            return null;
        }

        // Если Vm(H10) меньше минимального значения или больше максимального
        if (H77 === -1 || (H77 === 0 && A[0] > Vm)) {
            return null; // Значение вне диапазона
        }

        // Определение H79
        let H79 = H77 + 1;

        // Определение x1(I77) и x2(I79)
        let x1 = A[H77]; // Значение из массива A по индексу H77
        let x = Vm; // I78 = Vm(H10)
        let x2 = (H79 < A.length) ? A[H79] : null; // Проверяем границы массива

        // Определение y1(J77) и y2(J79)
        let y1 = B[H77]; // Значение из массива B по индексу H77
        let y2 = (H79 < B.length) ? B[H79] : null; // Проверяем границы массива

        // Если x2(I79) или y2(J79) равно null, это значит, что Vm(H10) было максимальным значением
        if (x2 === null || y2 === null) {
            return null;
        }

        //J78 = J77 + (I78 - I77) / (I79 - I77) * (J79 - J77);
        //y = y1 + (x - x1) / (x2 - x1) * (y2 - y1)

        // Расчет y(J78) по формуле интерполяции
        let y = y1 + (x - x1) / (x2 - x1) * (y2 - y1);

        return y;
    }

    /** Редактирование */
    body.on("input", ".concrete_strength_17624_7 #calculationData input:not([type='file']):not([readonly]):not('.do-not-clean')", function () {
        const inputReadonly = $(".concrete_strength_17624_7 #calculationData input[readonly]:not('.do-not-clean')");
        resetValue(inputReadonly);
    });

    $('.delete_file').click(function () {
        $(this).parents('.input-group').find('input').val('');
    });

    // /** Рандомайзер */
    // body.on("click", ".concrete_strength_17624_7 .generate", function () {
    //     const inputReadonly = $(".concrete_strength_17624_7 #calculationData input[readonly]:not('.do-not-clean')");
    //     const min = parseInt($('#min').val());
    //     const max = parseInt($('#max').val());
    //
    //     let mean = $('.mean');
    //
    //     if (isNaN(min) || isNaN(max) || min >= max) {
    //         alert('Пожалуйста, введите корректные значения.');
    //         return;
    //     }
    //
    //     const randomValues = [];
    //     for (let i = 0; i < mean.length; i++) {
    //         randomValue = Math.floor(Math.random() * (max - min + 1)) + min;
    //         randomValues.push((randomValue / 10).toFixed(1));
    //     }
    //
    //     resetValue(inputReadonly);
    //
    //     mean.each(function (index) {
    //         $(this).val(randomValues[index]);
    //     });
    // });
});

/**
 * Получить html для схемы "В"
 * @param result
 * @returns {string}
 */
function getHtmlForSchemeV(result, ugtpId)
{
    return `<div id="calculationData">
            <div class="row mb-3">
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">R</div>
                        </div>
                        <input type="number" class="form-control bg-light-secondary do-not-clean" id="round-R" name="form_data[${ugtpId}][round_R]" step="any"
                               value="${result['data']['round_a']}" readonly>
                        <input type="hidden" class="form-control bg-light-secondary do-not-clean" id="R" 
                                name="form_data[${ugtpId}][R]" step="any" value="${result['data']['a']}" readonly>
                    </div>
                </div>
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">V</div>
                        </div>
                        <input type="number" class="form-control bg-light-secondary do-not-clean" id="round-V" name="form_data[${ugtpId}][round_V]" step="any"
                               value="${result['data']['round_b']}" readonly>
                        <input type="hidden" class="form-control bg-light-secondary do-not-clean" id="V" 
                                name="form_data[${ugtpId}][V]" step="any" value="${result['data']['b']}" readonly>
                    </div>
                </div>
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">r</div>
                        </div>
                        <input type="number" class="form-control bg-light-secondary do-not-clean" id="r" name="form_data[${ugtpId}][r]" step="any"
                               value="${result['data']['r']}" readonly>
                    </div>
                </div>
                <div class="form-group col">
                    <div class="input-group">
                        <span class="input-group-text">B</span>
                        <input type="text" class="form-control number-only bg-light-secondary do-not-clean" name="form_data[${ugtpId}][class]"
                               value="${result['data']['concrete_class'] ?? ''}" readonly>
                    </div>
                </div>
                <div class="form-group col">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">Прибор</div>
                        </div>
                        <input type="text" class="form-control bg-light-secondary do-not-clean" id="measuringDevice" 
                                name="form_data[${ugtpId}][measuring_device]" value="${result['data']['measuring_device'] ?? ''}" readonly>
                    </div>
                </div>
                <div class="form-group col">
                    <div class="input-group">
                        <select id="method" class="form-control bg-light-secondary do-not-clean pointer-events-none" name="form_data[${ugtpId}][method]" readonly>
                            <optgroup label="Метод отрыва со скалыванием">
                                <option value="separation_0.04" ${'separation_0.04' === result['data']['method'] ? 'selected' : ''}>Глубина 48 мм</option>
                                <option value="separation_0.05" ${'separation_0.05' === result['data']['method'] ? 'selected' : ''}>Глубина 35 мм</option>
                                <option value="separation_0.06" ${'separation_0.06' === result['data']['method'] ? 'selected' : ''}>Глубина 30 мм</option>
                            </optgroup>
                            <optgroup label="Метод скалывания ребра">
                                <option value="chipping_0.04" ${'chipping_0.04' === result['data']['method'] ? 'selected' : ''}>Скалывание ребра</option>
                            </optgroup>
                            <optgroup label="Разрушающий метод">
                                <option value="destructive_0.02" ${'destructive_0.02' === result['data']['method'] ? 'selected' : ''}>Разрушающий</option>
                            </optgroup>
                        </select>
                    </div>
                </div>
                <div class="form-group col">
                    <div class="input-group">
                        <input type="text" class="form-control number-only bg-light-secondary do-not-clean" id="dayToTest" name="form_data[${ugtpId}][day_to_test]"
                               value="${result['data']['day_to_test'] ?? ''}" readonly>
                        <span class="input-group-text">суток</span>
                    </div>
                </div>
            </div>
    
            <div class="row">
                <div class="col">
                    <table class="table text-center align-middle table-bordered">
                        <thead>
                        <tr class="align-middle">
                            <th rowspan="2">Наименование конструкции</th>
                            <th colspan="3">Показания СИ</th>
                            <th colspan="2">Прочность бетона, МПа</th>
                            <th rowspan="2">Фактический класс бетона конструкции по прочности на сжатие</th>
                            <th rowspan="2">+/-</th>
                        </tr>
                        <tr class="align-middle">
                            <th colspan="2">Единичные значения</th>
                            <th>Среднее значение на контролируемом участке</th>
                            <th>Контролируе-мого участка</th>
                            <th>Конструкции</th>
                        </tr>
                        </thead>
                        <tbody class="construction-wrapper">
                        <tr class="construction-row">
                            <td class="name-wrapper">
                                <input type="text" class="form-control bg-light-secondary do-not-clean" name="form_data[${ugtpId}][name_for_protocol]"  
                                       value="${result['name_for_protocol']}" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white single-value-1" name="form_data[${ugtpId}][single_value_1][]"
                                       step="any" value="">
                            </td>
                            <td>
                                <input type="number" class="form-control single-value-2 ${result['measuring_device'] === 'ИПС' ? 'bg-light-secondary' : 'bg-white'}"
                                       name="form_data[${ugtpId}][single_value_2][]" step="any" value="" 
                                    ${result['measuring_device'] === 'ИПС' ? 'disabled' : ''}>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white mean clear" name="form_data[${ugtpId}][mean][]"
                                       step="any" value="">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white area-strength bg-light-secondary" 
                                        name="form_data[${ugtpId}][single_values][]" step="any" value="" readonly>
                            </td>
                            <td class="construction-strength-wrapper">
                                <input type="number" class="form-control bg-white bg-light-secondary" 
                                        name="form_data[${ugtpId}][result_value]" step="any" value="" readonly>
                            </td>
                            <td class="concrete-class-wrapper">
                                <input type="number" class="form-control bg-white bg-light-secondary" name="form_data[${ugtpId}][concrete_class]"
                                       step="any" value="" readonly>
                            </td>
                            <td>
                                <button class="btn mt-0 btn-square add-construction btn-primary" type="button">
                                    <i class="fa-solid fa-plus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
    
            <div class="row">
                <div class="col">
                    <table class="table text-center align-middle table-bordered mb-0">
                        <thead>
                        <tr class="align-middle">
                            <th colspan="5">Расчет характеристик однородности бетона</th>
                            <th rowspan="3">Текущий коэффициент вариации прочности Vm,%</th>
                            <th rowspan="3">Коэффициент Кт</th>
                            <th rowspan="3">Факт. Прочность бетона в %</th>
                        </tr>
                        <tr class="align-middle">
                            <th colspan="4">Среднеквадратическое отклонение прочности, МПа (для группы конструкций</th>
                            <th>Среднеквадратическое отклонение с учетом установленной градуировочной зависимости</th>
                        </tr>
                        <tr class="align-middle">
                            <th>S1</th>
                            <th>S3=Sт.м.н.</th>
                            <th>S4</th>
                            <th>S2</th>
                            <th>Sm</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" name="form_data[${ugtpId}][S1]" 
                                step="any" value="" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-light-secondary do-not-clean" name="form_data[${ugtpId}][S3]" step="any" 
                                value="${result['data']['S']}" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" name="form_data[${ugtpId}][S4]" 
                                step="any" value="" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" name="form_data[${ugtpId}][S2]" 
                                step="any" value="" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" name="form_data[${ugtpId}][Sm]" 
                                step="any" value="" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" name="form_data[${ugtpId}][Vm]" 
                                step="any" value="" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" name="form_data[${ugtpId}][Kt]" 
                                step="any" value="" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" name="form_data[${ugtpId}][percent]" step="any" 
                                value="" readonly>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="row mb-5">
                <div class="col">
                    <lable for="comment">Примечание</lable>
                    <textarea class="form-control mw-100"
                              rows="5" id="comment" name="form_data[${ugtpId}][comment]"></textarea>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Адрес</label>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col">
                            <input type="text" class="form-control do-not-clean" name="form_data[${ugtpId}][address]"
                                   value="" placeholder="Адрес места осуществления деятельности" required>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Схема участков</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="file" name="form_data[${ugtpId}][drawing_file]" class="form-control" value="">
                        <input type="text" name="form_data[${ugtpId}][drawing]" class="form-control do-not-clean"
                                placeholder="Нет сохраненного файла" value="" readonly>
                        <a class="btn btn-outline-secondary btn-square btn-icon disabled" title="Скачать/Открыть" >
                            <i class="fa-regular fa-file-lines"></i>
                        </a>
                        <a class="btn btn-outline-danger btn-square btn-icon disabled" title="Удалить" >
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    </div>
                    <small class="form-text text-muted">Загрузите изображение в формате .jpg, .jpeg или .png.</small>
                </div>
                <div class="col-sm-2"></div>
            </div>
            
            <div class="row">
                <label class="col-sm-2 col-form-label">Косвенный метод</label>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="designation1">Обозначение</label>
                                <input type="text" class="form-control" id="designation1"
                                       name="form_data[${ugtpId}][designation_1]" value="К1-К20">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="decoding1">Расшифровка</label>
                                <input type="text" class="form-control" id="decoding1"
                                       name="form_data[${ugtpId}][decoding_1]" value="Косвенный метод №1-№20">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>
            
            <div class="row">
                <label class="col-sm-2 col-form-label">Рандомайзер</label>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="min">От:</label>
                                <input type="number" class="form-control do-not-clean" id="min"
                                       name="form_data[${ugtpId}][min]"
                                       value="" placeholder="Мин. значение">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="max">До:</label>
                                <input type="number" class="form-control do-not-clean" id="max"
                                       name="form_data[${ugtpId}][max]"
                                       value="" placeholder="Макс. значение">
                            </div>
                        </div>
                        <div class="col-auto">
                            <div class="form-group">
                                <label></label>
                                <button type="button" class="btn btn-primary generate w-100">Генерировать</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>
            
            <div class="line-dashed-small"></div>

            <div class="row btn-wrapper">
                <div class="col-auto pe-0">
                    <button type="button" class="btn btn-primary calculate-v me-2">Рассчитать</button>
                </div>
                <div class="col-auto ps-0">
                    <button type="submit" class="btn btn-primary save">Сохранить</button>
                </div>
            </div>
        </div>`;
}

/**
 * Получить html для схемы "Г"
 * @param result
 * @returns {string}
 */
function getHtmlForSchemeG(result, ugtpId)
{
    const countRow = 3;
    const rows = Array.from({length: countRow}, (v, i) => {
        let colConstructionDetails = `
            <td rowspan="${countRow}">
                <input type="text" class="form-control bg-light-secondary do-not-clean" 
                    name="form_data[${ugtpId}][name_for_protocol]" value="${result['name_for_protocol']}" readonly>
            </td>`

        return `<tr>
                  ${i === 0 ? colConstructionDetails : ''}
                  <td>
                    <input type="number" class="form-control bg-white direct-method" 
                           name="form_data[${ugtpId}][direct_method][]" step="any">
                  </td>
                  <td>
                    <input type="number" class="form-control bg-white indirect-method" 
                           name="form_data[${ugtpId}][indirect_method][]" step="any">
                  </td>
                  <td>
                    <input type="number" class="form-control bg-light-secondary ratio-value" 
                           name="form_data[${ugtpId}][ratio_value][]" step="any" readonly>
                  </td>
                  <td>
                      <input type="text" class="form-control bg-light-secondary ratio-check"
                           name="form_data[${ugtpId}][ratio_check][]" readonly>
                    
                  </td>
                  <td>
                    <input type="text" class="form-control bg-light-secondary coefficient-check"
                           name="form_data[${ugtpId}][coefficient_check][]" readonly>
                    </select>
                  </td>
                  <td>
                    <select class="form-control measurement-rejection bg-white" 
                            name="form_data[${ugtpId}][measurement_rejection][]">
                      <option value="not_required">Не требуется</option>
                      <option value="required">Требуется</option>
                    </select>
                  </td>
                </tr>
          `}).join('');


    return `<div id="calculationData">
            <div class="row mb-3">
                <div class="form-group col">
                    <div class="input-group">
                        <span class="input-group-text">B</span>
                        <input type="text" class="form-control number-only bg-white" 
                            name="form_data[${ugtpId}][class]">
                    </div>
                </div>
                <div class="form-group col">
                    <div class="input-group">
                        <input type="text" class="form-control number-only bg-white" id="dayToTest" 
                            name="form_data[${ugtpId}][day_to_test]">
                        <span class="input-group-text">суток</span>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col">
                    <table class="table text-center align-middle table-bordered">
                        <thead>
                        <tr class="align-middle">
                            <th rowspan="2">Наименование, месторасположение и дата бетонирования конструкции</th>
                            <th colspan="2">Прочность бетона участка конструкции на сжатие, МПа</th>
                            <th>Частное значение</th>
                            <th colspan="2">Проверка соблюдения условия для Rос/Rкосв</th>
                            <th rowspan="2">Отбраковка измерений</th>
                        </tr>
                        <tr class="align-middle">
                            <th>Прямым методом</th>
                            <th>Косвенным методом</th>
                            <th>Rос/Rкосв</th>
                            <th>0,7≤Rос/Rкосв≤1,3</th>
                            <th>0,85*Кс≤Rос/Rкосв≤1,15*Ксc</th>
                        </tr>
                        </thead>
                        <tbody>
                        ${rows}
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col">
                    <table class="table text-center align-middle table-bordered mb-0">
                        <thead>
                        <tr class="align-middle">
                            <th>Коэффициент совпадения (Кс)</th>
                            <th>Нижняя граница (0.85*Кс)</th>
                            <th>Верхняя граница (1.15*Кс)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" id="coeff" name="form_data[${ugtpId}][coeff]" 
                                step="any" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" id="coeffLower" 
                                name="form_data[${ugtpId}][coeff_lower]" step="any" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" id="coeffUpper" name="form_data[${ugtpId}][coeff_upper]" 
                                step="any" readonly>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col">
                    <lable for="comment">Примечание к градуировочной зависимости</lable>
                    <textarea class="form-control mw-100"
                              rows="5" id="comment" name="form_data[${ugtpId}][description]"></textarea>
                </div>
            </div>
            
            <div class="line-dashed-small"></div>
    
            <div class="row">
                <div class="col">
                    <table class="table text-center align-middle table-bordered">
                        <thead>
                        <tr class="align-middle">
                            <th rowspan="3">Наименование, месторасположение  и дата бетонирования конструкции</th>
                            <th colspan="3">Прочность бетона на сжатие, МПа</th>
                            <th rowspan="3">Фактический класс бетона конструкции по прочности на сжатие</th>
                            <th rowspan="3">+/-</th>
                        </tr>
                        <tr class="align-middle">
                            <th colspan="2">Единичные значения (по участкам конструкции)</th>
                        </tr>
                        <tr class="align-middle">
                            <th>Показания СИ</th>
                            <th>Значение с учетом коэффициента сопадения</th>
                            <th rowspan="2c">Среднее значение прочности бетона конструкции</th>
                        </tr>
                        </thead>
                        <tbody class="construction-wrapper">
                        <tr class="construction-row">
                            <td class="name-wrapper">
                                <input type="text" class="form-control bg-light-secondary do-not-clean" name="form_data[${ugtpId}][name_for_protocol]"  
                                       value="${result['name_for_protocol']}" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white instrument-readings mean" 
                                       name="form_data[${ugtpId}][instrument_readings][]" step="any">
                            </td>
                            <td>
                                <input type="number" class="form-control bg-white adjusted-value bg-light-secondary" 
                                        name="form_data[${ugtpId}][adjusted_value][]" step="any" readonly>
                            </td>
                            <td class="construction-strength-wrapper average-strength">
                                <input type="number" class="form-control bg-white bg-light-secondary" 
                                        name="form_data[${ugtpId}][result_value]" step="any" readonly>
                            </td>
                            <td class="concrete-class-wrapper">
                                <input type="number" class="form-control bg-white bg-light-secondary" 
                                        name="form_data[${ugtpId}][concrete_class]" step="any" readonly>
                            </td>
                            <td>
                                <button class="btn mt-0 btn-square add-construction btn-primary" type="button">
                                    <i class="fa-solid fa-plus icon-fix"></i>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
    
            <div class="row">
                <div class="col">
                    <table class="table text-center align-middle table-bordered mb-0">
                        <thead>
                        <tr class="align-middle">
                            <th colspan="5">Коэффициент вариации единичных значений прочности, %</th>
                        </tr>
                        <tr class="align-middle">
                            <th colspan="4">Расчеты</th>
                            <th>Соответствие п. 5.5 ГОСТ 18105</th>
                        </tr>
                        <tr class="align-middle">
                            <th>Сумма квадратов разностей</th>
                            <th></th>
                            <th>Sm</th>
                            <th>Vm</th>
                            <th>Для <span id="countInstrumentReadings"></span> измерений не более <span id="viewVm"></span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" 
                                    name="form_data[${ugtpId}][sum_of_squares]" step="any" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" name="form_data[${ugtpId}][variance]" 
                                step="any" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" name="form_data[${ugtpId}][Sm]" 
                                step="any" readonly>
                            </td>
                            <td>
                                <input type="number" class="form-control bg-light-secondary" name="form_data[${ugtpId}][Vm]" 
                                step="any" readonly>
                            </td>
                            <td>
                                <input type="text" class="form-control bg-light-secondary" 
                                name="form_data[${ugtpId}][compliance_gost]" readonly>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col">
                    <lable for="comment">Примечание</lable>
                    <textarea class="form-control mw-100"
                              rows="5" id="comment" name="form_data[${ugtpId}][comment]"></textarea>
                </div>
            </div>
            
            <div class="row mb-3">
                <label class="col-sm-2 col-form-label">Адрес</label>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col">
                            <input type="text" class="form-control do-not-clean" name="form_data[${ugtpId}][address]"
                                   value="" placeholder="Адрес места осуществления деятельности" required>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>
            
            <div class="form-group row">
                <label class="col-sm-2 col-form-label">Схема участков</label>
                <div class="col-sm-8">
                    <div class="input-group">
                        <input type="file" name="form_data[${ugtpId}][drawing_file]" class="form-control" value="">
                        <input type="text" name="form_data[${ugtpId}][drawing]" class="form-control do-not-clean"
                                placeholder="Нет сохраненного файла" value="" readonly>
                        <a class="btn btn-outline-secondary btn-square btn-icon disabled" title="Скачать/Открыть" >
                            <i class="fa-regular fa-file-lines"></i>
                        </a>
                        <a class="btn btn-outline-danger btn-square btn-icon disabled" title="Удалить" >
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    </div>
                    <small class="form-text text-muted">Загрузите изображение в формате .jpg, .jpeg или .png.</small>
                </div>
                <div class="col-sm-2"></div>
            </div>
            
            <div class="row">
                <label class="col-sm-2 col-form-label">Отрыв со скалыванием</label>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="designation2">Обозначение</label>
                                <input type="text" class="form-control" id="designation2"
                                       name="form_data[${ugtpId}][designation_2]" value="О1-О3">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="decoding2">Расшифровка</label>
                                <input type="text" class="form-control" id="decoding2"
                                       name="form_data[${ugtpId}][decoding_2]" value="Отрыв со скалыванием №1-№3">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>
            
            <div class="row">
                <label class="col-sm-2 col-form-label">Косвенный метод</label>
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="designation1">Обозначение</label>
                                <input type="text" class="form-control" id="designation1"
                                       name="form_data[${ugtpId}][designation_1]" value="К1-К20">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="decoding1">Расшифровка</label>
                                <input type="text" class="form-control" id="decoding1"
                                       name="form_data[${ugtpId}][decoding_1]" value="Косвенный метод №1-№20">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-2"></div>
            </div>
            
            <div class="row">
                    <label class="col-sm-2 col-form-label">Рандомайзер</label>
                    <div class="col-sm-8">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="min">От:</label>
                                    <input type="number" class="form-control" id="min"
                                           name="form_data[${ugtpId}][min]"
                                           value="" placeholder="Мин. значение">
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="max">До:</label>
                                    <input type="number" class="form-control" id="max"
                                           name="form_data[${ugtpId}][max]"
                                           value="" placeholder="Макс. значение">
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-group">
                                    <label></label>
                                    <button type="button" class="btn btn-primary generate w-100">Генерировать</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            
            <div class="line-dashed-small"></div>

            <div class="row btn-wrapper">
                <div class="col-auto pe-0">
                    <button type="button" class="btn btn-primary calculate-g me-2">Рассчитать</button>
                </div>
                <div class="col-auto ps-0">
                    <button type="submit" class="btn btn-primary save">Сохранить</button>
                </div>
            </div>
        </div>`;
}