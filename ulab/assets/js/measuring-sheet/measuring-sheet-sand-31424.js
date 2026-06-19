/*sand 31424*/
$(function ($) {
  const body = $("body");

  let CONTENT_CLAY_PARTICLES_TO_VOLUME = {
    "1.50": 17.0,
    "1.45": 16.43,
    "1.40": 15.87,
    "1.35": 15.35,
    "1.30": 14.74,
    "1.25": 14.17,
    "1.20": 13.85,
    "1.15": 13.03,
    "1.10": 12.46,
    "1.05": 11.9,
    "1.00": 11.33,
    "0.95": 10.76,
    "0.90": 10.2,
    "0.85": 9.63,
    "0.80": 9.06,
    "0.75": 8.5,
    "0.70": 7.93,
    "0.65": 7.36,
    "0.60": 6.8,
    "0.55": 6.23,
    "0.50": 5.66,
    "0.45": 5.09,
    "0.40": 4.53,
    "0.35": 3.96,
    "0.30": 3.39,
    "0.25": 2.83,
    "0.20": 2.26,
    "0.15": 1.7,
    "0.12": 1.36,
    "0.10": 1.13
  };

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

  function getNumberSimbolsAfterComma(number) {
    return number.toString().includes(".") ?
      number
      .toString()
      .split(".")
      .pop().length :
      0;
  }

  /*Получить данные элементов*/
  function getElementsData(items) {
    let data = {};

    items.each(function (index, item) {
      let trial = $(item).data("trial");

      data[trial] = +$(this).val();
    });

    return data;
  }

  function getElementsDataArray(items) {
    let data = [];

    items.each(function (index, item) {
      data.push(+$(this).val());
    });

    return data.sort(function (a, b) {
      return a - b;
    });
  }

  /*Сбросить значения*/
  function resetValue(items) {
    items.each(function (index, item) {
      $(this).val("");
    });
  }

  /**
   * Проверка равен 0 или меньше 0
   * true - равен или меньше 0
   */
  function isZeroOrLess(items) {
    let zeroOrLess = false;

    items.each(function (index, item) {
      console.log("$(this).val()", $(this).val());
      if ($(this).val() === "") {
        return;
      }

      if ($(this).val() <= 0) {
        zeroOrLess = true;
        return false;
      }
    });

    return zeroOrLess;
  }

  /**
   * Проверка кол-ва символов после запятой
   * true - кол-во символов после запятой больше 2-х
   */
  function isNumberSimbolsAfterCommaMore2(items) {
    let numberSimbolsAfterCommaMore2 = false;

    items.each(function (index, item) {
      let number = $(this).val();
      let numberSimbolsAfterComma = number.toString().includes(".") ?
        number
        .toString()
        .split(".")
        .pop().length :
        0;

      if (numberSimbolsAfterComma > 2) {
        numberSimbolsAfterCommaMore2 = true;
        return false;
      }
    });

    return numberSimbolsAfterCommaMore2;
  }

  /*Находим два ближайших значений*/
  function findPair(input) {
    input = input.sort(function (a, b) {
      return a - b;
    });

    let minDiff = Infinity;
    let index = 0;

    for (let i = 0; i < input.length - 1; i++) {
      const cur = input[i];
      const next = input[i + 1];
      const diff = Math.abs(cur - next);

      if (diff < minDiff) {
        minDiff = diff;
        index = i;
      }
    }

    return input.slice(index, index + 2);
  }

  function round(num, decimalPlaces = 0) {
    if (num < 0)
      return -round(-num, decimalPlaces);
    let p = Math.pow(10, decimalPlaces);
    let n = num * p;
    let f = n - Math.floor(n);
    let e = Number.EPSILON * n;

    return (f >= .5 - e) ? Math.ceil(n) / p : Math.floor(n) / p;
  }

  function toFixed2(value) {
    if (value === 0 || value === '0') {
      return value
    }

    return value.toFixed(2)
  }

  /*Проверка равенства чисел в массиве*/
  function equalityCheckInArray(a) {
    for (var b = 0; b < a.length; b++)
      if (a[b] !== a[0]) return false;
    return true;
  }

  /*Проверка на пусто значение*/
  function checkEmptyValueInArray(arr) {
    for (let i in arr) {
      if (arr[i] === 0) {
        return true
      }
    }

    return false
  }

  /*Посчитать ко-во повторений в массиве*/
  function countNumberRepetitionsInArray(arr) {
    return result = arr.reduce(function (acc, el) {
      acc[el] = (acc[el] || 0) + 1;
      return acc;
    }, {})
  }

  /*Отсортировать данные обьекта по значению*/
  function sortObjectByValue(list) {
    return Object.keys(list).sort(function (a, b) {
      return list[a] - list[b]
    })
  }

  /*Получить последний элемент массива*/
  function returnLastItem(arr) {
    return arr[arr.length - 1];
  }

  function getArithmeticMean(data, amountData) {
    if (!$.isArray(data) || data.length === 0 || amountData <= 0) {
      return false;
    }

    let sumData = data.reduce(function (acc, val) {
      return acc + val;
    }, 0);

    return sumData / amountData;
  }


  function getPrivateRemainderByMass(
    totalMass,
    massOnSieve,
    privateRemainders
  ) {
    const SIEVE_10 = 10,
      SIEVE_5 = 5;

    let response = {},
      mass;

    if (!$.isArray(privateRemainders) || privateRemainders.length === 0) {
      return response;
    }

    for (let sieve in privateRemainders) {
      if (sieve == SIEVE_10 || sieve == SIEVE_5) {
        mass = totalMass;
      } else {
        mass = massOnSieve;
      }

      let result = (privateRemainders[sieve] / mass) * 100;

      response[sieve] = round(result, 2);
    }

    return response;
  }

  function getTotalRemainderByMass(privateRemaindersByMass) {
    let response = {},
      totalRemainderByMass = 0;

    const SIEVE_10 = 10,
      SIEVE_5 = 5;

    if ($.isEmptyObject(privateRemaindersByMass)) {
      return response;
    }

    for (let sieve in privateRemaindersByMass) {
      if (sieve == SIEVE_10 || sieve == SIEVE_5) {
        continue;
      }

      totalRemainderByMass += +privateRemaindersByMass[sieve];
      response[sieve] = round(totalRemainderByMass, 2);
    }

    return response;
  }

  /*Получить модуль крупности*/
  function getFinenessModule(totalRemainderByMass) {
    let response = 0,
      total = 0;
    const SIEVES = ["2.5", "1.25", "0.63", "0.315", "0.16"];

    if ($.isEmptyObject(totalRemainderByMass)) {
      return response;
    }

    for (let value in totalRemainderByMass) {
      if ($.inArray(value, SIEVES) === -1) {
        continue;
      }

      total += +totalRemainderByMass[value];
    }

    response = total / 100;

    return round(response, 2);
  }

  function getClayParticleDustContent(sampleMassBefore, sampleMassAfter) {
    let response = 0;

    response = ((sampleMassBefore - sampleMassAfter) / sampleMassBefore) * 100;

    return round(response, 2);
  }

  function getClayLumpContent(sandSampleMass, massSandGrains) {
    let clayLumpContent =
      ((sandSampleMass - massSandGrains) / sandSampleMass) * 100;

    return round(clayLumpContent, 1);
  }

  function getTotalClayContentLumps(
    clayLumpContent2_5,
    privateRemaindersByMass2_5,
    clayLumpContent1_25,
    privateRemaindersByMass1_25
  ) {
    let totalClayContentLumps =
      (clayLumpContent2_5 * privateRemaindersByMass2_5 +
        clayLumpContent1_25 * privateRemaindersByMass1_25) /
      100;

    return round(totalClayContentLumps, 1);
  }

  function getTrueDensity(
    pycnometerWeightWithSand,
    emptyPycnometerWeight,
    weightWithDistilledWater,
    massAfterRemovalAirBubbles,
    densityWater
  ) {

    let trueDensity =
      ((pycnometerWeightWithSand - emptyPycnometerWeight) *
        densityWater) /
      (pycnometerWeightWithSand -
        emptyPycnometerWeight +
        weightWithDistilledWater -
        massAfterRemovalAirBubbles);

    return round(trueDensity, 2);
  }

  function getBulkDensity(
    measuringVesselMass,
    measuringVesselWithSand,
    conteinerCapacity
  ) {
    let bulkDensity =
      (measuringVesselWithSand - measuringVesselMass) /
      conteinerCapacity;

    return round(bulkDensity, 2);
  }

  function getCrushability(testSampleMass, residueMassAfterScreening) {
    let crushability =
      ((testSampleMass - residueMassAfterScreening) / testSampleMass) * 100;

    return round(crushability);
  }

  function getContentLamellarGrains(analyticalSampleMass, massLamellarGrains) {
    let contentLamellarGrains =
      (massLamellarGrains / analyticalSampleMass) * 100;

    return round(contentLamellarGrains, 1);
  }

  function getSwellingVolumeIncrement(
    sandVolumeAfterSwelling,
    initialVolumeSand
  ) {
    let particleContentBySwellingMethod =
      (sandVolumeAfterSwelling - initialVolumeSand) / initialVolumeSand;

    //return round(particleContentBySwellingMethod, 2).toFixed(2);
    return round(particleContentBySwellingMethod, 2);
  }

  function getParticleContentBySwellingMethod(
    totalRemaindersByMass0_16,
    contentClayParticles
  ) {
    let particleContentBySwellingMethod =
      (totalRemaindersByMass0_16 * contentClayParticles) / 100;

    return round(particleContentBySwellingMethod, 1);
  }

  body.on("click", ".messages .btn-close", function () {
    $(this)
      .closest(".messages")
      .remove();
  });

  /*Расчёт определение зернового состава и модуля крупности*/
  body.on("click", "#finenessModuleCalculate", function () {
    let totalMass = +$(".total-mass").val(),
      massOnSieve = +$(".mass-on-sieve").val();

    let privateRemainders = [];

    const finenessModulusWrapper = $(".fineness-modulus-wrapper");

    $(".private-remainder").each(function (index, item) {
      let sieve = $(item).data("sieve");

      privateRemainders[sieve] = +$(this).val();
    });

    let privateRemaindersByMass = getPrivateRemainderByMass(
      totalMass,
      massOnSieve,
      privateRemainders
    );

    for (let sieve in privateRemaindersByMass) {
      $(`.private-remainders-by-mass[data-sieve = "${sieve}"]`).val(toFixed2(privateRemaindersByMass[sieve]));
    }

    let totalRemainderByMass = getTotalRemainderByMass(privateRemaindersByMass);

    for (let sieve in totalRemainderByMass) {
      $(`.total-remainder-by-mass[data-sieve = "${sieve}"]`).val(toFixed2(totalRemainderByMass[sieve]));
    }

    let finenessModule = getFinenessModule(totalRemainderByMass);

    $(".fineness-module").val(toFixed2(finenessModule));
  });

  /*Расчет содержание пылевидных и глинистых частиц*/
  body.on("click", ".particle-content-calculate", function () {
    let sampleMassBefore = +$(".sample-mass-before").val(),
      sampleMassAfter = +$(".sample-mass-after").val();

    const wrapperClayParticleDust = $(".wrapper-clay-particle-dust");

    let clayParticleDustContent = getClayParticleDustContent(
      sampleMassBefore,
      sampleMassAfter
    );

    $(".clay-particle-dust-content").val(clayParticleDustContent);
  });

  /*Расчет содержание глины в комках*/
  body.on("click", "#calculateClayContentLumps", function () {
    let sandSampleMass2_5 = +$(".sand-sample-mass-2_5").val(),
      sandSampleMass1_25 = +$(".sand-sample-mass-1_25").val(),
      massSandGrains2_5 = +$(".mass-sand-grains-2_5").val(),
      massSandGrains1_25 = +$(".mass-sand-grains-1_25").val(),
      privateRemaindersByMass2_5 = +$(
        ".hidden-private-remainders-by-mass-2_5"
      ).val(),
      privateRemaindersByMass1_25 = +$(
        ".hidden-private-remainders-by-mass-1_25"
      ).val();

    const inputClayLumpContent2_5 = $(".clay-lump-content-2_5"),
      inputClayLumpContent1_25 = $(".clay-lump-content-1_25"),
      inputTotalClayContentLumps = $(".total-clay-content-lumps"),
      wrapperClayLumps = $(".wrapper-clay-lumps");

    wrapperClayLumps.find(".messages").remove();

    if (
      $(".hidden-private-remainders-by-mass-2_5").val() === "" ||
      $(".hidden-private-remainders-by-mass-1_25").val() === ""
    ) {
      let messageError =
        "Внимание! Отсутствует расчет зернового состава и модуля крупности!";

      let messageErrorContent = getMessageErrorContent(messageError);

      wrapperClayLumps.prepend(messageErrorContent);
      inputClayLumpContent2_5.val("");
      inputClayLumpContent1_25.val("");
      inputTotalClayContentLumps.val("");

      return false;
    }

    let clayLumpContent2_5 = getClayLumpContent(
      sandSampleMass2_5,
      massSandGrains2_5
    );
    let clayLumpContent1_25 = getClayLumpContent(
      sandSampleMass1_25,
      massSandGrains1_25
    );

    inputClayLumpContent2_5.val(clayLumpContent2_5);
    inputClayLumpContent1_25.val(clayLumpContent1_25);

    let totalClayContentLumps = getTotalClayContentLumps(
      clayLumpContent2_5,
      privateRemaindersByMass2_5,
      clayLumpContent1_25,
      privateRemaindersByMass1_25
    );

    inputTotalClayContentLumps.val(totalClayContentLumps);
  });

  /*Расчет истинная плотность (пикнометрический метод)*/
  body.on("click", "#сalculateTrueDensity", function () {
    const ONE = 1,
      TWO = 2,
      THREE = 3;

    const inputTrueDensity = $(".true-density"),
      pycnometricMethod = $(".pycnometric-method"),
      inputPycnometerWeightWithSand = $(".pycnometer-weight-with-sand"),
      inputEmptyPycnometerWeight = $(".empty-pycnometer-weight"),
      inputWeightWithDistilledWater = $(".weight-with-distilled-water"),
      inputMassAfterRemovalAirBubbles = $(".mass-after-removal-air-bubbles"),
      inputDensityWater = $(".density-water"),
      inputTrueDensityAverage = $(".true-density-average"),
      trTrial3 = $(".true-density-table .tr-trial-3");

    let pycnometerWeightWithSand = getElementsData(
      inputPycnometerWeightWithSand
    );
    let emptyPycnometerWeight = getElementsData(inputEmptyPycnometerWeight);
    let weightWithDistilledWater = getElementsData(
      inputWeightWithDistilledWater
    );
    let massAfterRemovalAirBubbles = getElementsData(
      inputMassAfterRemovalAirBubbles
    );
    let densityWater = getElementsData(inputDensityWater);

    pycnometricMethod.find(".messages").remove();

    let trueDensityOne = getTrueDensity(
      pycnometerWeightWithSand[ONE],
      emptyPycnometerWeight[ONE],
      weightWithDistilledWater[ONE],
      massAfterRemovalAirBubbles[ONE],
      densityWater[ONE]
    );

    let trueDensityTwo = getTrueDensity(
      pycnometerWeightWithSand[TWO],
      emptyPycnometerWeight[TWO],
      weightWithDistilledWater[TWO],
      massAfterRemovalAirBubbles[TWO],
      densityWater[TWO]
    );

    $(`.true-density[data-trial=${ONE}]`).val(toFixed2(trueDensityOne));
    $(`.true-density[data-trial=${TWO}]`).val(toFixed2(trueDensityTwo));

    let trueDensityAverage = getArithmeticMean([trueDensityOne, trueDensityTwo], 2);

    inputTrueDensityAverage.val(round(trueDensityAverage, 2));

    let difference = trueDensityOne - trueDensityTwo;


    if (Math.abs(round(difference, 2)) > 0.02) {
      let messageError =
        "Расхождение между результатами двух определений истинной плотности более 0,02 г/см";

      let messageErrorContent = getMessageErrorContent(messageError);

      pycnometricMethod.prepend(messageErrorContent);

      if (!trTrial3.hasClass("d-none")) {
        let trueDensityThree = getTrueDensity(
          pycnometerWeightWithSand[THREE],
          emptyPycnometerWeight[THREE],
          weightWithDistilledWater[THREE],
          massAfterRemovalAirBubbles[THREE],
          densityWater[THREE]
        );

        $(`.true-density[data-trial=${THREE}]`).val(toFixed2(trueDensityThree));

        let trueDensity = getElementsDataArray(inputTrueDensity);
        let resultFindPair = findPair(trueDensity);
        let trueDensityAverage = getArithmeticMean(resultFindPair, 2);

        inputTrueDensityAverage.val(toFixed2(round(trueDensityAverage, 2)));
      }

      if (trTrial3.hasClass("d-none")) {
        trTrial3.removeClass("d-none")
      }

      /*if (!$(".true-density-table .row-trial-3").hasClass("d-none")) {
        $(".true-density-table .row-trial-3").addClass("d-none")
      }*/

    } else {

      let inputTrial3 = trTrial3.find('input')

      resetValue(inputTrial3);

      if (!trTrial3.hasClass("d-none")) {
        trTrial3.addClass("d-none")
      }

      /*if (!$(".true-density-table .row-trial-3").hasClass("d-none")) {
        $(".true-density-table .row-trial-3").addClass("d-none")
      }*/
    }
  });

  /*Расчет насыпная плотность*/
  body.on("click", "#сalculateBulkDensity", function () {
    const ONE = 1,
      TWO = 2;

    const inputMeasuringVesselMass = $(".measuring-vessel-mass"),
      inputMeasuringVesselWithSand = $(".measuring-vessel-with-sand"),
      inputConteinerCapacity = $(".conteiner-capacity"),
      inputbulkDensityAverage = $('.bulk-density-average');

    let measuringVesselMass = getElementsData(inputMeasuringVesselMass);
    let measuringVesselWithSand = getElementsData(inputMeasuringVesselWithSand);
    let conteinerCapacity = getElementsData(inputConteinerCapacity);

    let bulkDensityOne = getBulkDensity(
      measuringVesselMass[ONE],
      measuringVesselWithSand[ONE],
      conteinerCapacity[ONE]
    );

    let bulkDensityTwo = getBulkDensity(
      measuringVesselMass[TWO],
      measuringVesselWithSand[TWO],
      conteinerCapacity[TWO]
    );

    $(`.bulk-density[data-trial=${ONE}]`).val(toFixed2(bulkDensityOne));
    $(`.bulk-density[data-trial=${TWO}]`).val(toFixed2(bulkDensityTwo));

    let bulkDensityAverage = getArithmeticMean([bulkDensityOne, bulkDensityTwo], 2);

    inputbulkDensityAverage.val(round(bulkDensityAverage, 2));
  });

  /*Расчет дробимости*/
  body.on("click", "#сalculateСrushability", function () {
    const inputCrushability1 = $(".crushability-1"),
      inputCrushability2 = $(".crushability-2"),
      strengthGrade = $(".strength_grade");

    let testSampleMass1 = +$(".test-sample-mass-1").val(),
      testSampleMass2 = +$(".test-sample-mass-2").val(),
      residueMassAfterScreening1 = +$(".residue-mass-after-screening-1").val(),
      residueMassAfterScreening2 = +$(".residue-mass-after-screening-2").val();

    let crushability1 = getCrushability(
      testSampleMass1,
      residueMassAfterScreening1
    );

    let crushability2 = getCrushability(
      testSampleMass2,
      residueMassAfterScreening2
    );

    inputCrushability1.val(crushability1);
    inputCrushability2.val(crushability2);

    let averageCrushability = getArithmeticMean([crushability1, crushability2], 2)

    $("#arithmeticMeanOfTests").val(round(averageCrushability));
  });

  /*Расчет содержание зерен пластинчатой (лещадной) и игловатой формы*/
  body.on("click", "#сalculateContentLamellarGrains", function () {
    let analyticalSampleMass = +$(".analytical-sample-mass").val(),
      massLamellarGrains = +$(".mass-lamellar-grains").val();

    const contentLamellarCgrains = $(".content-lamellar-grains");

    let contentLamellarGrains = getContentLamellarGrains(
      analyticalSampleMass,
      massLamellarGrains
    );

    contentLamellarCgrains.val(contentLamellarGrains);
  });

  /** Расчет содержание пылевидных и глинистых частиц методом набухания */
  body.on("click", "#сalculateParticleContent", function () {
    const ONE = 1,
      TWO = 2;

    let totalRemaindersByMass0_16 = +$(".hidden-total-remainders-by-mass-0_16").val(),
      totalRemaindersByMass0_63 = +$(".hidden-total-remainders-by-mass-0_63").val(),
      particleContent = +$(".particles").val();

    const particleContentWrapper = $(".particle-content-wrapper"),
      inputSwellingVolumeIncrement = $(".swelling-volume-increment"),
      inputSwellingVolumeIncrement1 = $(".swelling-volume-increment-1"),
      inputSwellingVolumeIncrement2 = $(".swelling-volume-increment-2"),
      inputParticleContent = $(".particle-content-by-swelling-method"),
      inputArithmeticMeanVolumeIncrease = $(".arithmetic-mean-volume-increase"),
      inputInitialVolumeSand = $('.initial-volume-sand'),
      inputSandVolumeAfterSwelling = $('.sand-volume-after-swelling');

    particleContentWrapper.find(".messages").remove();

    if (
      $(".hidden-total-remainders-by-mass-0_16").val() === "" ||
      $(".hidden-total-remainders-by-mass-0_63").val() === ""
    ) {
      let messageError =
        "Внимание! Отсутствует расчет зернового состава и модуля крупности!";

      let messageErrorContent = getMessageErrorContent(messageError);

      particleContentWrapper.prepend(messageErrorContent);
      inputSwellingVolumeIncrement.val("");
      inputArithmeticMeanVolumeIncrease.val("");
      inputParticleContent.val("");

      return false;
    }

    if ($(".particles").val() === "") {
      let messageError =
        "Внимание! Выберите частицы песка учавствующие в испытании!";

      let messageErrorContent = getMessageErrorContent(messageError);

      particleContentWrapper.prepend(messageErrorContent);
      inputSwellingVolumeIncrement.val("");
      inputArithmeticMeanVolumeIncrease.val("");
      inputParticleContent.val("");

      return false;
    }

    let initialVolumeSand = getElementsData(inputInitialVolumeSand);
    let sandVolumeAfterSwelling = getElementsData(inputSandVolumeAfterSwelling);

    let swellingVolumeIncrement1 = getSwellingVolumeIncrement(
      sandVolumeAfterSwelling[ONE],
      initialVolumeSand[ONE]
    );

    let swellingVolumeIncrement2 = getSwellingVolumeIncrement(
      sandVolumeAfterSwelling[TWO],
      initialVolumeSand[TWO]
    );

    inputSwellingVolumeIncrement1.val(toFixed2(swellingVolumeIncrement1));
    inputSwellingVolumeIncrement2.val(toFixed2(swellingVolumeIncrement2));

    let swellingVolumeIncrementAverage = getArithmeticMean([swellingVolumeIncrement1, swellingVolumeIncrement2], 2);

    swellingVolumeIncrementAverage = round(swellingVolumeIncrementAverage, 2).toFixed(2)

    inputArithmeticMeanVolumeIncrease.val(swellingVolumeIncrementAverage)

    if (!CONTENT_CLAY_PARTICLES_TO_VOLUME[swellingVolumeIncrementAverage]) {
      let messageError =
        "Внимание! Приращение объема набухания не соответсвует табличному значению!";

      let messageErrorContent = getMessageErrorContent(messageError);

      particleContentWrapper.prepend(messageErrorContent);
      inputParticleContent.val("");

      return false;
    }

    if (particleContent === 0.16) {
      totalRemaindersByMass = totalRemaindersByMass0_16
    } else if (particleContent === 0.63) {
      totalRemaindersByMass = totalRemaindersByMass0_63
    }

    let particleContentBySwellingMethod = getParticleContentBySwellingMethod(
      totalRemaindersByMass,
      CONTENT_CLAY_PARTICLES_TO_VOLUME[swellingVolumeIncrementAverage]
    );

    inputParticleContent.val(particleContentBySwellingMethod);
  });

  /*Расчет качества сцепление с битумом исходной породы*/
  body.on("click", ".bitumen-bond-evaluation-calculate", function () {
    const inputQualityControlTbody = $(".bitumen-bond-evaluation tbody .quality-control"),
      inputQualityControl = $(".bitumen-bond-evaluation .quality-control"),
      bitumenAdhesionQuality = $("#bitumenAdhesionQuality"),
      bitumenBondEvaluationWrapper = $('.bitumen-bond-evaluation-wrapper'),
      tfoot = $(".bitumen-bond-evaluation tfoot");

    let qualityControlTbody = getElementsDataArray(inputQualityControlTbody);
    let checkEmptyValue = checkEmptyValueInArray(qualityControlTbody);

    bitumenBondEvaluationWrapper.find(".messages").remove();

    if (checkEmptyValue) {
      let messageError =
        "Внимание! Для расчета качество сцепления с битумом, оцените качество сцепления всех зерен!";

      let messageErrorContent = getMessageErrorContent(messageError);

      bitumenBondEvaluationWrapper.prepend(messageErrorContent);
      bitumenAdhesionQuality.val("");

      return false;
    }

    let equalityCheck = equalityCheckInArray(qualityControlTbody);

    if (!equalityCheck && tfoot.hasClass("d-none")) {
      let messageError =
        "Внимание! Несовпадение характеристик пленки битума на разных зернах!";

      let messageErrorContent = getMessageErrorContent(messageError);

      bitumenBondEvaluationWrapper.prepend(messageErrorContent);
      bitumenAdhesionQuality.val("");

      tfoot.removeClass("d-none")

      return false;

    } else if (equalityCheck) {
      tfoot.find('.quality-control').prop('selectedIndex', 0)

      if (!tfoot.hasClass("d-none")) {
        tfoot.addClass("d-none")
      }
    }

    bitumenAdhesionQuality.val(qualityControlTbody[0]);

    if (!tfoot.hasClass("d-none")) {
      let qualityControl = getElementsDataArray(inputQualityControl);
      let checkEmptyValue = checkEmptyValueInArray(qualityControl);

      if (checkEmptyValue) {
        let messageError =
          "Внимание! Для расчета качество сцепления с битумом, оцените качество сцепления всех зерен!";

        let messageErrorContent = getMessageErrorContent(messageError);

        bitumenBondEvaluationWrapper.prepend(messageErrorContent);
        bitumenAdhesionQuality.val("");

        return false;
      }

      let countNumberRepetitions = countNumberRepetitionsInArray(qualityControl)
      let sortedValues = sortObjectByValue(countNumberRepetitions)
      let lastItem = returnLastItem(sortedValues)

      bitumenAdhesionQuality.val(lastItem);
    }
  });


  /*Изменение определение зернового состава и модуля крупности*/
  body.on("input", ".fineness-modulus-wrapper input:not([readonly])", function () {
    const inputReadonly = $(".fineness-modulus-wrapper input[readonly]");
    resetValue(inputReadonly);
  });

  /*Изменение содержание пылевидных и глинистых частиц*/
  body.on("input", ".wrapper-clay-particle-dust input:not([readonly])", function () {
    $(".clay-particle-dust-content").val("");
  });

  /*Изменение содержание глины в комках*/
  body.on("input", ".wrapper-clay-lumps input:not([readonly])", function () {
    $(".clay-lump-content-2_5").val("");
    $(".clay-lump-content-1_25").val("");
    $(".total-clay-content-lumps").val("");
  });

  /*Расчет истинная плотность (пикнометрический метод)*/
  body.on("input", ".true-density-determination input:not([readonly])", function () {
    const inputReadonly = $(".true-density-determination input[readonly]");
    resetValue(inputReadonly);
  });

  /*Изменение насыпная плотность*/
  body.on("input", ".bulk-density-wrapper input:not([readonly])", function () {
    const inputReadonly = $(".bulk-density-wrapper input[readonly]");
    resetValue(inputReadonly);
  });

  /*Изменение дробимость*/
  body.on("input", ".crushability input:not([readonly])", function () {
    const inputReadonly = $(".crushability input[readonly]");
    resetValue(inputReadonly);
  });

  /*Изменение качества сцепление с битумом исходной породы*/
  body.on("change", ".bitumen-bond-evaluation-wrapper select", function () {
    $(".bitumen-adhesion-quality").val("");
  });

  /*Изменение содержания зерен пластинчатой (лещадной) и игловатой форм*/
  body.on("input", ".grain-shape-wrapper input:not([readonly])", function () {
    const inputReadonly = $(".grain-shape-wrapper input[readonly]");
    resetValue(inputReadonly);
  });

  /*Изменение определение содержание пылевидных и глинистых частиц методом набухания*/
  body.on("input", ".particle-content-wrapper input:not([readonly]), .particle-content-wrapper select", function () {
    $(".swelling-volume-increment").val("");
    $(".arithmetic-mean-volume-increase").val("");
    $(".particle-content-by-swelling-method").val("");
  });
});