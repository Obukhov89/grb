
    let body = $('body'),
        secondmentStage = $('.secondment-stage').text();

    // Проверка счетчиков
    [...$("[data-js-input-count]")].forEach(function (item) {
        if ($(item).text() == false) {
            $(item).hide()
        }
    })

    $("#contract-file").attr("href", $("#contract-select option:selected").attr("data-js-dir") + $("#contract-select option:selected").attr("data-js-file"));



    const INFORMATION_EDITING_STAGES = ['Новая', 'Нужна доработка'];
    const REPORT_EDITING_STAGES = ['Подготовка отчета', 'Затраты не подтверждены'];

    //общая информация
    if ($.inArray(secondmentStage, INFORMATION_EDITING_STAGES) === -1) {
       $("#formInfo :input").not('button').prop("disabled", true);
       $("#formInfo .dropzone").addClass('disabled-upload');
       $("#formInfo [data-js-fact]").prop("disabled", false)

    }

    //отчёт
    if ($.inArray(secondmentStage, REPORT_EDITING_STAGES) === -1) {
        // $("#formReport :input").not('button').prop("disabled", true);
        // $("#formReport .dropzone").addClass('disabled-upload');

        $("#formInfo [data-js-fact]").prop("disabled", true)
     //   $("#formInfo [data-js-fact]").prop("disabled", false)
        // console.log($("[data-js-fact]"))
        // console.log("ОТЧЕТ!!!")
    }

    // Меняет тип договора
    body.on('change', '#contract-select', function () {
        let contractType = $('option:selected', this).attr('data-js-type')
        $("[name=\"contract_type\"]").val(contractType)

    })

    //получает должность сотрудника и записывает в поле
    body.on('change', '#user', function () {
        let userId = $(this).val();

        $.ajax({
            url: '/ulab/secondment/getWorkPositionAjax/',
            data: {
                user_id: userId
            },
            method: 'POST',
            success: function (value) {
                if (value && typeof value === 'string') {
                    $('.work-position').val(value)
                } else {
                    $('.work-position').val('')
                }
            },
            error: function (jqXHR, exception) {
                let msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status === 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status === 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                console.error(msg)
            }
        })
    });

    function setMoneyFormat(elem, totalElem, form = "") {
        let moneyFields = [...elem]

        moneyFields.forEach(function (field) {
            let num = $(field).val()
            const format = new Intl.NumberFormat('ru-RU').format(num);
            $(field).attr("type", "text")
            $(field).val(format)
        })

        $(elem).focus(function (e) {
            let text = $(this).val();
            let textNum = text.replace(" ", "").replace(",", ".");
            $(this).attr("type", "number")
            $(this).val(textNum)
        })

        $(elem).blur(function (e) {
            let num = $(this).val().toString();
            const format = new Intl.NumberFormat('ru-RU').format(num);
            $(this).attr("type", "text")
            $(this).val(format)
        })

        if (form !== "") {
            form.submit(function (e) {
                let moneyFields = [...elem, ...totalElem]
                moneyFields.forEach(function (field) {
                    let text = $(field).val();
                    let textNum = text.replace(" ", "").replace(",", ".");
                    $(field).attr("type", "number")
                    $(field).val(textNum)
                })
            })
        }
    }

    function textFormat(elem) {
        let num =  elem.val().toString();
        const format = new Intl.NumberFormat('ru-RU').format(num);
        elem.attr("type", "text")
        elem.val(format)
        elem.val()
    }



    setMoneyFormat($("[data-js-plan-card] [data-js-format-money]"), $('#plannedExpenses'),  $("#formInfo"));
    setMoneyFormat($("[data-js-plan-card-result] [data-js-format-money]"), $('#plannedExpenses'),  $("#formInfo"));
   // setMoneyFormat($("[data-js-extend-card] [data-js-format-money]"), $('#extendExpenses'),  $("#formInfo"));

    getPlannedSum($('[data-js-plan-card] .cost'), $('#plannedExpenses'))
    getPlannedSum($('[data-js-extend-card] .cost'), $('#extendExpenses'))

    setMoneyFormat($("[data-js-fact-card] [data-js-format-money]"), $('#totalSpent'), $("#formReport"));
    setMoneyFormat($("[data-js-result-card] [data-js-format-money]"), $('#totalSpent'), $("#formReport"));


   // textFormat($("#plannedExpenses"))
   // textFormat($("[name=\"planned_expenses\"]"))
 //   textFormat($("#totalSpent"))

    body.on('change', '#transport', function (e) {
        const $option = $(this).find("option:selected")

        let fuelPrice = $option.attr("data-js-fuel-price")
        let fuelTitle = $option.attr("data-js-fuel-title")
        let fuelConsumption = $option.attr("data-js-fuel-consumption")
        let km = $("#kilometer").val();

        let totalPrice = Math.round((fuelConsumption / 100 * km * fuelPrice * 2 + Number.EPSILON) * 100) / 100

        $("#gasolineConsumption").val(totalPrice)
        setMoneyFormat($("#gasolineConsumption"), $("#plannedExpenses"), $("#formInfo"));
        getPlannedSum($('.cost'), $('#plannedExpenses'))

        $("[data-js-vehicle-info]").html(`
              <div><strong>Расход:</strong> ${fuelConsumption}</div>
              <div><strong>Топливо:</strong> ${fuelTitle}</div> 
              <div><strong>Цена:</strong> ${fuelPrice}</div>
        `)
    })



    body.on('change', '#object', function () {
        let city_id = $(this).find(":selected").attr("data-js-city-id");
        let city_title = $(this).find(":selected").attr("data-js-city");

       // $("#city").val($(this).find(":selected").attr("data-js-city"))
        $("#kilometer").val($(this).find(":selected").attr("data-js-km"))

        $("#city").append(`<option value="${city_id}">${city_title}</option>`)
        $("#city").val(city_id).change()

    })

    body.on('change', '#company', function () {
        let companyId = $('#company-hidden').val();

        $.ajax({
            url: '/ulab/secondment/getObjectsAjax/',
            data: {
                company_id: companyId
            },
            method: 'POST',
            dataType: 'json',
            success: function (data) {
                let $selectObject = $('#object');
                $selectObject.empty().append(`<option value=""></option>`);

                console.log($("#company-hidden").val())

                if (data.length !== 0) {
                    for (const i in data) {
                        if (data.hasOwnProperty(i)) {
                            $selectObject.append(
                                `<option value="${data[i].ID}" data-js-city="${data[i].settlement}" data-js-km="${data[i].KM}">
                                ${data[i].NAME}
                            </option>`
                            )
                        }
                    }
                }
            },
            error: function (jqXHR, exception) {
                let msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status === 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status === 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                console.error(msg)
            }
        })
    });

    function getPlannedSum(costs, field) {
        let sum = 0;

        if (typeof costs !== 'object' || $.isEmptyObject(costs)) {
            return false;
        }
        for (let el of costs) {

            if ($(el).val() == "") {
                $(el).val(0);
            }

            text = $(el).val()

            let textNum = text.replace(" ", "").replace(",", ".");
            sum += +parseFloat(textNum)
        }
        field.val(Math.round((sum + Number.EPSILON) * 100) / 100)
    }


    //Запланированные затраты(Итого)
    body.on('input', '[data-js-plan-card] .cost', function () {
        if ($(this).val() == "") {
            $(this).val(0)
        }

        getPlannedSum($('[data-js-plan-card] .cost'), $('#plannedExpenses'))

        let num =  $("#plannedExpenses").val().toString();
        const format = new Intl.NumberFormat('ru-RU').format(num);
        $("#plannedExpenses").attr("type", "text")
        $("#plannedExpenses").val(format)
        $("#plannedExpenses").val()
    });

    //Запланированные затраты продление (Итого)
    body.on('input', '[data-js-extend-card] .cost', function () {
        if ($(this).val() == "") {
            $(this).val(0)
        }

        getPlannedSum($('[data-js-extend-card] .cost'), $('#extendExpenses'))

        let num =  $("#extendExpenses").val().toString();
        const format = new Intl.NumberFormat('ru-RU').format(num);
        $("#extendExpenses").attr("type", "text")
        $("#extendExpenses").val(format)
        $("#extendExpenses").val()
    });



    //Фактические затраты(Итого)
    body.on('input', '[data-js-fact-card] .cost', function () {
        if ($(this).val() == "") {
            $(this).val(0)
        }

        getPlannedSum($('[data-js-fact-card] .cost'), $('#totalSpent'))

        const inputOverspending = $('#overspending');

        let plannedExpenses = parseFloat($("#plannedExpenses").val().replace(" ", "").replace(",", "."))

        let totalSpent = +$('#totalSpent').val();

        let overspending = round((((totalSpent - plannedExpenses) / plannedExpenses) * 100));

        if (overspending) {
            inputOverspending.val(overspending);

            if (overspending > 20) {
                if (!$('#overspending').hasClass('border-red')) {
                    $('#overspending').addClass('border-red');
                }
            }else {
                $('#overspending').removeClass('border-red');
            }
        } else {
            inputOverspending.val('');
        }

        let num =  $("#totalSpent").val().toString();
        const format = new Intl.NumberFormat('ru-RU').format(num);
        $("#totalSpent").attr("type", "text")
        $("#totalSpent").val(format)
        $("#totalSpent").val()
    });


    //Всего дней
    $('.date-begin, .date-ending').on('change', function () {
        const totalDays = $('.total-days');

        let dateBegin = $('.date-begin').val(),
            dateEnding = $('.date-ending').val();

        let numberOfDays = getNumberOfDays(dateBegin, dateEnding);

        if (numberOfDays === 0) {
            $("#perDiem").val(0)
        } else {
            $("#perDiem").val((numberOfDays + 1 )* 700 )
        }



        if (numberOfDays < 0) {
            $('.alert-title').text('Внимание')
            $('.alert-content').text('Дата начала командировки больше даты окончания командировки')

            $.magnificPopup.open({
                items: {
                    src: $('#alert_modal'),
                    type: 'inline',
                    fixedContentPos: false
                }
            })

            totalDays.val(numberOfDays - 1);

            if (!$('.total-days-wrapper').hasClass('border border-red')) {
                $('.total-days-wrapper').addClass('border border-red');
            }

            return false;
        }

        if (numberOfDays > 0 || numberOfDays === 0) {
            totalDays.val(numberOfDays + 1);

            $('.total-days-wrapper').removeClass('border border-red');

            $('.cost').trigger('input');
        }
    });


    //Загрузка фалов
    body.on('change', '#edictBtn', function () {
        const errorMassage = 'Файл не загружен',
            uploadEdict = $('#uploadEdict');

        let file = this.files ? this.files[0] : {},
            fileName = file ? file.name : '';

        if (!fileName) {
            uploadEdict.text(errorMassage);
            uploadEdict.addClass('text-light-red');
            return false;
        }

        let countElem = $(this).parent().find("[data-js-input-count]");
        let input = $(this).parent().find("input");
        let count = $(input).prop('files').length;
        $(countElem).text(count).show(300)

       // uploadEdict.text(fileName);
    });

    body.on('change', '#serviceAssignmentBtn', function () {
        const errorMassage = 'Файл не загружен',
            uploadServiceAssignment = $('#uploadServiceAssignment');

        let file = this.files ? this.files[0] : {},
            fileName = file ? file.name : '';

        if (!fileName) {
            uploadServiceAssignment.text(errorMassage);
            uploadServiceAssignment.addClass('text-light-red');
            return false;
        }

        let countElem = $(this).parent().find("[data-js-input-count]");
        let input = $(this).parent().find("input");
        let count = $(input).prop('files').length;
        $(countElem).text(count).show(300)

       // uploadServiceAssignment.text(fileName);
    });

    /*body.on('change', '#memoBtn', function () {
        const errorMassage = 'Файл не загружен',
            uploadMemo = $('#uploadMemo');

        let file = this.files ? this.files[0] : {},
            fileName = file ? file.name : '';

        if (!fileName) {
            uploadMemo.text(errorMassage);
            uploadMemo.addClass('text-light-red');
            return false;
        }

        uploadMemo.text(fileName);
    });*/


    //Отправить на согласование(#sendApprove), Подтвердить(#confirmSecondment), Вернуть на доработку(#returnSecondment),
    // Отклонить(#rejectSecondment), В командировке(#isSecondment), Подготовка отчета(#preparingReport),
    // Отправить на проверку(отчёт #sendVerify), Перерасходы проверены(#overspendingChecked), Отчет подтвержден(#confirmReport),
    // Затраты не подтверждены(#expensesNotVerified)
    body.on('click', '#sendApprove, #confirmSecondment, #returnSecondment, #rejectSecondment, #isSecondment, ' +
        '#preparingReport, #sendVerify, #overspendingChecked, #confirmReport, #expensesNotVerified', function () {
        let stage = $(this).data('stage'),
            secondmentId = $('.form-info .secondment-id').val();

        let improvementReason = $("[name='improvement_reason']").val()

        let data = {
            stage: stage,
            secondment_id: secondmentId,
            improvement_reason: improvementReason

        }


        if (stage === 'Подготовка приказа и СЗ' &&
            !confirm('Выдействительно хотите подтвердить? После подтверждения, данные нельзя изменить')) {
            return false;
        }

        if (stage === 'Отчет подтвержден' &&
            !confirm('Выдействительно хотите подтвердить отчёт? После подтверждения, отчет нельзя изменить')) {
            return false;
        }



        $.ajax({
            method: 'POST',
            url: '/ulab/secondment/updateStageAjax/',
            data: data,
            dataType: 'json',
            success: function (data) {
                console.log(data)
                if (data) {
                    location.reload();
                }
            },
            error: function (jqXHR, exception) {
                let msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status === 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status === 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                console.error(msg)
            }
        })
    });


    //Перерасход
    $('#totalSpent').on('input', function () {
        const inputOverspending = $('#overspending');

        let plannedExpensesText = $('#plannedExpenses').val();
        let textNum = plannedExpensesText.replace(" ", "").replace(",", ".");

        console.log(textNum)
        let plannedExpenses = +parseFloat(textNum)
        let totalSpent = +$(this).val();

        console.log("===")
        console.log(plannedExpenses)

        let overspending = round((((totalSpent - plannedExpenses) / plannedExpenses) * 100));

        if (overspending) {
            inputOverspending.val(overspending);

            if (overspending > 20) {
                if (!$('#overspending').hasClass('border-red')) {
                    $('#overspending').addClass('border-red');
                }
            }else {
                $('#overspending').removeClass('border-red');
            }
        } else {
            inputOverspending.val('');
        }
    });


