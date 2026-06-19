$("#save").click(function (e) {


  //  let dataObject = new FormData($(`[data-js-form-object]`)[0]);

    if ($(`[data-js-form-object]`).find("[name='NAME']").val() == "") {
        $(`[data-js-form-object]`).find("[name='NAME']").css("background", "#f08080")
    }

    if ($(`[data-js-form-object]`).find("[name='NAME']").val() != "") {
        let dataObject = new FormData();

        dataObject.append("NAME", $(`[data-js-form-object]`).find("[name='NAME']").val());
        dataObject.append("ID_COMPANY", $(`[data-js-form-object]`).find("[name='ID_COMPANY']").val());
        dataObject.append("CITY", $(`[data-js-form-object]`).find("[name='CITY']").val());
        dataObject.append("KM", $(`[data-js-form-object]`).find("[name='KM']").val());

        [...$("[data-js-coords]")].forEach(function (coord) {
            dataObject.append($(coord).attr("name"), $(coord).val());
        })
        console.log(dataObject)

        $.ajax({
            url: "/ulab/object/addAjax/",
            type:     "POST",
            dataType: "json",
            processData: false,
            contentType: false,
            data: dataObject,
            success: function(response) { //Данные отправлены успешно
                // document.location.reload();
                // if (i === forms.length - 1) document.location.reload();
                console.log(response)

                if (typeof $("#object") != "undefined") {
                    console.log(response["ID"] + " " + response["NAME"])

                    let id = response.ID
                    let name = response.NAME

                    $("#object").append(`<option value="${id}">${name}</option>`)
                    $("#object").val(id).change();
                    $("[data-js-form-object]").toggle(300)
                }
            },
            error: function(response) { // Данные не отправлены
                console.log(response)
                $('#result_form').html('Ошибка. Данные не отправлены.');
            }
        });
    }




})

$(document).ready(function() {
    $('[data-js-clients]').select2({

    });
    $('[data-js-cities]').select2();
});