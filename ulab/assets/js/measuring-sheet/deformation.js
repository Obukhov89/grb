$(document).ready(function() {
    const ugtpId = $('#ugtpId').val();

    // Функция расчета отличия от 11-го измерения
    function calculateDifference(measurementNum, eleventhValue) {
        if (!eleventhValue || eleventhValue == 0) return '';

        // Получаем значение для текущего измерения
        let currentValue = $(`input[data-measurement="${measurementNum}"]`).val();
        if (!currentValue) return '';

        // Расчет по формуле: ((11 - N) / 11) * 100
        let difference = ((parseFloat(eleventhValue) - parseFloat(currentValue)) / parseFloat(eleventhValue)) * 100;
        return difference.toFixed(2);
    }

    // Функция формирования заключения
    function generateConclusion() {
        let eleventhValue = $(`input[data-measurement="11"]`).val();

        if (!eleventhValue) {
            $('#conclusionText').text('Заключение будет сформировано автоматически после ввода данных');
            $('#conclusionHidden').val('');
            return;
        }

        let eleventhVal = parseFloat(eleventhValue);

        // Проверяем отличия для строк 8, 9, 10
        let differences = [];
        let allWithinLimit = true;

        for (let i = 8; i <= 10; i++) {
            let diff = $(`input[data-measurement="${i}"].difference-input`).val();
            if (diff) {
                let diffVal = parseFloat(diff);
                differences.push(diffVal);
                if (isNaN(diffVal) || Math.abs(diffVal) > 15) {
                    allWithinLimit = false;
                }
            } else {
                allWithinLimit = false;
            }
        }

        let conclusion = '';

        if (eleventhVal < 0.1 && allWithinLimit) {
            conclusion = 'Материал считается нереакционноспособным по отношению к щелочам, так как величина деформации расширения образцов в растворе гидроксида натрия составляет менее 0,1 %, при условии, что последний (одиннадцатый) результат испытания отличается от трёх предшествующих результатов измерений не более чем на 15 %.';
        } else if (eleventhVal > 0.1) {
            conclusion = 'Материал считается потенциально реакционноспособным со щелочами, так как величина деформации расширения образцов в растворе гидроксида натрия составляет более 0,1, и возможность использования его в качестве заполнителей для бетона определяется испытанием образцов бетона.';
        } else if (eleventhVal == 0.1) {
            conclusion = 'Заключение о реакционной способности не может быть сделано на основании текущих данных.';
        } else if (!allWithinLimit) {
            conclusion = 'Заключение о реакционной способности не может быть сделано на основании текущих данных.';
        }

        if (conclusion) {
            $('#conclusionText').text(conclusion);
            $('#conclusionHidden').val(conclusion);
        }
    }

    // Обновление всех отличий для строк 8, 9, 10
    function updateDifferences() {
        let eleventhValue = $(`input[data-measurement="11"]`).val();

        if (eleventhValue) {
            for (let i = 8; i <= 10; i++) {
                let diff = calculateDifference(i, eleventhValue);
                $(`input[data-measurement="${i}"].difference-input`).val(diff.toString().replace('.', ','));
            }

            // Генерируем заключение после обновления отличий
            generateConclusion();
        }
    }

    // Обработчик изменения значений деформации
    $(document).on('input', '.deformation-input', function() {
        let measurementNum = $(this).data('measurement');

        // Если это 11-е измерение, обновляем все отличия
        if (measurementNum == 11) {
            updateDifferences();
        } else {
            // Если изменились другие измерения, тоже пересчитываем (может влиять на отличия)
            updateDifferences();
        }
    });

    // Проверка перед отправкой формы
    $('.save-deformation').on('click', function(e) {
        let eleventhValue = $(`input[data-measurement="11"]`).val();
        if (!eleventhValue) {
            alert('Необходимо указать значение для 11-го измерения');
            e.preventDefault();
            return false;
        }
    });

    // Инициализация при загрузке страницы, если есть сохраненные данные
    if ($(`input[data-measurement="11"]`).val()) {
        updateDifferences();
    }
});