let secondmentId = $('.form-info .secondment-id').val();
// Dropzone.options.checks = {
//     url: `/ulab/secondment/uploadChecksFilesAjax/${secondmentId}`,
//     paramName: "file",
//     maxFilesize: 15,
//     init: function () {
//         this.on("complete", function (file) {
//             if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
//                 $('.checks-wrapper .dropzone-msg').append(
//                     `<div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
//                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
//                         <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
//                     </svg>
//                     <div>
//                         Файлы сохранены.
//                     </div>
//                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
//                 </div>`
//                 )
//             }
//         });
//     },
//     error: function (file, message) {
//         $('.checks-wrapper .dropzone-msg').append(
//             `<div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
//                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
//                     <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
//                 </svg>
//                 <div>
//                     Ошибка: ${message}
//                 </div>
//                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
//             </div>`
//         )
//     }
// }
//
// Dropzone.options.ticketPayment = {
//     url: `/ulab/secondment/uploadTicketPaymentFileAjax/${secondmentId}`,
//     paramName: "file", // The name that will be used to transfer the file
//     maxFilesize: 15, // MB
//     init: function () {
//         this.on("complete", function (file) {
//             if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
//                 $('.ticket-payment-wrapper .dropzone-msg').append(
//                     `<div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
//                     <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
//                         <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
//                     </svg>
//                     <div>
//                         Файлы сохранены.
//                     </div>
//                     <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
//                 </div>`
//                 )
//             }
//         });
//     },
//     error: function (file, message) {
//         $('.ticket-payment-wrapper .dropzone-msg').append(
//             `<div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
//                 <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
//                     <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
//                 </svg>
//                 <div>
//                     Ошибка: ${message}
//                 </div>
//                 <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
//             </div>`
//         )
//     }
// }

