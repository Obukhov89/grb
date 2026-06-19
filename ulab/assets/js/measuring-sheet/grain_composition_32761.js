$(function ($) {
    let M_2_0_i, M_0_125_i, M_0_063_i, M_2_0_2_i, M_0_125_2_i, M_0_063_2_i, M_2_0, M_0_125, M_0_063;

    function roundPlus(x, n) {
        if (isNaN(x) || isNaN(n)) return false;
        let m = Math.pow(10, n);
        return Math.round(x * m) / m;
    }


    $('#calc').on('click', function () {


        function calcA(m_i, m, size, trial = "_2") {

            let a = roundPlus(((m_i / m) * 100), 2);
            let prefix = '';

            if (size === "2.0") {
                prefix = "_2_0" + trial;
            }
            if (size === "0.125") {
                prefix = "_0_125" + trial;
            }
            if (size === "0.063") {
                prefix = "_0_063" + trial;
            }



            $(`[name='a${prefix}']`).val(a);

            return roundPlus((100 - a), 2);
        }

        M_2_0_i = calcA($("[name='m_2_0']").val(), $("[name='m_1_2_0']").val(), "2.0", "");
        M_0_125_i = calcA($("[name='m_0_125']").val(), $("[name='m_1_0_125']").val(), "0.125", "");
        M_0_063_i = calcA($("[name='m_0_063']").val(), $("[name='m_1_0_063']").val(), "0.063", "");

        M_2_0_2_i = calcA($("[name='m_2_0_2']").val(), $("[name='m_1_2_0_2']").val(), "2.0");
        M_0_125_2_i = calcA($("[name='m_0_125_2']").val(), $("[name='m_1_0_125_2']").val(), "0.125");
        M_0_063_2_i = calcA($("[name='m_0_063_2']").val(), $("[name='m_1_0_063_2']").val(), "0.063");

        M_2_0 = roundPlus(((M_2_0_i + M_2_0_2_i) / 2), 2);
        $("[name='p_2_0']").val(M_2_0);

        M_0_125 = roundPlus(((M_0_125_i + M_0_125_2_i) / 2), 2);
        $("[name='p_0_125']").val(M_0_125);

        M_0_063 = roundPlus(((M_0_063_i + M_0_063_2_i) / 2), 2);
        $("[name='p_0_063']").val(M_0_063);

        let M_2_0_diff = roundPlus((M_2_0_i - M_2_0_2_i), 2);
        let M_0_125_diff = roundPlus((M_0_125_i - M_0_125_2_i), 2);
        let M_0_063_diff = roundPlus((M_0_063_i - M_0_063_2_i), 2);

        if (M_2_0_diff > 2) {
            alert('Разница между определениями содержания частиц порошка мельче 2.000 мм превышает 2% и равна ' + M_2_0_diff + "%. Необходимо повторить испытание");
        }

        if (M_0_125_diff > 2) {
            alert('Разница между определениями содержания частиц порошка мельче 0.125 мм превышает 2% и равна ' + M_0_125_diff + "%. Необходимо повторить испытание");
        }

        if (M_0_063_diff > 2) {
            alert('Разница между определениями содержания частиц порошка мельче 0,063 мм превышает 2% и равна ' + M_0_063_diff + "%. Необходимо повторить испытание");
        }
    });
})