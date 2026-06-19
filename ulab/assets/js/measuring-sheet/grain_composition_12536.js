function roundPlus(x, n) {
    if(isNaN(x) || isNaN(n)) return false;
    let m = Math.pow(10, n);
    return Math.round(x * m) / m;
}

$('input').on('change', function(){

    let field = $(this).attr('name');
    let seave = field.split('_').pop();
    let average_mass = $("[name='initial_mass']").val();

    if(field == "g_10"){
        a_i = roundPlus((($(`[name='g_${seave}']`).val() / average_mass) * 100), 2);
        
        $(`[name='a_${seave}']`).val(a_i);
        $(`[name='p_${seave}']`).val(a_i);
    }
    else{
        a_i = roundPlus((($(`[name='g_${seave}']`).val() / average_mass) * 100), 2);
        
        $(`[name='a_${seave}']`).val(a_i);

        let parentTD = $($(`[name='p_${seave}']`)).parent();
        let adjacentTD = $(parentTD).prev();

        let adjacentInput = Number($(adjacentTD).children().val());

        P_i = roundPlus((adjacentInput + a_i), 2);
        $(`[name='p_${seave}']`).val(P_i);

        if(field == "g_005"){
            let clayParticles = 100 - P_i;
            $("[name='content_clay_particles']").val(clayParticles);
        }
    }
    $("[name*='p']").each(function(){

        if($(this).attr('data-id') == 1){
    
            if(Number($(this).val()) > 50){
                $("[name='name_soil']").val("Гравийный");
                return false;
            }
            else{
                if(Number($("[data-id=3]").val()) > 25){
                    $("[name='name_soil']").val("Гравелистый");
                    return false;
                }
                else{
                    if(Number($("[data-id=5]").val()) > 50){
                        $("[name='name_soil']").val("Крупный");
                        return false;
                    }
                    else{
                        if(Number($("[data-id=6]").val()) > 50){
                            $("[name='name_soil']").val("Средней крупности");
                            return false;
                        }
                        else{
                            if(Number($("[data-id=7]").val()) >= 75){
                                $("[name='name_soil']").val("Мелкий");
                                return false;
                            }
                            else{
                                if(Number($("[data-id=7]").val()) < 75){
                                    $("[name='name_soil']").val("Пылеватый");
                                    return false;
                                }
                            }
                        }
                    }
                }
            }
        }
    });
});