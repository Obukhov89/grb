$(function ($) {

    function roundPlus(x, n) {
        if (isNaN(x) || isNaN(n)) return false;
        let m = Math.pow(10, n);
        return Math.round(x * m) / m;
    }

    $('input').change(function () {

        let initial_mass = $("[name='initial_mass']").val();

        let field = $(this).attr('name');
        let seave = field.split('_').pop();

        if (field == "m_120") {
            let a_i = roundPlus(($(`[name='m_${seave}']`).val() / initial_mass), 2);

            $(`[name='a_${seave}']`).val(a_i);

            $(`[name='p_${seave}']`).val(a_i);
        } else {
            if ($(this).attr('name').includes("dust")) {
               let a_i = roundPlus(($(`[name='m_${seave}']`).val() / 100), 2);
                $(`[name='a_${seave}']`).val(a_i);
            } else {
               let a_i = roundPlus(($(`[name='m_${seave}']`).val() / initial_mass), 2);

                $(`[name='a_${seave}']`).val(a_i);

                let parentTD = $($(`[name='p_${seave}']`)).parent();
                let adjacentTD = $(parentTD).prev();

                let adjacentInput = Number($(adjacentTD).children().val());

               let P_i = roundPlus((adjacentInput + a_i), 2);
                $(`[name='p_${seave}']`).val(P_i);
            }

            let FR = 100 - $(".graincomposition [name='p_5']").val();


            let PR_2_5_dust = $("[name='a_25']").val();
            let PR_2_5 = roundPlus(((PR_2_5_dust * FR) / 100), 2);
            $(".graincomposition [name='a_25']").val(PR_2_5);
            let parentTD = $($("[name='p_25']")).parent();
            let adjacentTD = $(parentTD).prev();
            let adjacentInput = Number($(adjacentTD).children().val());
            let FR_2_5 = roundPlus((adjacentInput + PR_2_5), 2);
            $(".graincomposition [name='p_25']").val(FR_2_5);


            let PR_0_63_dust = $("[name='a_063']").val();
            let PR_0_63 = roundPlus(((PR_0_63_dust * FR) / 100), 2);
            $(".graincomposition [name='a_063']").val(PR_0_63);
            parentTD = $($("[name='p_063']")).parent();
            adjacentTD = $(parentTD).prev();
            adjacentInput = Number($(adjacentTD).children().val());
            let FR_0_63 = roundPlus((adjacentInput + PR_0_63), 2);
            $(".graincomposition [name='p_063']").val(FR_0_63);


            let PR_0_16_dust = $("[name='a_016']").val();
            let PR_0_16 = roundPlus(((PR_0_16_dust * FR) / 100), 2);
            $(".graincomposition [name='a_016']").val(PR_0_16);
            parentTD = $($("[name='p_016']")).parent();
            adjacentTD = $(parentTD).prev();
            adjacentInput = Number($(adjacentTD).children().val());
            let FR_0_16 = roundPlus((adjacentInput + PR_0_16), 2);
            $(".graincomposition [name='p_016']").val(FR_0_16);


            let PR_0_05_dust = $("[name='a_005']").val();
            let PR_0_05 = roundPlus(((PR_0_05_dust * FR) / 100), 2);
            $(".graincomposition [name='a_005']").val(PR_0_05);
            parentTD = $($("[name='p_005']")).parent();
            adjacentTD = $(parentTD).prev();
            adjacentInput = Number($(adjacentTD).children().val());
            let FR_0_05 = roundPlus((adjacentInput + PR_0_05), 2);
            $(".graincomposition [name='p_005']").val(FR_0_05);
        }
    });
});