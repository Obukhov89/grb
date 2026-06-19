$(document).ready(function () {


    $("#package-search").on("keyup change", function (e) {
        console.log("test!")
        let searchValue = this.value.toLowerCase();

        console.log(this.value);


        [...$(".search-item")].forEach(function (item) {
            let itemText = $(item).find("label").text().toLowerCase();


            if (itemText.includes(searchValue)) {
                $(item).show()
            } else {
                $(item).hide()
            }
        })
    })

    $('[data-js-add-shipment]').click(function (e) {

        let dealId = $(this).attr("data-js-add-shipment");
        //  console.log(dealId)
        $.ajax({
            url: `${URI}/request/addShipmentAjax/`,
            type: "POST", //метод отправки
            dataType: 'json', // data type
            data: {"dealId": dealId},
            success: function (result) {
                //  console.log(result);
                document.location.href = `/local/factory/shipments/form/${result}`

            },
            error: function (xhr, resp, text) {
                console.log(xhr, resp, text);
            }
        });
    });


    $('[data-js-delete-recept]').click(function (e) {
        let td = $(this).closest("td");
        $(this).parent().hide();
        $(td).find("select").show()
    })

    $('[data-js-save-card]').click(function (e) {
        // $('[data-js-card-form]').submit()

        // ajax для обновления полей сделки
        $.ajax({
            url: "/local/factory/request/updateProduction/",
            type: "POST",
            dataType: "json",
            data: {
                id: $("[name='id']").val(),
                comments: $("[name='comments']").val(),
                productionComments: $("[name='productionComments']").val(),
            },
            success: function (response) { //Данные отправлены успешно
                document.location.reload();
                console.log(response)
            },
            error: function (response) { // Данные не отправлены
                $('#result_form').html('Ошибка. Данные не отправлены.');
            }
        });

        let forms = [...$("[data-js-product-form]")]

        forms.forEach(function (form, i) {
            var data = new FormData($(form)[0]);

            // ajax для обновления полей товаров в БД
            $.ajax({
                url: "/local/factory/request/updateProductionTableAjax/",
                type: "POST",
                dataType: "json",
                //  data: $(form).serialize(),
                processData: false,
                contentType: false,
                data: data,
                success: function (response) { //Данные отправлены успешно
                    //document.location.reload();
                    //   if (i === forms.length - 1) document.location.reload();
                    console.log(response)
                },
                error: function (response) { // Данные не отправлены
                    $('#result_form').html('Ошибка. Данные не отправлены.');
                }
            });
        })
    })

    $('[data-js-btn-modal]').click(function (e) {
        let productId = $(this).attr("data-js-btn-modal");
        let packagesArr = $(`[data-js-packages=${productId}]`).val().split(",")
        let modalPackages = [...$("#materials-modal").find("input")];

        $("[data-js-add-packages]").attr("data-js-add-packages", productId);

        modalPackages.forEach(function (modalPackage) {
            $(modalPackage).prop("checked", false)

            if (packagesArr.includes(modalPackage.value)) {
                $(modalPackage).prop("checked", true)
            }
        })
    })

    $("[data-js-add-packages]").click(function (e) {
        let productId = $(this).attr("data-js-add-packages");
        let modalPackages = [...$("#materials-modal").find("input")];
        let selectedPackageArr = [];
        let selectedPackages = "";

        modalPackages.forEach(function (modalPackage) {

            if ($(modalPackage).is(":checked")) {
                selectedPackageArr.push(modalPackage.value);
            }
        })

        selectedPackages = selectedPackageArr.join(",");

        $(`[data-js-packages=${productId}]`).val(selectedPackages)

    })
    $(document).on('show.bs.modal', '.modal', function () {
        $(this).appendTo('body');
    });

    $('[data-js-delete-file]').click(function (e) {
        let td = $(this).closest("td")
        $(this).parent().hide()
        //  $(td).find("select").show()
        //   $(td).find("select option:first").prop("selected", true)
        //  $(td).find("[name='is_sended']").val(-1)

        // $(td).find("input").val(-1)
        //  $(td).removeClass("bg-color-light").removeClass("bg-color-dark")

        //  let productId = $(this).attr("data-js-productId");
        let inputDelete = $(td).find("[data-js-delete]");
        let inputReceptIdDelete = $(td).find("[data-js-id-delete]");
        //    $(inputDelete).val($(inputDelete).val() + $(this).attr("data-js-file-id") + ",")
        $(inputDelete).val($(inputDelete).val() + $(this).parent().find("a").text() + ",")

        $(inputReceptIdDelete).val($(inputReceptIdDelete).val() + $(this).attr("data-js-recept-id") + ",")

        //  $(td).find("[data-js-add-file]").show()

    })


// Скрол по старнице
    let container = $('.production-table-wrap'),
        scroll = $('#production-table').width()

    if ($('#production-table').width() <= $('.production-table-wrap').width()) {
        $('.btn-group-toolbar').hide()
    }


    $('.production-arrowRight').hover(function () {

            container.animate(
                {
                    scrollLeft: scroll
                },
                {
                    duration: 4000, queue: false
                }
            )
        },
        function () {
            container.stop();
        })

    $('.production-arrowLeft').hover(function () {

            container.animate(
                {
                    scrollLeft: -scroll
                },
                {
                    duration: 4000, queue: false
                }
            )
        },
        function () {
            container.stop();
        })


// Скрол по старнице (кнопки снизу)
    $(document).scroll(function () {
        let maxScroll = $(document).height() - $(window).height();
        let scrollTop = $(window).scrollTop();

        let bottomInt = (10 + maxScroll - scrollTop - 100) + "px";
        let bottomText = (10 + maxScroll - scrollTop - 100);

        if (scrollTop >= maxScroll - 100) {
            $(".btn-group-toolbar").css("bottom", "10px")
        } else {
            $(".btn-group-toolbar").css("bottom", bottomText)
        }

    })
})