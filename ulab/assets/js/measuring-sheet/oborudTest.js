// Методика с расчетом среднего значения
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

        let averageValue = average(actualValue);
        let roundAverage = round(averageValue, decimalPlaces);

        if (decimalPlaces) {
            roundAverage = roundAverage.toFixed(decimalPlaces);
        }
        inputAverageValue.val(roundAverage);
    });

    body.on('click', '.random_num', function (e) {
        let maxCell = getRandomInt(2, 2),
            num1 = ''
        console.log(maxCell)
        for (let i = 0; i < maxCell; i++) {
            let min = Math.abs(num1 - 1),
                max = num1 + 1,
                num = Math.random() + Math.floor(Math.random() * (max - min) + min)
                num1 = round(num, 1)

            let actualValueWrapper = $('.actual-value-wrapper').last(),
                cloneActualValueWrapper = $(actualValueWrapper).clone(true),
                tdActualValue = actualValueWrapper.closest('.td-actual-value');

            cloneActualValueWrapper.find('.actual-value').val(round(num, 1));

            cloneActualValueWrapper.find('.add-value-actual').replaceWith(
                `<button type="button" class="btn btn-danger del-value-actual mt-0 btn-square">
                 <i class="fa-solid fa-minus icon-fix"></i>
            </button>`
            );

            tdActualValue.append(cloneActualValueWrapper);
        }

        $('.actual-value-wrapper').first().remove()

        $('.calculate-average').trigger('click')

    })


});

function getRandomInt(min, max) {
    min = Math.ceil(min);
    max = Math.floor(max);
    return Math.floor(Math.random() * (max - min) + min); // Максимум не включается, минимум включается
}
