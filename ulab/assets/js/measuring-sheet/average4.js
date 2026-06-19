// Методика с расчетом среднего значения по 4 наибольшим
$(function ($) {
    const body = $('body');

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
        if ( nums.length === 0 || nums.length === undefined ) {
            return false
        }
        return nums.reduce((a, b) => (a + b));
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

    /** Расчитать среднее значение */
    body.on('click', '.calculate-average', function (e) {
        e.preventDefault();
        const averageWrapper = $('#averageWrapper'),
            inputAverageValue = averageWrapper.find('#averageValue'),
            inputActualValue = averageWrapper.find('.actual-value');
        let methodType = averageWrapper.find('#methodType').val(),
            decimalPlaces = +averageWrapper.find('#decimalPlaces').val();

        let actualValueEmpty = inputActualValue.filter(function () {
            return $(this).val() === null || $(this).val() === '';
        })

        averageWrapper.find(".messages").remove();

        if (actualValueEmpty.length) {
            let messageError =
                "Внимание! Для расчета значений заполните все фактические значения!";

            let messageErrorContent = getMessageErrorContent(messageError)

            averageWrapper.prepend(messageErrorContent)
            resetValue(inputAverageValue)
            return false;
        }

        let actualValue = inputActualValue.map(function (index, value) {
            return +$(value).val()
        }).get()

        let sumActualValue = sum(actualValue);
        let minActualValue = Math.min.apply(Math,actualValue);
     
        let indexActualValue = actualValue.indexOf(minActualValue);
        if (indexActualValue !== -1) {
            actualValue.splice(indexActualValue, 1);
        }

        let averageValue =
            (sumActualValue - minActualValue - Math.min.apply(Math,actualValue)) / (actualValue.length - 1);

        let roundAverage = round(averageValue, decimalPlaces);

        if (decimalPlaces) {
            roundAverage = roundAverage.toFixed(decimalPlaces);
        }
        inputAverageValue.val(roundAverage);
    });

});