$("[data-js-download]").click(function (e) {
    let wrap = $(this).closest("[data-js-btn-group]")
    let hrefs = [...$(wrap).find("[data-js-file-download]")]

    hrefs.forEach(function (href) {
       href.click()

    })
})

    $("[data-js-delete-payment-file]").click(function (e) {
        let inputDelete = $(this).closest("form").find($("[name=\"file_payment_delete\"]"));

        $(inputDelete).val($(inputDelete).val() + "\n" + $(this).attr("data-js-delete-payment-file"))

        let filesWrap = $(this).parent();
        let fileButton = $(filesWrap).find("[data-js-file-wrap]");
        let secondmentId = $('.form-info .secondment-id').val();
        let file = $(this).attr("data-js-delete-payment-file");
        $(filesWrap).hide(300)
        $.ajax({
            method: 'POST',
            url: '/ulab/secondment/deleteTicketPaymentFile',
            data: {
                id: secondmentId,
                file: file
            },
            dataType: 'json',
            success: function (json) {
                console.log(json);
            },
            error: function (e) {
                console.log(e);
            }
        })
        //console.log(fileButton)
    })

$("[data-js-delete-file]").click(function (e) {
    let inputDelete = $("[name=\"file_delete\"]");

    $(inputDelete).val($(inputDelete).val() + "\n" + $(this).attr("data-js-delete-file"))

   // let filesWrap = $(this).closest("[data-js-files]");
    let filesWrap = $(this).parent();
  //  let addButton = $(filesWrap).find("[data-js-upload-wrap]");
    let fileButton = $(filesWrap).find("[data-js-file-wrap]");
    let secondmentId = $('.form-info .secondment-id').val();
    let file = $(this).attr("data-js-delete-file");
    console.log(filesWrap)
    $(filesWrap).hide(300)
    $(fileButton).hide(300)
    $.ajax({
        method: 'POST',
        url: '/ulab/secondment/deleteTicketPaymentFile',
        data: {
            id: secondmentId,
            file: file
        },
        dataType: 'json',
        success: function (json) {
            console.log(json);
        },
        error: function (e) {
            console.log(e);
        }
    })
  //  $(addButton).show(300)
   // console.log(fileButton)
})

