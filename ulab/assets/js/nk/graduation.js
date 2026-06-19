// Градуировочная зависимость
$(function ($) {
    const body = $('body');

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
     * Нахождение cуммы значений массива
     * @param nums
     * @returns {number|boolean}
     */
    function sum(nums) {
        if ( nums.length === '' || nums.length === undefined ) {
            return false
        }
        return nums.reduce((a, b) => (a + b));
    }

    /**
     * Сбросить значения
     * @param items
     */
    function resetValue(items) {
        items.each(function (index, item) {
            $(this).val("");
        });
    }

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

    /** Проверить на заполненость полей */
    function checkEmptyFields(fieldsSelector, errorWrapperSelector, resetSelector, messageError) {
        const wrapper = $(errorWrapperSelector),
            resetElement = $(resetSelector);

        let valueEmpty = $(fieldsSelector).filter(function () {
            return $(this).val() === null || $(this).val() === '';
        })

        wrapper.find(".messages").remove();

        if (valueEmpty.length) {
            let messageErrorContent = getMessageErrorContent(messageError)

            wrapper.prepend(messageErrorContent);
            resetValue(resetElement);
            return false;
        }

        return true;
    }

    function calculateMean() {
        const graduationWrapper = $('#graduationWrapper');
        const ONE = 1;

        let inputSingleValue1 = graduationWrapper.find('.single-value-1'),
            inputSingleValue2 = graduationWrapper.find('.single-value-2'),
            inputMeans = graduationWrapper.find('.mean'),
            inputHi = graduationWrapper.find('.Hi');
        let measuringDevice = graduationWrapper.find('#measuringDevice').val();

        let arrMean = [];
        inputSingleValue1.each(function (index, item) {
            let singleValue1 = +$(item).val(),
                singleValue2 = +$(inputSingleValue2[index]).val(),
                inputMean = $(inputMeans[index]),
                elemHi = $(inputHi[index]);

            // Среднее значение на участке (если УКС расчитываем среднее по 2 полям)
            if (measuringDevice === 'УКС') {
                let mean = (singleValue1 + singleValue2) / 2;
                let roundMean = round(mean, ONE);
                inputMean.val(roundMean.toFixed(ONE));

                // Для "Расчет коэффициента а"
                elemHi.val(roundMean.toFixed(ONE));

                arrMean.push(mean);
            } else {
                let roundMean = round(singleValue1, ONE);
                inputMean.val(roundMean.toFixed(ONE));

                // Для "Расчет коэффициента а"
                elemHi.val(roundMean.toFixed(ONE));

                arrMean.push(singleValue1);
            }
        });

        return arrMean;
    }

    /**
     * Коэффициент a
     * @param arrMean
     * @param Ro
     * @param X
     * @returns {number}
     */
    function calculateA(arrMean, Ro, X) {
        const graduationWrapper = $('#graduationWrapper'),
            wrapperA = graduationWrapper.find('.wrapper-a'),
            inputSumRiRfHiHf = wrapperA.find('.sumRiRfHiHf'),
            inputSumHiHf2 = wrapperA.find('.sumHiHf2'),
            inputSumRH = wrapperA.find('.sumRH');

        const TWO = 2,
            FOUR = 4;

        let arrRiRf = calculateRiRf(Ro);
        let arrHiHf = calculateHiHf(arrMean, X);
        let arrRiRfHiHf = calculateRiRfHiHf(arrRiRf, arrHiHf);

        let sumRiRfHiHf = sum(arrRiRfHiHf);
        let roundSumRiRfHiHf = round(sumRiRfHiHf, TWO);
        inputSumRiRfHiHf.val(roundSumRiRfHiHf.toFixed(TWO));

        let arrHiHf2 = calculateHiHf2(arrMean, arrHiHf);

        let sumHiHf2 = sum(arrHiHf2);
        let roundSumHiHf2 = round(sumHiHf2, TWO);
        inputSumHiHf2.val(roundSumHiHf2.toFixed(TWO));

        let arrRH = calculateRH(arrMean, arrRiRf, arrHiHf);

        let sumRH = sum(arrRH);
        let roundSumRH = round(sumRH, TWO);
        inputSumRH.val(roundSumRH.toFixed(TWO));

        let a = sumRH / sumHiHf2;
        let roundA = round(a, FOUR);
        $('#a').val(a);
        $('.round-a').val(roundA.toFixed(FOUR));

        return a;
    }

    /**
     * Разность Ri-Rф
     * @param Ro
     * @returns {[]}
     */
    function calculateRiRf(Ro) {
        const graduationWrapper = $('#graduationWrapper'),
            wrapperA = graduationWrapper.find('.wrapper-a'),
            inputRf = wrapperA.find('.Rf'),
            inputRiRf = wrapperA.find('.Ri-Rf');

        const ONE = 1,
            TWO = 2;

        let inputShearStrength = graduationWrapper.find('.shear-strength');
        let shearStrength = inputShearStrength.map(function (index, value) {
            return +$(value).val();
        }).get()

        inputRf.val(Ro.toFixed(ONE));

        let arrRiRf = [];
        for (let i in shearStrength) {
            let Ri = shearStrength[i],
                elemRiRf = inputRiRf[i];

            let RiRf = Ri - Ro;
            let roundRiRf = round(RiRf, TWO);
            $(elemRiRf).val(roundRiRf.toFixed(TWO));

            arrRiRf.push(RiRf);
        }

        return arrRiRf;
    }

    /**
     * Разность Hi-Hф
     * @param arrMean
     * @param X
     * @returns {[]}
     */
    function calculateHiHf(arrMean, X) {
        const graduationWrapper = $('#graduationWrapper'),
            wrapperA = graduationWrapper.find('.wrapper-a'),
            inputHf = wrapperA.find('.Hf'),
            inputHiHf = wrapperA.find('.Hi-Hf');

        const TWO = 2;

        inputHf.val(X.toFixed(TWO));

        let arrHiHf = [];
        for (let i in arrMean) {
            let Hi = arrMean[i],
                elemHiHf = inputHiHf[i];

            let HiHf = Hi - X;
            let roundHiHf = round(HiHf, TWO);
            $(elemHiHf).val(roundHiHf.toFixed(TWO));

            arrHiHf.push(HiHf);
        }

        return arrHiHf;
    }

    /**
     * произв. разностей
     * @param arrRiRf
     * @param arrHiHf
     * @returns {[]}
     */
    function calculateRiRfHiHf(arrRiRf, arrHiHf) {
        const graduationWrapper = $('#graduationWrapper'),
            wrapperA = graduationWrapper.find('.wrapper-a'),
            inputRiRfHiHf = wrapperA.find('.RiRfHiHf');

        const TWO = 2;

        let inputShearStrength = graduationWrapper.find('.shear-strength');
        let shearStrength = inputShearStrength.map(function (index, value) {
            return +$(value).val();
        }).get()

        let arrRiRfHiHf = [];
        for (let i in shearStrength) {
            let elemRiRfHiHf = inputRiRfHiHf[i];

            let RiRfHiHf = arrRiRf[i] * arrHiHf[i];
            let roundRiRfHiHf = round(RiRfHiHf, TWO);
            $(elemRiRfHiHf).val(roundRiRfHiHf.toFixed(TWO));

            arrRiRfHiHf.push(RiRfHiHf);
        }

        return arrRiRfHiHf;
    }

    /**
     * (Hi-Hф)^2
     * @param arrMean
     * @param arrHiHf
     * @returns {[]}
     */
    function calculateHiHf2(arrMean, arrHiHf) {
        const graduationWrapper = $('#graduationWrapper'),
            wrapperA = graduationWrapper.find('.wrapper-a'),
            inputHiHf2 = wrapperA.find('.HiHf2');

        const TWO = 2;

        let arrHiHf2 = [];
        for (let i in arrMean) {
            let elemHiHf2 = inputHiHf2[i];

            let HiHf2 = arrHiHf[i] ** 2;
            let roundHiHf2 = round(HiHf2, TWO);
            $(elemHiHf2).val(roundHiHf2.toFixed(TWO));

            arrHiHf2.push(HiHf2);
        }

        return arrHiHf2;
    }

    /**
     * R*H
     * @param arrMean
     * @param arrRiRf
     * @param arrHiHf
     * @returns {[]}
     */
    function calculateRH(arrMean, arrRiRf, arrHiHf) {
        const graduationWrapper = $('#graduationWrapper'),
            wrapperA = graduationWrapper.find('.wrapper-a'),
            inputRH = wrapperA.find('.RH');

        const TWO = 2;

        let arrRH = [];
        for (let i in arrMean) {
            let elemRH = inputRH[i];

            let RH = arrRiRf[i] * arrHiHf[i];
            let roundRH = round(RH, TWO);
            $(elemRH).val(roundRH.toFixed(TWO));

            arrRH.push(RH);
        }

        return arrRH;
    }

    /**
     * Прочность бетона по градуировочной зависимости, МПа
     * @param arrMean
     * @param a
     * @param b
     */
    function calculateGradationStrength(arrMean, a, b) {
        const graduationWrapper = $('#graduationWrapper');
        const ONE = 1;

        let inputGradationStrength = graduationWrapper.find('.gradation-strength'),
            inputGradationStrengthS = graduationWrapper.find('.gradation-strength-S');

        let arrGradationStrength = [];
        for (let i in arrMean) {
            let gradationStrength = arrMean[i] * a + b;
            let roundGradationStrength = round(gradationStrength, ONE);
            let elemGradationStrength = $(inputGradationStrength[i]);
            $(elemGradationStrength).val(roundGradationStrength.toFixed(ONE));
            arrGradationStrength.push(gradationStrength);

            // Для "Расчет коэффицента S (Sт.м.н.)"
            let elemGradationStrengthS = $(inputGradationStrengthS[i]);
            $(elemGradationStrengthS).val(roundGradationStrength.toFixed(ONE));
        }

        return arrGradationStrength;
    }

    /**
     * Riн
     * @param arrMean
     * @param a
     * @param b
     */
    function calculateGradationStrengthR(arrMean, a, b) {
        const graduationWrapper = $('#graduationWrapper');
        const TWO = 2;

        let inputRinr = graduationWrapper.find('.Rin-r');

        let arrGradationStrengthR = [];
        for (let i in arrMean) {
            let gradationStrength = arrMean[i] * a + b;

            // Для "Расчет коэффицента r"
            let roundGradationStrengthR = round(gradationStrength, TWO);
            let elemRinr = $(inputRinr[i]);
            $(elemRinr).val(roundGradationStrengthR.toFixed(TWO));

            arrGradationStrengthR.push(gradationStrength);
        }

        return arrGradationStrengthR;
    }

    /**
     * Остаточное среднеквадратическое отклонение S
     * @param arrGradationStrength
     * @returns {number}
     */
    function calculateS(arrGradationStrength) {
        const graduationWrapper = $('#graduationWrapper'),
            inputSumSqrS = graduationWrapper.find('#sumSqrS');
        const TWO = 2;

        let inputDifferenceS = graduationWrapper.find('.difference-S'),
            inputSqrDifferenceS = graduationWrapper.find('.sqr-difference-S');

        let inputShearStrength = graduationWrapper.find('.shear-strength');
        let shearStrength = inputShearStrength.map(function (index, value) {
            return +$(value).val();
        }).get();

        let arrSqrDifferenceS = [];
        for (let i in shearStrength) {
            let elemDifferenceS = inputDifferenceS[i],
                elemSqrDifferenceS = inputSqrDifferenceS[i];

            // Разность
            let differenceS = shearStrength[i] - arrGradationStrength[i];
            let roundDifferenceS = round(differenceS, TWO);
            $(elemDifferenceS).val(roundDifferenceS.toFixed(TWO));

            // Квадрат разности
            let sqrDifferenceS = differenceS ** 2;
            let roundSqrDifferenceS = round(sqrDifferenceS, TWO);
            $(elemSqrDifferenceS).val(roundSqrDifferenceS.toFixed(TWO));

            arrSqrDifferenceS.push(sqrDifferenceS);
        }

        let sumSqrS = sum(arrSqrDifferenceS);
        let roundSumSqrS = round(sumSqrS, TWO);
        inputSumSqrS.val(roundSumSqrS.toFixed(TWO));

        let S = Math.sqrt(sumSqrS / (arrSqrDifferenceS.length - 2));
        let roundS = round(S, TWO);
        $('#S').val(roundS.toFixed(TWO));
        $('.S').val(roundS.toFixed(TWO));

        return S;
    }

    /**
     * Коэффициент корреляции r
     * @param arrGradationStrengthR
     * @param Ro
     * @returns {number}
     */
    function calculateR(arrGradationStrengthR, Ro) {
        const graduationWrapper = $('#graduationWrapper'),
            inputRn = graduationWrapper.find('#Rn'),
            inputRnr = graduationWrapper.find('#Rnr');

        const ONE = 1,
            TWO = 2,
            THREE = 3;

        let inputRinRnr = graduationWrapper.find('.RinRn-r'),
            inputRinRn = graduationWrapper.find('.RinRn'),
            inputRinRn2 = graduationWrapper.find('.RinRn2'),
            inputRifRfr = graduationWrapper.find('.RifRf-r'),
            inputRifRf = graduationWrapper.find('.RifRf'),
            inputRifRf2 = graduationWrapper.find('.RifRf2'),
            inputRinRnRifRfr = graduationWrapper.find('.RinRnRifRf-r');

        let inputShearStrength = graduationWrapper.find('.shear-strength');
        let shearStrength = inputShearStrength.map(function (index, value) {
            return +$(value).val();
        }).get();

        let averGradationStrength = average(arrGradationStrengthR);
        let roundAverGradationStrength = round(averGradationStrength, TWO);
        inputRn.val(roundAverGradationStrength.toFixed(TWO));
        inputRnr.val(roundAverGradationStrength.toFixed(TWO));


        let arrRinRnRifRfr = [];
        let arrRinRn2 = [];
        let arrRifRf2 = [];
        for (let i in arrGradationStrengthR) {
            // Числитель
            let RinRnr =  arrGradationStrengthR[i] - averGradationStrength;
            let roundRinRnr = round(RinRnr, TWO);
            $(inputRinRnr[i]).val(roundRinRnr.toFixed(TWO));
            $(inputRinRn[i]).val(roundRinRnr.toFixed(TWO));

            let RifRfr =  shearStrength[i] - Ro;
            let roundRifRfr = round(RifRfr, ONE);
            $(inputRifRfr[i]).val(roundRifRfr.toFixed(ONE));
            $(inputRifRf[i]).val(roundRifRfr.toFixed(ONE));

            let RinRnRifRfr = RinRnr * RifRfr;
            let roundRinRnRifRfr = round(RinRnRifRfr, THREE);
            $(inputRinRnRifRfr[i]).val(roundRinRnRifRfr.toFixed(THREE));

            arrRinRnRifRfr.push(RinRnRifRfr);


            // Знаменатель
            let RinRn2 = RinRnr ** 2;
            let roundRinRn2 = round(RinRn2, TWO);
            $(inputRinRn2[i]).val(roundRinRn2.toFixed(TWO));

            let RifRf2 = RifRfr ** 2;
            let roundRifRf2 = round(RifRf2, TWO);
            $(inputRifRf2[i]).val(roundRifRf2.toFixed(TWO));

            arrRinRn2.push(RinRn2);
            arrRifRf2.push(RifRf2);
        }

        let sumRinRnRifRfr = sum(arrRinRnRifRfr);
        let roundRinRnRifRfr = round(sumRinRnRifRfr, THREE);
        $('#sumRinRnRifRfr').val(roundRinRnRifRfr.toFixed(THREE));

        let sumRinRn2 = sum(arrRinRn2);
        let roundSumRinRn2 = round(sumRinRn2, TWO);
        $('#sumRinRn2').val(roundSumRinRn2.toFixed(TWO));

        let sqrSumRinRn2 = Math.sqrt(sumRinRn2);
        let roundSqrSumRinRn2 = round(sqrSumRinRn2, TWO);
        $('#sqrSumRinRn2').val(roundSqrSumRinRn2.toFixed(TWO));

        let sumRifRf2 = sum(arrRifRf2);
        let roundSumRifRf2 = round(sumRifRf2, TWO);
        $('#sumRifRf2').val(roundSumRifRf2.toFixed(TWO));

        let sqrSumRifRf2 = Math.sqrt(sumRifRf2);
        let roundSqrSumRifRf2 = round(sqrSumRifRf2, TWO);
        $('#sqrSumRifRf2').val(roundSqrSumRifRf2.toFixed(TWO));

        let sqrSumRinRn2RifRf2 = sqrSumRinRn2 * sqrSumRifRf2;
        let roundSqrSumRinRn2RifRf2 = round(sqrSumRinRn2RifRf2, THREE);
        $('#sqrSumRinRn2RifRf2').val(roundSqrSumRinRn2RifRf2.toFixed(THREE));

        let r = sumRinRnRifRfr / sqrSumRinRn2RifRf2;
        let roundR = round(r, THREE);
        $('#r').val(roundR.toFixed(THREE));
        $('.r').val(roundR.toFixed(THREE));

        return r;
    }

    let chartData;
    function showChart() {
        // Очищаем график
        destroyChart();
        $('#chart').attr("src", '');

        // Проверка заполненость полей "Среднее значение на участке" не расчитано"
        let checkMean =
            checkEmptyFields(
                `.mean`,
                `#graduationWrapper`,
                '',
                'Внимание! Поле "Среднее значение на участке" не расчитано!'
            );

        if (!checkMean) {
            return false;
        }

        // Проверка заполненость полей "Прочность бетона на участке методом отрыва со скалыванием" поле 2
        let checkShearStrength =
            checkEmptyFields(
                `.shear-strength`,
                `#graduationWrapper`,
                '',
                'Внимание! Поле "Прочность бетона на участке методом отрыва со скалыванием" не расчитано!'
            );
        if (!checkShearStrength) {
            return false;
        }

        let inputMean = $('.mean'),
            inputShearStrength = $('.shear-strength');

        let xyValues = inputMean.map(function (index, value) {
            let mean = +$(value).val(),
                shearStrength = +$(inputShearStrength[index]).val();

            return {x:mean, y:shearStrength}
        }).get();

        let a = +$('#a').val(),
            roundA = round(+$('.round-a').val(), 2),
            b = +$('#b').val(),
            roundB = +$('#round-b').val();

        let measuringDevice = $('#measuringDevice').val(),
            pointRadius = $('#pointRadius').val(),
            minAxisY = $('#minAxisY').val(),
            maxAxisY = $('#maxAxisY').val(),
            yMain = $('#yMain').val(),
            minAxisX = $('#minAxisX').val(),
            maxAxisX = $('#maxAxisX').val(),
            xMain = $('#xMain').val();

        let applyPointRadius = pointRadius === '' ? 4 : +pointRadius,
            applyMinY = minAxisY === '' ? null : +minAxisY,
            applyMaxY = maxAxisY === '' ? null : +maxAxisY,
            applyMainY = yMain === '' ? null : +yMain,
            applyMinX = minAxisX === '' ? null : +minAxisX,
            applyMaxX = maxAxisX === '' ? null : +maxAxisX,
            applyMainX = xMain === '' ? null : +xMain;

        let applyMeasuringDevice = measuringDevice === 'УКС' ? 'Скорость распространения ультразвука, м/с' :
            (measuringDevice === 'ИПС' ? 'Прочность бетона на сжатие, ИПС, МПа' : '');


        const ctx = $('#myChart');
        let myChart = new Chart(ctx, {
            type: "scatter",
            data: {
                datasets: [{
                    pointRadius: applyPointRadius,
                    pointBackgroundColor: "rgb(173,216,230)",
                    data: xyValues,
                    borderColor: "rgba(169,169,169, .6)",
                    trendlineLinear: {
                        lineStyle: "solid",
                        width: 2,
                        scale: a,
                        offset: b,
                        calculate: false, // true - вычисляет scale и offset автоматически из данных X и Y
                    }
                }]
            },
            options: {
                plugins: {
                    legend: {
                        display: false
                    },
                    title: {
                        display: true,
                        text: 'Градуировочная зависимость',
                        font: {
                            size: 25
                        },
                        padding: {
                            bottom: 30
                        }
                    },
                },
                scales: {
                    y: {
                        min: applyMinY,
                        max: applyMaxY,
                        title: {
                            display: true,
                            text: ['Прочность бетона по результатам испытаний', 'методом отрыва со скалыванием, МПа'],
                            font: {
                                size: 16
                            }
                        },
                        ticks: {
                            font: {
                                size: 15,
                            },
                            stepSize: applyMainY,
                        },
                    },
                    x: {
                        min: applyMinX,
                        max: applyMaxX,
                        title: {
                            display: true,
                            text: applyMeasuringDevice,
                            font: {
                                size: 18
                            }
                        },
                        ticks: {
                            font: {
                                size: 15,
                            },
                            stepSize: applyMainX,
                        }
                    }
                },
                layout: {
                    padding: {
                        right: 20
                    }
                },
                animation: {
                    onComplete: function () {
                        let myImage = myChart.toBase64Image();

                        $('input[name="chart"]').val(myImage);
                        $('#chart').attr("src",myImage);
                    },
                },
            },
            plugins: [{
                id: 'colorArea',
                beforeDraw: function (chart, args, plugins) {
                    const { ctx, chartArea: { left, top} } = chart

                    ctx.save();
                    ctx.fillStyle = "#ffffff";
                    ctx.fillRect(0, 0, chart.width, chart.height);
                    ctx.fillStyle = "rgb(33,37,41,0.7)";
                    ctx.font = "15px Verdana";
                    ctx.fillText(`y = ${roundA.toFixed(2)}H`+(roundB > 0 ? `+${roundB}` : roundB), left, top - 10);
                    ctx.restore();
                }
            }]
        });

        chartData = myChart;
    }
    showChart();

    /**
     * Очистить граффик
     */
    function destroyChart() {
        if (chartData) {
            chartData.destroy();
        }
    }


    // Прибор для замеров
    body.on('change', '#measuringDevice', function (e) {
        const inputReadonly = $(".calculations-wrapper input[readonly]:not('.do-not-clean')");
        resetValue(inputReadonly);

        // Очищаем график
        destroyChart();
        $('#chart').attr("src", '');

        let measuringDevice = $(this).val();

        if (measuringDevice === 'УКС') {
            $('.single-value-2').prop('disabled', false);
            $('.single-value-2').removeClass('bg-light-secondary');

            if (!$('.single-value-2').hasClass('bg-white')) {
                $('.single-value-2').addClass('bg-white');
            }
        } else if(measuringDevice === 'ИПС') {
            $('.single-value-2').val('');
            $('.single-value-2').prop('disabled', true);
            $('.single-value-2').removeClass('bg-white');

            if (!$('.single-value-2').hasClass('bg-light-secondary')) {
                $('.single-value-2').addClass('bg-light-secondary');
            }
        }
    });

    body.on('click', '.calculate', function (e) {
        e.preventDefault();
        const graduationWrapper = $('#graduationWrapper');

        const ONE = 1,
            TWO = 2;

        let inputShearStrength = graduationWrapper.find('.shear-strength'),
            inputX = graduationWrapper.find('#x'),
            inputRo = graduationWrapper.find('#Ro');

        let measuringDevice = graduationWrapper.find('#measuringDevice').val();
        let shearStrength = inputShearStrength.map(function (index, value) {
            return +$(value).val();
        }).get()

        if (measuringDevice === 'УКС' || measuringDevice === 'ИПС') {
            // Проверка заполненость полей "Единичные значения" поле 1
            let checkAfterBefore =
                checkEmptyFields(
                    `.single-value-1`,
                    `#graduationWrapper`,
                    `#graduationWrapper input[readonly]:not('.do-not-clean')`,
                    'Внимание! Не все поля "Единичные значения" заполнены!'
                );
            if (!checkAfterBefore) {
                window.scrollTo(0,0);
                return false;
            }
        }

        if (measuringDevice === 'УКС') {
            // Проверка заполненость полей "Единичные значения" поле 2
            let checkAfterBefore =
                checkEmptyFields(
                    `.single-value-2`,
                    `#graduationWrapper`,
                    `#graduationWrapper input[readonly]:not('.do-not-clean')`,
                    'Внимание! Не все поля "Единичные значения" заполнены!'
                );
            if (!checkAfterBefore) {
                window.scrollTo(0,0);
                return false;
            }
        }

        let arrMean = calculateMean();

        let x = average(arrMean);
        let roundX = round(x, ONE);
        inputX.val(roundX.toFixed(ONE));

        // Проверка заполненость полей "Прочность бетона на участке методом отрыва со скалыванием, МПа"
        let checkAfterBefore =
            checkEmptyFields(
                `.shear-strength`,
                `#graduationWrapper`,
                `#graduationWrapper input[readonly]:not('.do-not-clean')`,
                'Внимание! Не все поля "Прочность бетона на участке методом отрыва со скалыванием, МПа" заполнены!'
            );
        if (!checkAfterBefore) {
            window.scrollTo(0,0);
            return false;
        }

        let Ro = average(shearStrength);
        let roundRo = round(Ro, ONE);
        inputRo.val(roundRo.toFixed(ONE));

        // Расчет коэффициента а
        let a = calculateA(arrMean, Ro, x);

        const inputB = graduationWrapper.find('#b');
        const inputRoundB = graduationWrapper.find('#round-b');
        let b = Ro - a * x;
        let roundB = round(b, TWO);
        inputB.val(b);
        inputRoundB.val(roundB.toFixed(TWO));

        // Прочность бетона по градуировочной зависимости, МПа
        let arrGradationStrength = calculateGradationStrength(arrMean, a, b);
        let arrGradationStrengthR = calculateGradationStrengthR(arrMean, a, b);

        // Расчет коэффицента S (Sт.м.н.)
        let S = calculateS(arrGradationStrength);

        // Условие отбраковки единичных результатов испытаний |RiH-Riф|/S
        let inputCondition = graduationWrapper.find('.condition');
        for (let i in shearStrength) {
            let elemCondition = inputCondition[i];
            let condition = Math.abs((arrGradationStrength[i] - shearStrength[i]) / S);
            let roundCondition = round(condition, TWO);

            $elemCondition = $(elemCondition);
            $elemCondition.val(roundCondition.toFixed(TWO));

            if (condition > 2) {
                $elemCondition.removeClass('bg-light-secondary');
                if (!$elemCondition.hasClass('bg-danger')) {
                    $elemCondition.addClass('bg-danger');
                }
            } else {
                $elemCondition.removeClass('bg-danger');
                if (!$elemCondition.hasClass('bg-light-secondary')) {
                    $elemCondition.addClass('bg-light-secondary');
                }
            }
        }

        let SR = S / Ro;
        let roundSR = round(SR, TWO);
        $('#SR').val(roundSR.toFixed(TWO));

        let inputRfr = graduationWrapper.find('#Rfr');
        inputRfr.val(roundRo.toFixed(ONE));

        // Расчет коэффицента r
        let r = calculateR(arrGradationStrengthR, Ro);

        let SR015 = SR < 0.15 ? 'Соответствует' : 'Не соответствует';
        $('#SR015').val(SR015);

        let r07 = r > 0.7 ? 'Соответствует' : 'Не соответствует';
        $('#r07').val(r07);

        showChart();
    });

    /**
     * Добавить конструкцию
     */
    body.on('click', '.add-construction', function () {
        // Расчет для конструкций на объекте строительства класса бетона по прочности на сжатие
        let constructionRow = $(this).closest('.construction-row'),
            cloneConstructionRow = $(constructionRow).clone(true),
            constructionWrapper = constructionRow.closest('.construction-wrapper');

        const inputReadonly = cloneConstructionRow.find('input');
        resetValue(inputReadonly);

        cloneConstructionRow.find('.add-construction').replaceWith(
            `<button type="button" class="btn btn-danger del-construction mt-0 btn-square">
                 <i class="fa-solid fa-minus icon-fix"></i>
            </button>`
        );

        constructionWrapper.append(cloneConstructionRow);


        // Расчет коэффициента а
        const wrapperRiRf = $('.wrapper-Ri-Rf'),
            constructionRowRiRf = wrapperRiRf.find('.construction-row:last'),
            cloneConstructionRowRiRf = $(constructionRowRiRf).clone(true),
            constructionWrapperRiRf = wrapperRiRf.find('.construction-wrapper');

        const inputReadonlyRiRf = cloneConstructionRowRiRf.find('input');
        resetValue(inputReadonlyRiRf);

        constructionWrapperRiRf.append(cloneConstructionRowRiRf);


        const wrapperHiHf = $('.wrapper-Hi-Hf'),
            constructionRowHiHf = wrapperHiHf.find('.construction-row:last'),
            cloneConstructionRowHiHf = $(constructionRowHiHf).clone(true),
            constructionWrapperHiHf = wrapperHiHf.find('.construction-wrapper');

        const inputReadonlyHiHf = cloneConstructionRowHiHf.find('input');
        resetValue(inputReadonlyHiHf);

        constructionWrapperHiHf.append(cloneConstructionRowHiHf);


        const wrapperRiRfHiHf = $('.wrapper-RiRfHiHf'),
            constructionRowRiRfHiHf = wrapperRiRfHiHf.find('.construction-row:last'),
            cloneConstructionRowRiRfHiHf = $(constructionRowRiRfHiHf).clone(true),
            constructionWrapperRiRfHiHf = wrapperRiRfHiHf.find('.construction-wrapper');

        const inputReadonlyRiRfHiHf = cloneConstructionRowRiRfHiHf.find('input');
        resetValue(inputReadonlyRiRfHiHf);
        cloneConstructionRowRiRfHiHf.find('.sumRiRfHiHf').remove();

        constructionWrapperRiRfHiHf.append(cloneConstructionRowRiRfHiHf);


        const wrapperHiHf2 = $('.wrapper-HiHf2'),
            constructionRowHiHf2 = wrapperHiHf2.find('.construction-row:last'),
            cloneConstructionRowHiHf2 = $(constructionRowHiHf2).clone(true),
            constructionWrapperHiHf2 = wrapperHiHf2.find('.construction-wrapper');

        const inputReadonlyHiHf2 = cloneConstructionRowHiHf2.find('input');
        resetValue(inputReadonlyHiHf2);
        cloneConstructionRowHiHf2.find('.sumHiHf2').remove();

        constructionWrapperHiHf2.append(cloneConstructionRowHiHf2);


        const wrapperRH = $('.wrapper-RH'),
            constructionRowRH = wrapperRH.find('.construction-row:last'),
            cloneConstructionRowRH = $(constructionRowRH).clone(true),
            constructionWrapperRH = wrapperRH.find('.construction-wrapper');

        const inputReadonlyRH = cloneConstructionRowRH.find('input');
        resetValue(inputReadonlyRH);
        cloneConstructionRowRH.find('.sumRH').remove();

        constructionWrapperRH.append(cloneConstructionRowRH);


        // Расчет коэффицента S (Sт.м.н.)
        const wrapperSumSqr = $('.wrapper-sum-sqr'),
            constructionRowSumSqr = wrapperSumSqr.find('.construction-row:last'),
            cloneConstructionRowSumSqr = $(constructionRowSumSqr).clone(true),
            constructionWrapperSumSqr = wrapperSumSqr.find('.construction-wrapper');

        const inputReadonlySumSqr = cloneConstructionRowSumSqr.find('input');
        resetValue(inputReadonlySumSqr);
        cloneConstructionRowSumSqr.find('.sumSqrS').remove();

        constructionWrapperSumSqr.append(cloneConstructionRowSumSqr);


        // Расчет коэффицента r
        const wrapperNumeratorR = $('.wrapper-numerator-r'),
            constructionRowNumeratorR = wrapperNumeratorR.find('.construction-row:last'),
            cloneConstructionRowNumeratorR = $(constructionRowNumeratorR).clone(true),
            constructionWrapperNumeratorR = wrapperNumeratorR.find('.construction-wrapper');

        const inputReadonlyNumeratorR = cloneConstructionRowNumeratorR.find('input');
        resetValue(inputReadonlyNumeratorR);
        cloneConstructionRowNumeratorR.find('.Rn-r').remove();
        cloneConstructionRowNumeratorR.find('#Rfr').remove();
        cloneConstructionRowNumeratorR.find('#sumRinRnRifRfr').remove();

        constructionWrapperNumeratorR.append(cloneConstructionRowNumeratorR);


        const wrapperDenominatorR = $('.wrapper-denominator-r'),
            constructionRowDenominatorR = wrapperDenominatorR.find('.construction-row:last'),
            cloneConstructionRowDenominatorR = $(constructionRowDenominatorR).clone(true),
            constructionWrapperDenominatorR = wrapperDenominatorR.find('.construction-wrapper');

        const inputReadonlyDenominatorR = cloneConstructionRowDenominatorR.find('input');
        resetValue(inputReadonlyDenominatorR);
        cloneConstructionRowDenominatorR.find('#sumRinRn2').remove();
        cloneConstructionRowDenominatorR.find('#sqrSumRinRn2').remove();
        cloneConstructionRowDenominatorR.find('#sumRifRf2').remove();
        cloneConstructionRowDenominatorR.find('#sqrSumRifRf2').remove();
        cloneConstructionRowDenominatorR.find('#sqrSumRinRn2RifRf2').remove();

        constructionWrapperDenominatorR.append(cloneConstructionRowDenominatorR);


        const inputReadonlyAll = $(".calculations-wrapper input[readonly]:not('.do-not-clean')");
        resetValue(inputReadonlyAll);
        $('.reset-select').val('');
    });

    /**
     * Удалить конструкцию
     */
    body.on('click', '.del-construction', function () {
        // Расчет для конструкций на объекте строительства класса бетона по прочности на сжатие
        let constructionRow = $(this).closest('.construction-row');
        let i = constructionRow.index();
        constructionRow.remove();


        // Расчет коэффициента а
        const wrapperRiRf = $('.wrapper-Ri-Rf');
        let constructionRowRiRf = wrapperRiRf.find('.construction-row').eq(i);
        constructionRowRiRf.remove();

        const wrapperHiHf = $('.wrapper-Hi-Hf');
        let constructionRowHiHf = wrapperHiHf.find('.construction-row').eq(i);
        constructionRowHiHf.remove();

        const wrapperRiRfHiHf = $('.wrapper-RiRfHiHf');
        let constructionRowRiRfHiHf = wrapperRiRfHiHf.find('.construction-row').eq(i);
        constructionRowRiRfHiHf.remove();

        const wrapperHiHf2 = $('.wrapper-HiHf2');
        let constructionRowHiHf2 = wrapperHiHf2.find('.construction-row').eq(i);
        constructionRowHiHf2.remove();

        const wrapperRH = $('.wrapper-RH');
        let constructionRowRH = wrapperRH.find('.construction-row').eq(i);
        constructionRowRH.remove();

        // Расчет коэффицента S (Sт.м.н.)
        const wrapperSumSqr = $('.wrapper-sum-sqr');
        let constructionRowSumSqr = wrapperSumSqr.find('.construction-row').eq(i);
        constructionRowSumSqr.remove();

        // Расчет коэффицента r
        const wrapperNumeratorR = $('.wrapper-numerator-r');
        let constructionRowNumeratorR = wrapperNumeratorR.find('.construction-row').eq(i);
        constructionRowNumeratorR.remove();

        const wrapperDenominatorR = $('.wrapper-denominator-r');
        let constructionRowDenominatorR = wrapperDenominatorR.find('.construction-row').eq(i);
        constructionRowDenominatorR.remove();

        const inputReadonly = $(".calculations-wrapper input[readonly]:not('.do-not-clean')");
        resetValue(inputReadonly);
        $('.reset-select').val('');

        // Очищаем график
        destroyChart();
        $('#chart').attr("src", '');
    });

    // Прочность бетона на участке методом отрыва со скалыванием, МПа
    body.on("input", ".shear-strength", function () {
        let value = $(this).val(),
            i = $('.shear-strength').index(this);

        // Расчет коэффициента а
        const wrapperA = $('.wrapper-a'),
            inputRi = wrapperA.find('.Ri');
        $(inputRi[i]).val(value);

        // Расчет коэффицента S (Sт.м.н.)
        const wrapperS = $('.wrapper-S'),
            shearStrengthS = wrapperS.find('.shear-strength-S');
        $(shearStrengthS[i]).val(value);

        // Расчет коэффицента r
        const wrapperR = $('.wrapper-r');
        let Rifr = wrapperR.find('.Rif-r');
        $(Rifr[i]).val(value);
    });

    body.on('input', '#pointRadius, #minAxisY, #maxAxisY, #yMain, #minAxisX, #maxAxisX, #xMain', function () {
        showChart();
    });

    /** Редактирование */
    body.on("input", ".calculations-wrapper input:not([readonly])", function () {
        const inputReadonly = $(".calculations-wrapper input[readonly]:not('.do-not-clean')");
        resetValue(inputReadonly);
        $('.reset-select').val('');

        // Очищаем график
        destroyChart();
        $('#chart').attr("src", '');
    });
});