
$(function ($) {
	$(".sub").on("click", function () {

		let result = $('.result-formula')

		let obj = {}
		$.each($(".isFormula"), function (a,b){
			var formData = $('#dynamic-form').find('.variable')
			let formula = $(b).data("formula")
			let dec = 2
			let functionName = formula.split("(")[0]; // "AVERAGE"
			if (functionName === "AVERAGE") {
				$(b).val(splitAverage(formula).toFixed(2))
				$(b).addClass('variable')
				$(b).removeClass('isFormula')
				return true
			}

			if (functionName === "_xlfn.VAR.S") {
				$(b).val(variance(formula))
				$(b).addClass('variable')
				$(b).removeClass('isFormula')
				return true
			}


			if (functionName === "SUM") {
				$(b).val(splitSum(formula).toFixed(2))
				$(b).addClass('variable')
				$(b).removeClass('isFormula')
				return true
			}


			if (functionName === "_xlfn.STDEV.S") {
				$(b).val(standartDeviationPopulation(formula))
				$(b).addClass('variable')
				$(b).removeClass('isFormula')
				return true
			}

			if (functionName === "ROUND") {

				let dec = formula.match(/^ROUND\((.*),\s*(\d)/)[2]; // "B2:B7"
				formula = formula.match(/^ROUND\((.*),\s*(\d)/)[1]; // "B2:B7"
			}

			$.each(formData, function (i, v) {
				let regex = new RegExp(`${$(v).attr('name')}\\b`, 'g'); // Регулярное выражение для поиска всех вхождений аргумента
				// let regex = new RegExp(`([A-Z]+\d+)`, 'g'); // Регулярное выражение для поиска всех вхождений аргумента
				// console.log(regex)
				// formula = formula.replace(regex, $(v).val()); // Замена аргумента на соответствующую переменную
				formula = formula.replace(regex, $(v).val()); // Замена аргумента на соответствующую переменную
				formula = formula.replace('SQRT', 'Math.sqrt'); // Замена аргумента на соответствующую переменную
				formula = formula.replace('^', '**'); // Замена аргумента на соответствующую переменную
			})

			$(b).val(eval(formula).toFixed(dec))
			$(b).addClass('variable')
			$(b).removeClass('isFormula')
		})

	});

	function splitAverage(formula)
	{
		// let functionName = formula.split("(")[0]; // "AVERAGE"
		let rangeString = formula.match(/\(([^)]+)\)/)[1]; // "B2:B7"
		let values = getValuesFromRange(rangeString);

		// if (functionName === "AVERAGE") {
			const result = average(values);
		// }
		return result
	}

	function splitSum(formula)
	{
		// let functionName = formula.split("(")[0]; // "AVERAGE"
		let rangeString = formula.match(/\(([^)]+)\)/)[1]; // "B2:B7"
		let values = getValuesFromRange(rangeString);
		const result = average(values);
		return result * values.length
	}

	function calculateAverage(values) {
		if (values.length === 0) return 0;

		let sum = 0;
		for (let i = 0; i < values.length; i++) {
			sum += values[i];
		}

		return sum / values.length;
	}

	/**
	 * Функция для преобразования строкового представления диапазона ячеек в массив значений
	 */
	function getValuesFromRange(rangeString) {
		const startCell = rangeString.split(":")[0].toUpperCase(); // B2
		const endCell = rangeString.split(":")[1].toUpperCase();   // B7

		const rowsStart = Number(startCell.replace(/[^\d]/g, "")); // 2
		const rowsEnd = Number(endCell.replace(/[^\d]/g, ""));     // 7

		const colStart = startCell.replace(/[\d]/g, "");          // B
		const colEnd = endCell.replace(/[\d]/g, "");              // B

		const values = [];

		for (let row = rowsStart; row <= rowsEnd; row++) {
			for (let col = colStart.charCodeAt(0); col <= colEnd.charCodeAt(0); col++) {
				const cellValue = getCellValue(String.fromCharCode(col) + row); // Это должна быть ваша функция для получения значения ячейки

				values.push(parseFloat(cellValue));
			}
		}

		return values;
	}

	function getCellValue(cellAddress) {
		let value = $(`[name="${cellAddress}"]`).val()
		return value; // Пример возвращаемого значения
	}

	function variance(formula) {
		let rangeString = formula.match(/\(([^)]+)\)/)[1]; // "B2:B7"
		let values = getValuesFromRange(rangeString);

		// Проверка на корректность входных данных
		if (!Array.isArray(values) || values.length === 0) return NaN;

		let sum = 0,
			mean = 0,
			variance = 0;

		// Вычисление среднего значения массива
		for (let i = 0; i < values.length; ++i) {
			sum += values[i];
		}
		mean = sum / values.length;

		// Вычисление дисперсии
		for (let j = 0; j < values.length; ++j) {
			variance += Math.pow((values[j] - mean), 2);
		}
		variance /= values.length;

		return variance;
	}

	function standartDeviationPopulation(formula) {
		let rangeString = formula.match(/\(([^)]+)\)/)[1]; // "B2:B7"
		let values = getValuesFromRange(rangeString);
		// Проверяем, является ли переданный аргумент массивом и содержит ли он хотя бы одно значение
		if (!Array.isArray(values) || values.length === 0) {
			return NaN;
		}

		// Находим среднее арифметическое значений массива
		const average = values.reduce((acc, val) => acc + val, 0) / values.length;

		// Вычисляем сумму квадратов отклонений от среднего
		const deviationsSquaredSum = values.reduce((acc, val) => acc + Math.pow(val - average, 2), 0);

		// Вычисляем стандартное отклонение как корень квадратный из суммы квадратов отклонений, деленной на длину массива
		return Math.sqrt(deviationsSquaredSum / values.length);
	}

})