$('#city').select2({

    ajax: {
        url: "/ulab/secondment/getSettlementsAjax",
        type: "post",
        dataType: 'json',
        delay: 250,
        data: function (params) {
            return {
                searchTerm: params.term || '*' // search term
            };
        },
        processResults: function (response) {
            return {
                results: response
            };
        },
        cache: true
    }
})

$("[data-js-delete-card]").click(function (e) {
    $.ajax({
        url: '/ulab/secondment/deleteCardAjax/',
        data: {
            secondment_id: $(this).attr("data-js-delete-card")
        },
        method: 'POST',
        dataType: 'json',
        success: function (data) {
            if (data == 1) {
                document.location.href = "/ulab/secondment/list"
            } else {
                $("[data-js-message]").html(`
                <div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    <div>
                        ${data}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `)
            }
        },
        error: function (jqXHR, exception) {
            let msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status === 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status === 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            console.error(msg)
        }
    })
})

    $("#btn-extend").click(function (e) {
        let num = $(this).closest("form").find("[name=\"planned_expenses\"]").val().text.replace(" ", "")
        $(this).closest("form").find("[name=\"planned_expenses\"]").val(num)
    })

// Модальное окно - Транспорт
$("[data-js-toggle-transport]").click(function (e) {
    $("[data-js-form-transport]").toggle(500)
})

