$(function ($) {
    const body = $('body');

    /**
     * Нахождение среднего арифметического
     */
    function average(nums) {
        if (nums.length === 0 || nums.length === undefined) {
            return false;
        }
        return nums.reduce((a, b) => (a + b)) / nums.length;
    }

    /**
     * Нахождение суммы значений массива
     */
    function sum(nums) {
        if (nums.length === 0 || nums.length === undefined) {
            return false;
        }
        return nums.reduce((a, b) => (a + b));
    }

    /**
     * Округление
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
     * Сбросить значения
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

    /**
     * Расчет среднего значения в зависимости от выбранного типа
     */
    function calculateAverage(actualValues, calculationType) {
        const values = [...actualValues].sort((a, b) => b - a); // Сортируем по убыванию
        let sum = values.reduce(function (a, b) {
            return a + b
        }, 0).toFixed(8)

        console.log(sum)

        switch (calculationType) {
            case 'top2':
                return values.length >= 2 ? average(values.slice(0, 2)) : average(values);
            case 'top3':
                return values.length >= 3 ? average(values.slice(0, 3)) :
                    values.length === 2 ? average(values) : values[0];
            case 'top4':
                return values.length >= 4 ? average(values.slice(0, 4)) :
                    values.length === 3 ? average(values) :
                        values.length === 2 ? average(values) : values[0];
            case 'simple':
            default:
                return sum / values.length;
        }
    }

    /** Обработчик расчета среднего значения */
    body.on('click', '.calculate-average', function (e) {
        e.preventDefault();
        const averageWrapper = $('#averageWrapper'),
            inputAverageValue = averageWrapper.find('#averageValue'),
            inputActualValue = averageWrapper.find('.actual-value'),
            calculationType = averageWrapper.find('#calculationType').val();

        let decimalPlaces = +averageWrapper.find('#decimalPlaces').val();

        // Проверка на заполненность всех полей
        let actualValueEmpty = inputActualValue.filter(function () {
            return $(this).val() === null || $(this).val() === '';
        });

        averageWrapper.find(".messages").remove();

        if (actualValueEmpty.length) {
            let messageError = "Внимание! Для расчета значений заполните все фактические значения!";
            let messageErrorContent = getMessageErrorContent(messageError);
            averageWrapper.prepend(messageErrorContent);
            resetValue(inputAverageValue);
            return false;
        }

        // Получаем массив значений
        let actualValue = inputActualValue.map(function (index, value) {
            return +$(value).val();
        }).get();

        // Вычисляем среднее значение в зависимости от выбранного типа
        let averageValue = calculateAverage(actualValue, calculationType);
        // let roundAverage = round(averageValue, decimalPlaces);

        // let roundAverage = averageValue.toFixed(decimalPlaces);

        console.log(averageValue)

        let rounded = Math.round(averageValue * (10 ** decimalPlaces)) / (10 ** decimalPlaces)
        let roundAverage = rounded.toFixed(decimalPlaces)

        inputAverageValue.val(roundAverage);
    });


});