// Модальное окно - Транспорт
$("[data-js-toggle-contract]").click(function (e) {
    $("[data-js-form-contract]").toggle(500)
})

// Добавить договор
$("[data-js-add-contract]").click(function (e) {
    console.log("add contract!")
    let formData = new FormData()

    formData.append("number", $("[name=\"contract_number\"]").val())
    formData.append("client_id", $("[name=\"company_id\"]").val())
    formData.append("contract", $("[name='contract']")[0].files[0]);

    $.ajax({
        url: '/ulab/secondment/addContractAjax/',
        data: formData,
        method: 'POST',
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function (data) {
             console.log(data)
            $("#contract-select").append(`
                        <option value="${data.id}" novalidate>${data.number}</option>
                    `)

            $("#contract-select").val(data.id).change()

            $("#contract-file").attr("href", `/ulab/upload/contracts/${data.id}/${data.name}`);
            $("[data-js-form-contract]").hide(300)

        },
        error: function (jqXHR, exception) {
            let msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status === 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status === 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            console.error(msg)
        }
    })
})

// Добавить транспорт
$("[data-js-add-vehicle]").click(function (e) {

    let fields = $(this).parent().find("table input, table select");
    let data = $(fields).serialize()

    let check = true;

    $(fields).each(function() {
        if ($( this ).val() == "") {
            $( this ).css("background", "#F25F66");
            check = false;
        } else {
            $( this ).css("background", "white");
        }



    });

    if (check) {
        $.ajax({
            url: '/ulab/transport/addTransportAjax/',
            data: data,
            method: 'POST',
            dataType: 'json',
            success: function (data) {
               // console.log(data)
                $("#transport").append(`
                        <option value="${data.id}" data-js-fuel-price="${data.price}" data-js-fuel-consumption="${data.consumption_rate}" novalidate>${data.model} (${data.number})</option>
                    `)

                $("#transport").val(data.id).change()
                $("[data-js-form-transport]").hide(300)


            },
            error: function (jqXHR, exception) {
                let msg = '';
                if (jqXHR.status === 0) {
                    msg = 'Not connect.\n Verify Network.';
                } else if (jqXHR.status === 404) {
                    msg = 'Requested page not found. [404]';
                } else if (jqXHR.status === 500) {
                    msg = 'Internal Server Error [500].';
                } else if (exception === 'parsererror') {
                    msg = 'Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                console.error(msg)
            }
        })
    }



    console.log(data)
  //  console.log($(this).parent().find("table input"))


})
    $("[data-js-add-order]").click(function (e) {
        let secondmentId = $(this).closest('form').find("[name='secondment_id']").val();
        let workPosition = $(this).closest('form').find("#workPosition").val();
        let edictNumber = $(this).closest('form').find("[name='edict_number']").val();
        let userId = $('#user').val();
        let city = $('#city option:selected').text();
        let org = $('#company').val();
        let start = $('#dateBeginning').val();
        let end = $('#dateEnding').val();
        let totalDays = $('#totalDays').val();
        let contract = $('#contract-select option:selected').text();

        let postData = {};
        postData.secondment_id = secondmentId;
        postData.work_position = workPosition;
        postData.edict_number = edictNumber;
        postData.userId = userId;
        postData.city = city;
        postData.org = org;
        postData.start = start;
        postData.end = end;
        postData.totalDays = totalDays;
        postData.contract = contract;
            $.ajax({
                url: '/ulab/secondment/addOrderAjax/',
                data: postData,
                method: 'POST',
                dataType: 'json',
                success: function (postData) {
                    window.location.reload();
                },
                error: function (jqXHR, exception) {
                    let msg = '';
                    if (jqXHR.status === 0) {
                        msg = 'Not connect.\n Verify Network.';
                    } else if (jqXHR.status === 404) {
                        msg = 'Requested page not found. [404]';
                    } else if (jqXHR.status === 500) {
                        msg = 'Internal Server Error [500].';
                    } else if (exception === 'parsererror') {
                        msg = 'Requested JSON parse failed.';
                    } else if (exception === 'timeout') {
                        msg = 'Time out error.';
                    } else if (exception === 'abort') {
                        msg = 'Ajax request aborted.';
                    } else {
                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                    }
                    console.error(msg)
                }
            })



        console.log(postData)
        //  console.log($(this).parent().find("table input"))


    })
// Добавить поле прочее
$("[data-js-add-other]").click(function (e) {
    let currentNum = 0;

    if ($(this).parent().prev().find("[type=\"file\"]").attr("name") != undefined) {
        parseInt($(this).parent().prev().find("[type=\"file\"]").attr("name").replace ( /[^\d.]/g, '' )) + 1;
    }

    $(this).parent().before(`
        <div class="row mb-2 align-items-end">
            <div class="form-group col-sm-3">
               
                <input type="text" class="form-control other cost" id="other" name="other[]" min="0" step="0.01" data-js-format-money="" autocomplete="off" value="0.00">
            </div>
            <div class="form-group col-sm-4">
            <textarea class="form-control mw-100 comment-other" id="commentOther" name="comment_other[]"></textarea>
            </div>
            <div class="form-group col-sm-4 d-flex align-items-end" data-js-btn-group="">
                <div data-js-upload-wrap="" class="position-relative">
                    <label class="p-0 text-center" title="Загрузить билеты">
                        <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                        <span data-js-input-count="" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded" style="z-index: 100; display: none;">
                        </span>
                        </div>
                        <input multiple="" class="form-control d-none" type="file" id="edictBtn" name="other[${currentNum}][]" data-js-upload="">
                    </label>
                </div>

                
                <div data-js-upload-wrap="" class="position-relative btn-count-wrap">
                    <button data-js-download="" type="button" class="btn btn-primary position-relative rounded">0</button>
                </div>

            </div>
        </div>
    `)

   // setMoneyFormat($("[data-js-plan-card] [data-js-format-money]"), $('#plannedExpenses'),  $("#formInfo"));
   // setMoneyFormat($("[data-js-extend-card] [data-js-format-money]"), $('#extendExpenses'),  $("#formInfo"));
   //
   //  getPlannedSum($('[data-js-plan-card] .cost'), $('#plannedExpenses'))
   //  getPlannedSum($('[data-js-extend-card] .cost'), $('#extendExpenses'))
   //
   //  getPlannedSum($('.cost'), $('#plannedExpenses'))
   //  setMoneyFormat($("[data-js-format-money]").last(), $("#plannedExpenses"), $("#formInfo"));


});

// Добавить поле прочее
$("[data-js-add-additional]").click(function (e) {
    $(this).parent().before(`
    <div class="row mb-2 align-items-end">
        <div class="form-group col-sm-3">
           
            <input type="text" class="form-control other cost" id="additional" name="additional[]" min="0" step="0.01" data-js-format-money="" autocomplete="off" value="0.00">
        </div>
        <div class="form-group col-sm-4">
        <textarea class="form-control mw-100 comment-other" id="commentOther" name="comment_additional[]"></textarea>
        </div>
        <div class="form-group col-sm-5 d-flex align-items-end" data-js-btn-group="">
            <div data-js-upload-wrap="" class="position-relative">
                <label class="p-0 text-center" title="Загрузить билеты">
                    <div class="btn btn-primary position-relative rounded fa-solid fa-plus" style="font-size: 16px">
                    <span data-js-input-count="" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger rounded" style="z-index: 100; display: none;">
                    </span>
                    </div>
                    <input multiple="" class="form-control d-none" type="file" id="edictBtn" name="additional[]" data-js-upload="">
                </label>
            </div>

            
            <div data-js-upload-wrap="" class="position-relative">
                <button 
                    data-js-download="" 
                    type="button" 
                    class="btn btn-primary position-relative rounded" 
                    style="margin-left: 4px; font-size: 14px; font-weight: bold; padding: 4px; margin-top: 4px;"
                >0</button>
            </div>

        </div>
    </div>
`)

    getPlannedSum($('.cost'), $('#plannedExpenses'))
    setMoneyFormat($("[data-js-format-money]").last(), $("#plannedExpenses"), $("#formInfo"));


});

// Отправить форму сохранить инфо
$("[data-js-save-info]").click(function (e) {
    e.preventDefault();
    const form = $('#formInfo');

    let fileInput = $("[name=\"ticket_payment_fact[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"fuel_payment_fact[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"fuel_payment_object_fact[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"per_diem_fact[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"accommodation_fact[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"signed_service_assignment[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"signed_edict[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"memo_doc[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"waybill[]\"]").clone();
    form.append(fileInput);

    form.submit();
})

// Отправить форму сохранить отчет
$("[data-js-save-report]").click(function (e) {
    $("#formReport").submit()
    $("#uploadDocuments").submit()
})

// Отправить форму приказа
$("[data-js-save-files]").click(function (e) {
    e.preventDefault();
    const form = $('#uploadFiles');
    if ($(this).attr("name") == "stage_ready") {
        $("#uploadFiles").append("<input name='stage_ready' value='true' style='display: none'>")
    }
    let fileInput = $("[name=\"ticket_payment_fact[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"fuel_payment_fact[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"fuel_payment_object_fact[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"per_diem_fact[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"accommodation_fact[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"signed_service_assignment[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"signed_edict[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"memo_doc[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"waybill[]\"]").clone();
    form.append(fileInput);
    fileInput = $("[name=\"no_edict2[]\"]").clone();
    form.append(fileInput);

    form.submit();
    $("#uploadFiles").submit()
})

//
// /** modal */
// $('[data-js-change-stage]').magnificPopup({
//     items: {
//         src: '#cancelStage',
//         type: 'inline'
//     },
//     fixedContentPos: false
// })

/** modal */
$("[data-js-rework]").click(function (e) {
    $.magnificPopup.open({
        items: {
            //  src: '#extend-secondment',
            src: '#add-entry-modal-form',
            type: 'inline'
        },
        fixedContentPos: false
    })
})

// $('#rework').magnificPopup({
//     items: {
//         src: '#add-entry-modal-form',
//         type: 'inline'
//     },
//     fixedContentPos: false
// })

/** modal */
$('#cancel-stage-toggle').click(function (e) {
    $.magnificPopup.open({
        items: {
            src: '#change-stage',
            type: 'inline'
        },
        fixedContentPos: false
    })
})
// $('#cancel-stage-toggle').magnificPopup({
//     items: {
//         src: '#change-stage',
//         type: 'inline'
//     },
//     fixedContentPos: false
// })

/** modal */
// $("[data-js-extend]").magnificPopup({
//     items: {
//         src: '#extend-secondment',
//         type: 'inline'
//     },
//     fixedContentPos: false
// })
$("[data-js-extend]").click(function (e) {
    $("#json_data").val(getJsonCard())
    console.log($("#json_data").val())
    $.magnificPopup.open({
        items: {
           src: '#extend-secondment',
          //   src: '#add-entry-modal-form',
            type: 'inline'
        },
        fixedContentPos: false
    })
})


// Продлить командировку
$("#extendStageBtn").click(function (e) {
    $.ajax({
        url: '/ulab/secondment/extendAjax/',
        data: $("#extend-secondment").serialize(),
        method: 'POST',
        dataType: 'json',
        success: function (data) {
            console.log(data)
            if (data) {
               location.reload();
            }

        },
        error: function (jqXHR, exception) {
            let msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status === 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status === 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            console.error(msg)
        }
    })
})

// Изменить стадию
$("#cancelStage").click(function (e) {
    $.ajax({
        url: '/ulab/secondment/changeStageAjax/',
        data: {
            secondment_id: $('.form-info .secondment-id').val(),
            stage: $(this).attr("data-stage"),
            cancel_comment: $("[name=\"cancel_reason\"]").val()
        },
        method: 'POST',
        dataType: 'json',
        success: function (data) {
             console.log(data)
            if (data) {
                location.reload();
            }

        },
        error: function (jqXHR, exception) {
            let msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status === 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status === 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            console.error(msg)
        }
    })
})


function getJsonCard() {

    let otherData = [];

    let otherSum = $("[data-js-plan-card] [name='other[]']")
    let otherComments = $("[data-js-plan-card] [name='comment_other[]']")
    let otherIdArr = $("[data-js-plan-card] [name='other_id[]']")

    let otherLength = $("[data-js-plan-card] [name='other[]']").length;

    for (let i = 0; i < otherLength; i++) {
        otherData.push({
            "id": $(otherIdArr[i]).val(),
            "sum": $(otherSum[i]).val(),
            "comment": $(otherComments[i]).text().trim()
        })
    }


    let data = {
        "user_name": $("#user option:selected").text().trim(),
        "work_position": $("#workPosition").val(),
        "city": $("#city option:selected").text().trim(),
        "object": $("#object option:selected").text().trim(),
        "km": $("#kilometer").val(),
        "company": $("#company").val(),
        "contract": $("#contract-select option:selected").text().trim(),
        "date_begin": $("#dateBeginning").val(),
        "date_end": $("#dateEnding").val(),
        "total_days": $("#totaаlDays").val(),
        "content": $("#content").text().trim(),
        "transport": $("#transport option:selected").text().trim(),
        "comment": $("#comment").text().trim(),
        "ticket_price": $("#ticketPrice").val().replace(" ", ""),
        "ticket_price_comment": $("#commentTicketPrice").text().trim(),
        "gasoline_consumption": $("#gasolineConsumption").val().replace(" ", ""),
        "gasoline_consumption_object_comment": $("#commentGasolineConsumptionObject").text().trim(),
        "gasoline_consumption_object": $("#gasolineConsumptionObject").val().replace(" ", ""),
        "gasoline_consumption_comment": $("#commentGasolineConsumption").text().trim(),
        "per_diem": $("#perDiem").val().replace(" ", ""),
        "per_diem_comment": $("#commentPerDiem").text().trim(),
        "accommodation": $("#accommodation").val().replace(" ", ""),
        "accommodation_comment": $("#commentAccommodation").text().trim(),
        "other": otherData,
        "planned_expenses": $("#plannedExpenses").val().replace(" ", ""),
        "planned_expenses_comment": $("#commentPlannedExpenses").text().trim(),
    }

    return JSON.stringify(data);

}

console.log(getJsonCard())



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

$("[data-js-generate-compensation]").click(function (e) {
    console.log("click click")
   // let $button = $("[data-js-generate-compensation]")
    let $button = $(this)
    $.ajax({
        url: '/ulab/secondment/generateCompensationAjax/',
        data: {
            secondment_id: $('.form-info .secondment-id').val(),
            file_delete: $("#uploadDocuments [name=\"file_delete\"]").val()
        },
        method: 'POST',
        dataType: 'json',
        success: function (data) {
            console.log(data)
            let $btnGroup = $button.closest("[data-js-files]")
            let $btnHref = $btnGroup.find("[data-js-file-wrap]")

            $btnHref.find("a").attr("href", data.href)

         //   $hrefItem.attr("href", data.href)

            $btnGroup.find("[data-js-upload-wrap]").hide(300)
            $btnHref.show(300)
            $btnHref.removeClass("d-none")

            console.log($btnHref)
        },
        error: function (jqXHR, exception) {
            let msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status === 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status === 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            console.error(msg)
        }
    })
})

$("[data-js-add-sign]").click(function (e) {
    // console.log("add sign!")
    // console.log(JSON.parse($(this).attr("data-js-img-params")));

  //  let filePath = $(this).attr("data-js-file-path")
    let imgUrl = $(this).attr("data-js-img-path");
    let imgParams = JSON.parse($(this).attr("data-js-img-params"));

    [...$(this).parent().parent().find("a")].forEach(function (item) {
        let filePath = $(item).attr("href").split('?')[0]
        console.log(filePath)
        console.log(imgUrl)
        console.log(imgParams)

        addImgToPdf(filePath, imgUrl, imgParams);
    })

})

$("[data-js-generate-memo-doc]").click(function (e) {
    console.log("genarate doc!")
    $.magnificPopup.open({
        items: {
            src: '#memo-modal',
            type: 'inline'
        },
        fixedContentPos: false
    })
})


let transportCounter = 0;
let gsmCounter = 0;

$("body").on("keyup", "[data-js-km]", function (e) {
    let index = $(this).attr("data-js-km")
    let km = $(this).val();
    let consumptionRate = $("#consumption_rate").val()

    let gsm = km * consumptionRate / 100
    gsm = Math.round((gsm + Number.EPSILON) * 100) / 100
    $(`[data-js-gsm=${index}]`).val(gsm)
})

$("body").on('click', '#gsm-report-add', function (e) {
    const $tbody = $("#gsm-report-table").find("tbody");

    $tbody.append(`
        <tr>
            <td><input type="number" data-js-km="${gsmCounter}" name="gsmText[${gsmCounter}][km]" class="form-control"></td>
            <td><input type="number" data-js-gsm="${gsmCounter}" name="gsmText[${gsmCounter}][gsm]" class="form-control"></td>
            <td><input type="number" name="gsmText[${gsmCounter}][price]" class="form-control"></td>
            <td><input type="text" name="gsmText[${gsmCounter}][object]" class="form-control"></td>
            <td class="d-flex justify-content-center">
                <button data-js-remove-row type="button" class="btn btn-danger rounded fa-solid fa-minus"></button>
            </td>
        </tr>
    `);

    gsmCounter++;
})

$("#transport-report-add").click(function (e) {
    const $tbody = $("#transport-report-table").find("tbody");

    $tbody.append(`
        <tr>
            <td><input type="text" name="reportText[${transportCounter}][check_number]" class="form-control"></td>
            <td><input type="text" name="reportText[${transportCounter}][destination]" class="form-control"></td>
            <td><input type="date" name="reportText[${transportCounter}][travel_date]" class="form-control"></td>
            <td><input type="number" name="reportText[${transportCounter}][travel_sum]" class="form-control"></td>
            <td class="d-flex justify-content-center">
                <button data-js-remove-row type="button" class="btn btn-danger rounded fa-solid fa-minus"></button>
            </td>
        </tr>
    `);

    transportCounter++;

})

$("body").on('click', '[data-js-remove-row]', function () {
    $(this).closest("tr").remove()
})

$("#send-memo").click(function (e) {
    let form = $("#memo-modal").serialize()

    $.ajax({
        url: '/ulab/secondment/createMemoDocAjax/',
        data: form,
        method: 'POST',
        success: function (data) {
          // console.log(JSON.parse(data))
            window.location.reload()
        },
        error: function (jqXHR, exception) {
            let msg = '';
            if (jqXHR.status === 0) {
                msg = 'Not connect.\n Verify Network.';
            } else if (jqXHR.status === 404) {
                msg = 'Requested page not found. [404]';
            } else if (jqXHR.status === 500) {
                msg = 'Internal Server Error [500].';
            } else if (exception === 'parsererror') {
                msg = 'Requested JSON parse failed.';
            } else if (exception === 'timeout') {
                msg = 'Time out error.';
            } else if (exception === 'abort') {
                msg = 'Ajax request aborted.';
            } else {
                msg = 'Uncaught Error.\n' + jqXHR.responseText;
            }
            console.error(msg)
        }
    })

})

$('#oborud').select2();


