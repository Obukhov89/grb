/**
 * Результаты испытаний
 */
$(function ($) {
    let body = $('body')

    /**
     * подключение плагина select2 для подписи в протоколе и оборудования
     */
    $('.verify').select2();
    $('.equipment').select2();
    $('.material-group').select2();

    /** Сообщение об ошибки */
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


    /**
     * фактическое значение
     */
    body.on('click', '.add-value-actual', function () {
        let actualValueWrapper = $(this).parents('.actual-value-wrapper'),
            cloneActualValueWrapper = $(actualValueWrapper).clone(true),
            tdActualValue = actualValueWrapper.closest('.td-actual-value');

        cloneActualValueWrapper.find('.actual-value').val('');

        cloneActualValueWrapper.find('.add-value-actual').replaceWith(
            `<button type="button" class="btn btn-danger del-value-actual mt-0 btn-square">
                 <i class="fa-solid fa-minus icon-fix"></i>
            </button>`
        );

        tdActualValue.append(cloneActualValueWrapper);
    })

    body.on('click', '.actual-value-wrapper button.del-value-actual', function () {
        let actualValueWrapper = $(this).closest('.actual-value-wrapper');

        actualValueWrapper.remove();
    })

    /**
     * тип протокола
     */
    body.on('change', '.protocol-type', function () {
        const protocolInformationWrapper = $('.protocol-information-wrapper');

        let grainComposition = protocolInformationWrapper.find('.grain-composition'),
            protocolType = $(this).val();

        grainComposition.addClass('d-none');
        $('.' + protocolType).removeClass('d-none');
    })

    /**
     * кнопка "Присвоить номер" протоколу
     * проверка на советствие выбранных и сохраненных проб
     */
    body.on('click', '.add-protocol-number', function (e) {
        const formCreateProtocol = $(this).closest('.form-create-protocol');

        let selectedProtocolId = formCreateProtocol.find('.selected-protocol-id').val();


        if (!confirm('Вы действительно хотите присвоить номер? После присвоения номера, данные нельзя будет изменить')) {
            e.preventDefault();
            return false;
        }


        if (selectedProtocolId === '' || selectedProtocolId === null) {
            e.preventDefault();

            $('.alert-title').text('Внимание!')
            $('.alert-content').text('Внимание для присвоения номера выберите протокол')

            $.magnificPopup.open({
                items: {
                    src: $('#alert_modal'),
                    type: 'inline',
                    fixedContentPos: false
                }
            })

            return false;
        }


        if (!$(".probe-checkbox:checked").not(":disabled").length) {
            e.preventDefault();

            $('.alert-title').text('Внимание!')
            $('.alert-content').text('Для присвоения номера выберите и сохраните пробы для протокола')

            $.magnificPopup.open({
                items: {
                    src: $('#alert_modal'),
                    type: 'inline',
                    fixedContentPos: false
                }
            })

            return false;
        }


        //масив ИД ulab_material_to_request (массив выбранных проб для протокола)
        let selectedSamples = $(".probe-checkbox:checked").not(":disabled").map(function(index, elem) {
            let umtrId = $(elem).attr('name').match(/\d+/);

            if (umtrId !== null) {
                return +umtrId[0];
            }
        }).toArray();
        

        $.ajax({
            method: 'POST',
            url: '/ulab/result/checkingTrialResultsAjax',
            data: {
                selected_samples: selectedSamples,
                selected_protocol_id: selectedProtocolId
            },
            async: false,
            dataType: 'json',
            success: function (data) {
                if (!data['success']) {
                    e.preventDefault();

                    $('.alert-title').text('Внимание!')
                    $('.alert-content').text(data['error']['message'])

                    $.magnificPopup.open({
                        items: {
                            src: $('#alert_modal'),
                            type: 'inline',
                            fixedContentPos: false
                        }
                    })

                    return false
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

    /**
     * кнопка "Удалить протокол"
     */
    body.on('click', '.delete-protocol', function (e) {
        if (!confirm('Вы действительно хотите удалить протокол? После удаления протокола, данные нельзя будет восстановить')) {
            e.preventDefault();
            return false;
        }
    })

    /**
     * протокол недействителен
     */
    body.on('change', '.protocol-is-invalid', function (e) {
        if (!confirm('Вы действительно хотите признать протокол недействительным? После признания протокола недействительным, данные нельзя будет восстановить')) {
            e.preventDefault();
            $(this).prop('checked', false);
            return false;
        }

        $(this).closest('form').submit();
    })

    /**
     * сформировать протокол
     */
    body.on('change', '.btn-form-protocol', function (e) {
        console.log('btn-form-protocol')
    })


    let container = $('div.trial-results-wrapper'),
        scroll = $('#trialResultsTable').width()

    $('.arrowRight').hover(function() {
            container.animate(
                {
                    scrollLeft: scroll
                },
                {
                    duration: 4000, queue: false
                }
            )
        },
        function() {
            container.stop();
        })

    $('.arrowLeft').hover(function() {
            container.animate(
                {
                    scrollLeft: -scroll
                },
                {
                    duration: 4000, queue: false
                }
            )
        },
        function() {
            container.stop();
        })

    /**
     * добавить оборудование
     */
    body.on('change', '.equipment', function () {
        const equipmentUsed = $('.equipment-used'),
            selectedOption = $(this).find('option:selected');

        let equipmentUsedCount = equipmentUsed.length,
            selectedText = selectedOption.text(),
            selectedGost = selectedOption.data('gost'),
            selectedOborud = $(this).val(),
            aldOborudGost = JSON.parse($('#equipmentIds').val());

        aldOborudGost.push(JSON.parse(selectedOborud));
        $('#equipmentIds').val(JSON.stringify(aldOborudGost));

        let newOption = `<option value="${selectedOborud}">${selectedText}</option>`

        equipmentUsed.append(newOption)
    })

    /**
     * удалить оборудование
     */
    body.on('dblclick', '.equipment-used option', function () {
        let aldOborudGost = JSON.parse($('#equipmentIds').val());
        let iEquipmentUsed = aldOborudGost.indexOf(+this.value);
            console.log($(this))
            console.log(+this.value)
            console.log(aldOborudGost)
            console.log(iEquipmentUsed)
            console.log($(this).val())
        if (iEquipmentUsed !== -1) {
            aldOborudGost.splice(iEquipmentUsed, 1);
        } else {
            return false
        }

        $('#equipmentIds').val(JSON.stringify(aldOborudGost));
        $('.equipment-used option').eq(iEquipmentUsed).remove()
    })

    /**
     * вернуть оборудование по умолчанию
     */
    body.on('click', '.revert-default', function () {
        let protocolId = $(this).data('protocolId')

        $.ajax({
            method: 'POST',
            url: '/ulab/result/revertDefaultAjax',
            data: {
                protocol_id: protocolId
            },
            dataType: 'json',
            success: function (data) {
                if (data.success || data.error) {
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

    /**
     * выбор всех проб
     */
    body.on('change', '.all-checkbox-prob', function () {
        let isAllChecked = $(this).prop('checked');

        $('.probe-checkbox').each(function (key, checkbox) {
            let checkBox = $(checkbox);

            if (!checkBox.prop('disabled') && !isAllChecked) {
                checkBox.prop('checked', false);
            }

            if (!checkBox.prop('disabled') && isAllChecked) {
                checkBox.prop('checked', true);
            }
        });
    });

    /**
     * отображение всех проб или выбранные пробы для протокола
     */
    body.on('change', '.selected-probe', function () {
        const pathname = $(location).attr('pathname');
        let protocolId = $(this).val();

        // Если выбраны все пробы
        if (!protocolId) {
            location.href = pathname
            return false
        }

        if ( $(this).prop('checked') ) { // только выбранные пробы для протокола
            location.href = pathname + `?protocol_id=${protocolId}&selected`;
        } else { // все пробы
            location.href = location.href.replace('&selected', '');
        }
    })

    /** Получить лист измерения */
    body.on('click', '.measurement-sheet', function (e) {
        e.preventDefault();
        const btn = $(this),
            inputugtpId = $('#measurementModalForm').find('#ugtpId');
        let measurementId = $(this).data('measurement'),
            ugtpId = $(this).data('ugtp'),
            methodId = $(this).data('method');

        btn.find('i').addClass('spinner-animation');
        btn.addClass('disabled');

        if (btn.find('.fa-calculator').length > 0) {
            btn.prop('disabled', true);
            btn.find('.fa-calculator').addClass('d-none');
            btn.append(
                `<div class="spinner-border" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>`
            );
        }

        $('.measurement-wrapper').remove();

        $.ajax({
            method: 'POST',
            url: '/ulab/result/getMeasurementSheetAjax',
            data: {
                measurement_id: measurementId,
                ugtp_id: ugtpId,
                method_id: methodId,
            },
            success: function (data) {
                e.preventDefault();

                inputugtpId.val(ugtpId);
                $('#measurementModalForm').append(data);

                $.magnificPopup.open({
                    items: {
                        src: $('#measurementModalForm'),
                        type: 'inline',
                        fixedContentPos: false
                    },
                    closeOnBgClick: false
                })

                btn.find('i').removeClass('spinner-animation');
                btn.removeClass('disabled');

                if (btn.find('.fa-calculator').length > 0) {
                    btn.prop('disabled', false);
                    btn.find('.fa-calculator').removeClass('d-none');
                    btn.find('.spinner-border').remove();
                }

                return false
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

    // /** Сохранить данные листа измерений */
    // $('#measurementModalForm').on('submit', function(e) {
    //     e.preventDefault();
    //     let ugtpId = $(this).find('#ugtpId').val(),
    //         actualValue = $(this).find('.actual-value').val(),
    //         formData = $(this).serialize();
    //     $.ajax({
    //         method: 'POST',
    //         url: '/ulab/result/saveMeasurementDataAjax',
    //         data: {
    //             ugtp_id: ugtpId,
    //             form_data: formData
    //         },
    //         success: function (data) {
    //             e.preventDefault();
    //
    //             if (data) {
    //                 location.reload();
    //             }
    //
    //             return false
    //         },
    //         error: function (jqXHR, exception) {
    //             let msg = '';
    //             if (jqXHR.status === 0) {
    //                 msg = 'Not connect.\n Verify Network.';
    //             } else if (jqXHR.status === 404) {
    //                 msg = 'Requested page not found. [404]';
    //             } else if (jqXHR.status === 500) {
    //                 msg = 'Internal Server Error [500].';
    //             } else if (exception === 'parsererror') {
    //                 msg = 'Requested JSON parse failed.';
    //             } else if (exception === 'timeout') {
    //                 msg = 'Time out error.';
    //             } else if (exception === 'abort') {
    //                 msg = 'Ajax request aborted.';
    //             } else {
    //                 msg = 'Uncaught Error.\n' + jqXHR.responseText;
    //             }
    //             console.error(msg)
    //         }
    //     })
    // });

    /**
     * Проверка данных у протокола перед формированием
     */
    body.on('click', '.validate-protocol', function () {
        let success = true
        let protocolId = $(this).data('protocol_id')

        $.ajax({
            method: 'POST',
            cache: false,
            async: false,
            url: '/ulab/protocol/validateProtocolAjax/',
            dataType: 'json',
            data: {
                protocol_id: protocolId
            },
            success: function (data) {
                console.log(data)
                if ( data.success ) {
                    success = true
                } else {
                    success = false
                    $.each(data.errors, function (i, item) {
                        showErrorMessage(item)
                    })
                    window.scrollTo(0,0)
                }
            }
        })

        return success
    })

    /**
     * Проверка условия у протокола перед формированием
     */
    body.on('click', '.validate-conditions', function () {
        let success = true
        let protocolId = $(this).data('protocol_id')

        $.ajax({
            method: 'POST',
            cache: false,
            async: false,
            url: '/ulab/result/validateProtocolAjax/',
            dataType: 'json',
            data: {
                protocol_id: protocolId
            },
            success: function (data) {
                console.log(data)
                if ( data.success ) {
                    success = true
                } else {
                    success = false
                    $.each(data.errors, function (i, item) {
                        showErrorMessage(item)
                    })
                    window.scrollTo(0,0)
                }
            }
        })

        return success
    })

    /**
     * начать испытание (для одной методике)
     */
    body.on('click', '.btn-start', function () {
        const roomsModalForm = $('#roomsModalForm'),
            title = roomsModalForm.find('.title'),
            _this = $(this);
        let ugtpId = _this.data('ugtp'),
            protocolId = _this.data('protocol');

        roomsModalForm.find('#accordionFlush').remove();

        _this.prop('disabled', true);
        _this.find('.icon-start').addClass('d-none');
        _this.append(
            `<div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>`
        );

        $.ajax({
            method: 'POST',
            url: '/ulab/result/startTrialAjax',
            data: {
                ugtp_id: ugtpId,
                protocol_id: protocolId,
            },
            dataType: 'json',
            success: function (result) {
                $.magnificPopup.close();
                let countErrors = Object.keys(result.errors).length;

                console.log(result.success)

                console.log(result)

                if (result.success) {
                    let dealId = $("input[name=deal_id]").val();
                    let userId = $("#user-id-input").val();

                    location.reload();

                    return false;
                }

                _this.prop('disabled', false);
                _this.find('.icon-start').removeClass('d-none');
                _this.find('.spinner-border').remove();

                // выводим ошибки
                if (!result.success && countErrors) {
                    $.each(result.errors, function (i, item) {
                        console.log('item', item);
                        showErrorMessage(item)
                    })
                    window.scrollTo(0, 0);

                    return false;
                }

                // Если к методике привязано более 1 помещения, то модальное окно выбора помещения
                if (!result.success && !countErrors) {
                    title.text('Выберите помещение для испытаний');

                    let methodHtml = '',
                        roomsHtml = '';
                    $.each(result.data, function (materialId, material) {
                        $.each(material['probe'], function (probeId, probe) {
                            $.each(probe['method'], function (gostId, method) {
                                $.each(probe['rooms'][gostId], function (roomKey, room) {
                                    roomsHtml += getHtmlRooms(room, gostId);
                                });

                                methodHtml += getHtmlMethod(method, roomsHtml)
                            });
                        });
                    });

                    let contentHtml = `<div class="rooms-wrapper" id="accordionFlush">
                                            ${methodHtml}
                                            <div class="line-dashed-small"></div>
                                            <button type="submit" class="btn btn-primary save" form="roomsModalForm" 
                                            data-ugtp="${ugtpId}">
                                                Сохранить
                                            </button>
                                            </div>`;


                    if (methodHtml) {
                        roomsModalForm.append(contentHtml)

                        $.magnificPopup.open({
                            items: {
                                src: roomsModalForm,
                                type: 'inline',
                            }
                        })
                    }

                    return false;
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
        });
    });

    /**
     * сохраняем выбранные помещения для испытаний
     */
    $('#roomsModalForm').on('submit', function (e) {
        e.preventDefault();

        const roomsModalForm = $('#roomsModalForm'),
            button = roomsModalForm.find('button'),
            rooms = roomsModalForm.find('.room');

        let selectedRooms = $(this).serialize()
        ugtpId = button.data('ugtp');

        //Проверка выбора помещений
        let roomsEmpty = rooms.filter(function () {
            if ( $(this).prop('checked') ) {
                return $(this);
            }
        })

        roomsModalForm.find(".messages").remove();
        if (!roomsEmpty.length) {
            let messageError = "Внимание! Не выбраны помещения для испытания!";
            let messageErrorContent = getMessageErrorContent(messageError);

            roomsModalForm.prepend(messageErrorContent);
            return false;
        }

        roomsModalForm.find('button[type="submit"]').replaceWith(
            `<button class="btn btn-primary" type="button" disabled>
                <span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>
                Сохранение...
            </button>`
        );

        $.ajax({
            method: 'POST',
            url: '/ulab/result/saveSelectedRoomsAjax',
            data: {
                rooms: selectedRooms,
            },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    $.magnificPopup.close();

                    if (button.hasClass('save-all')) {
                        $(".date-begin").trigger("change");
                    } else if (button.hasClass('save')) {
                        $(`.btn-start[data-ugtp='${ugtpId}']`).trigger("click");
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
        });
    });

    body.on('click', '.btn-stop', function () {
        let a = body.find('.btn-stop'),
            b = []
        $.each(a, function (i, val) {
            let z = $(val).data('ugtp')
            console.log(z)
            b.push(z)
        })
        console.log(b)
    })

    /**
     * остановить испытание (для одной методике)
     */
    body.on('click', '.btn-stop', function () {
        const _this = $(this);
        let ugtpId = _this.data('ugtp'),
            protocolId = _this.data('protocol');

        _this.prop('disabled', true);
        _this.find('.fill-danger').addClass('d-none');
        _this.append(
            `<div class="spinner-border text-danger" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>`
        );

        $.ajax({
            method: 'POST',
            url: '/ulab/result/stopTrialAjax',
            data: {
                ugtp_id: ugtpId,
                protocol_id: protocolId,
            },
            dataType: 'json',
            success: function (data) {
                let countErrors = Object.keys(data.errors).length;

                if (data.success) {

                    let dealId = $("input[name=deal_id]").val();
                    let userId = $("#user-id-input").val();

                    location.reload();
                    return false;
                }

                _this.prop('disabled', false);
                _this.find('.fill-danger').removeClass('d-none');
                _this.find('.spinner-border').remove();

                // выводим ошибки
                if (!data.success && countErrors) {
                    $.each(data.errors, function (i, item) {
                        console.log('item', item);
                        showErrorMessage(item)
                    })
                    window.scrollTo(0, 0)
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
        });
    });

    /**
     * приостановить испытание (для одной методики)
     */
    body.on('click', '.btn-pause', function () {
        const _this = $(this);
        let ugtpId = _this.data('ugtp'),
            protocolId = _this.data('protocol');

        _this.prop('disabled', true);
        _this.find('.icon').addClass('d-none');
        _this.append(
            `<div class="spinner-border" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>`
        );

        $.ajax({
            method: 'POST',
            url: '/ulab/result/pauseTrialAjax',
            data: {
                ugtp_id: ugtpId,
                protocol_id: protocolId,
            },
            dataType: 'json',
            success: function (data) {
                let countErrors = Object.keys(data.errors).length;

                if (data.success) {
                    location.reload();
                    return false;
                }

                _this.prop('disabled', false);
                _this.find('.icon').removeClass('d-none');
                _this.find('.spinner-border').remove();

                // выводим ошибки
                if (!data.success && countErrors) {
                    $.each(data.errors, function (i, item) {
                        console.log('item', item);
                        showErrorMessage(item)
                    })
                    window.scrollTo(0, 0)
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
        });
    });


    let prevDateBegin = $('.date-begin').val();
    let prevDateEnd = $('.date-end').val();
    /**
     * Проверить данные перед началом испытаний при изменения даты в протоколе начала/окончания испытаний
     */
    body.on('change', '.date-trials', function () {
        const roomsModalForm = $('#roomsModalForm'),
            title = roomsModalForm.find('.title'),
            _this = $(this);

        let protocolId = $(this).data('protocol'),
            $dateStart = $('.date-begin').val(),
            $dateEnd = $('.date-end').val();

        $('#roomsModalForm').find('#accordionFlush').remove();

        if (protocolId) {
            $.ajax({
                method: 'POST',
                url: '/ulab/result/checkTrialsDataAjax',
                data: {
                    protocol_id: protocolId,
                    date_start: $dateStart,
                    date_end: $dateEnd,
                },
                dataType: 'json',
                success: function (result) {
                    let countErrors = Object.keys(result.errors).length;

                    // выводим ошибки
                    if (!result.success && countErrors) {
                        $.magnificPopup.close();

                        if (_this.hasClass('date-begin')) {
                            $('.date-begin').val(prevDateBegin);
                        }

                        if (_this.hasClass('date-end')) {
                            $('.date-end').val(prevDateBegin);
                        }

                        $.each(result.errors, function (i, item) {
                            showErrorMessage(item)
                        })
                        window.scrollTo(0, 0)

                        return false;
                    }

                    // Если к методике привязано более 1 помещения, то модальное окно выбора помещения
                    if (!result.success && !countErrors) {
                        $.magnificPopup.close();

                        title.text('Выберите помещение для испытаний');

                        let materialHtml = '';
                        $.each(result.data, function (materialId, material) {
                            let probeHtml = '';

                            $.each(material['probe'], function (probeId, probe) {
                                let methodHtml = '',
                                    roomsHtml = '';

                                $.each(probe['method'], function (gostId, method) {

                                    $.each(probe['rooms'][gostId], function (roomKey, room) {
                                        roomsHtml += getHtmlRooms(room, gostId);
                                    });

                                    methodHtml += getHtmlMethod(method, roomsHtml)
                                });

                                probeHtml += getHtmlProbe(material, probe, methodHtml)
                            });

                            materialHtml += getHtmlMaterial(material, probeHtml)
                        });

                        let contentHtml = `<div class="accordion accordion-flush mb-3 material-block" id="accordionFlush">
                                            ${materialHtml}
                                            <div class="line-dashed-small"></div>
                                            <button type="submit" class="btn btn-primary save-all" form="roomsModalForm">
                                                Сохранить
                                            </button>
                                            </div>`;

                        if (materialHtml) {
                            roomsModalForm.append(contentHtml)

                            $.magnificPopup.open({
                                modal: true,
                                items: {
                                    src: roomsModalForm,
                                    type: 'inline',
                                }
                            })
                        }

                        return false;
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
            });
        }
    });

    /**
     * Свернуть пробы
     */
    body.on('click', '.expand-all', function () {
        $(this).parents('.accordion-body').find('.accordion .collapse').collapse('hide')
    })

    /**
     * Развернуть пробы
     */
    body.on('click', '.collapse-all', function () {
        $(this).parents('.accordion-body').find('.accordion .collapse').collapse('show')
    })

    /**
     * Изменить дату испытаний
     */
    body.on('change', '.change-trials-date', function () {
        const dateBegin = $('.date-begin'),
            dateEnd = $('.date-end');

        let isChecked = $(this).prop('checked');

        if (isChecked) {
            if (!confirm('Вы действительно хотите изменить дату испытаний? После изменения, данные нельзя будет востановить')) {
                $(this).prop('checked', false);
                return false;
            }

            //TODO: костыль Саша проверь
            // TODO: Проверил, Будет не соответвиет темпиратуры и тд в помещении т.к. проводятся паралельно испытания с другой темпиратурой,
            // нужно доробатывать функционал по внесению теприратур для каждой методики
            dateBegin.removeClass('date-trials');
            dateEnd.removeClass('date-trials');

            dateBegin.prop('readonly', false);
            dateEnd.prop('readonly', false);
        }

        if (!isChecked) {
            dateBegin.prop('readonly', true);
            dateEnd.prop('readonly', true);

            dateBegin.addClass('date-trials');
            dateEnd.addClass('date-trials');
        }

        isChecked ? dateBegin.removeClass('bg-light-secondary').addClass('bg-white') :
            dateBegin.removeClass('bg-white').addClass('bg-light-secondary');
        isChecked ? dateEnd.removeClass('bg-light-secondary').addClass('bg-white') :
            dateEnd.removeClass('bg-white').addClass('bg-light-secondary');
    });

    body.on('change', '.additional-information', function () {
        let text = $(this).text()

        text.replace(/\n/g, '<w:br/>')
        console.log(text)
    })

    /**
     * Изменить условия испытаний
     */
    body.on('change', '.change-trials-conditions', function () {
        const conditionsWrapper = $('.conditions-wrapper'),
            inputConditions = conditionsWrapper.find('input'),
            temp1 = $('.temp1'),
            temp2 = $('.temp2'),
            wet1 = $('.wet1'),
            wet2 = $('.wet2');

        let isChecked = $(this).prop('checked'),
            protocolId = $(this).data('protocol');

        //Если чекбокс "Изменить условия испытаний" отмечен, то получаем сохраненные данные условий эксплуатации из текущего протокола
        if (isChecked) {
            inputConditions.prop('readonly', false);
            inputConditions.removeClass('bg-light-secondary');

            $.ajax({
                method: 'POST',
                url: '/ulab/result/getProtocolAjax',
                data: {
                    protocol_id: protocolId,
                },
                dataType: 'json',
                success: function (result) {
                    if (result.success) {
                        let isEmpty = !+result.data['TEMP_O'] && !+result.data['TEMP_TO_O'] && !+result.data['VLAG_O'] && !+result.data['VLAG_TO_O'];

                        // Если все значения пустые то оставляем предыдущие значения(из журнала условий)
                        if (!isEmpty) {
                            temp1.val(result.data['TEMP_O']);
                            temp2.val(result.data['TEMP_TO_O']);
                            wet1.val(result.data['VLAG_O']);
                            wet2.val(result.data['VLAG_TO_O']);
                        }
                    } else {
                        $.each(data.errors, function (i, item) {
                            showErrorMessage(item)
                        })
                        window.scrollTo(0, 0)
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
            });
        }

        //Если чекбокс "Изменить условия испытаний" не отмечен, то получаем сохраненные данные условий эксплуатации из журнала условий
        if (!isChecked) {
            inputConditions.prop('readonly', true);

            $.each(inputConditions, function (i, item) {
                if (!$(item).hasClass('bg-light-secondary')) {
                    $(item).addClass('bg-light-secondary');
                }
            });

            $.ajax({
                method: 'POST',
                url: '/ulab/result/getConditionsAjax',
                data: {
                    protocol_id: protocolId,
                },
                dataType: 'json',
                success: function (result) {
                    console.log('result', result);
                    if (result.success) {
                        temp1.val(result.data['min_temp']);
                        temp2.val(result.data['max_temp']);
                        wet1.val(result.data['min_humidity']);
                        wet2.val(result.data['max_humidity']);
                    } else {
                        $.each(data.errors, function (i, item) {
                            showErrorMessage(item)
                        })
                        window.scrollTo(0, 0)
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
            });
        }
    });

    $('#formResult').on('submit', function (e) {
        const formResult = $('#formResult');

        formResult.find('button[type="submit"]').replaceWith(
            `<button class="btn btn-primary" type="button" disabled>
                <span class="spinner-grow spinner-grow-sm spinner-save" role="status" aria-hidden="true"></span>
                Сохранение...
            </button>`
        );
    });

    $('#protocolInformation').on('submit', function (e) {
        const protocolInformation = $('#protocolInformation');

        protocolInformation.find('button[type="submit"]').replaceWith(
            `<button class="btn btn-primary" type="button" disabled>
                <span class="spinner-grow spinner-grow-sm spinner-save" role="status" aria-hidden="true"></span>
                Сохранение...
            </button>`
        );
    });

    body.on('click', '.protocol-information', function () {
        const _this = $(this),
            protocolInformation = $('#protocolInformation'),
            dealId = +protocolInformation.find('input[name="deal_id"]').val();

        let protocolId = $(this).data('protocol');

        _this.prop('disabled', true);
        _this.find('.icon').addClass('d-none');
        _this.append(
            `<div class="spinner-border" role="status">
                <span class="visually-hidden">Загрузка...</span>
            </div>`
        );

        // Сбрасываем значения формы
        protocolInformation.find('.verify').html('');
        protocolInformation.find('.equipment-used').html('');
        protocolInformation.find('.equipment').html('');
        protocolInformation.find('#equipmentIds').val('');
        protocolInformation[0].reset()

        $.ajax({
            method: 'POST',
            url: '/ulab/result/getProtocolInfoAjax',
            data: {
                deal_id: dealId,
                protocol_id: protocolId,
            },
            dataType: "json",
            success: function (data) {
                console.log(data)
                let count = Object.keys(data).length

                if (count) {
                    let verify = protocolInformation.find('.verify'),
                        equipmentUsed = protocolInformation.find('.equipment-used'),
                        equipment = protocolInformation.find('#equipment');

                    $.each(data['assigned'], function(key, value) {
                        verify.append(`<option value="${value['user_id']}">${value['user_name']}</option>`);
                    });

                    $.each(data['protocol_equipment'], function(key, value) {
                        equipmentUsed.append(
                            `<option value="${value['b_o_id']}" class="${value['bg_color']}">
                                ${value['TYPE_OBORUD']} ${value['OBJECT']}, инв. номер ${value['REG_NUM']}
                            </option>`
                        );
                    });

                    $.each(data['oboruds'], function(key, value) {
                        equipment.append(
                            `<option value="${value['o_id']}" data-gost="${value['g_id']}">
                                ${value['TYPE_OBORUD']} ${value['OBJECT']}
                                , инв. номер ${value['REG_NUM']} ${value['reg_doc']}, ${value['clause']}
                            </option>`
                        );
                    });

                    let protocolNum = data['protocol']['NUMBER'] ? data['protocol']['NUMBER'] : 'Номер не присвоен';

                    protocolInformation.find('.title').text(`Информация по протоколу № ${protocolNum}`);
                    protocolInformation.find('input[name="protocol_id"]').val(protocolId);

                    // Общая информация
                    let protocolType = data['protocol']['PROTOCOL_TYPE'] ? data['protocol']['PROTOCOL_TYPE'] : 0;
                    protocolInformation.find('.protocol-type').val(protocolType);
                    protocolInformation.find('.verify').val(data['protocol']['verify']);
                    protocolInformation.find('.no-evaluate').prop('checked', +data['protocol']['NO_COMPLIANCE']);

                    //Информация об испытаниях
                    let dateBegin = protocolInformation.find('.date-begin'),
                        dateEnd = protocolInformation.find('.date-end'),
                        temp1 = protocolInformation.find('.temp1'),
                        temp2 = protocolInformation.find('.temp2'),
                        wet1 = protocolInformation.find('.wet1'),
                        wet2 = protocolInformation.find('.wet2'),
                        changeTrialsConditions = protocolInformation.find('.change-trials-conditions'),
                        attestatInProtocol = protocolInformation.find('.attestat-in-protocol'),
                        switchAttestat = attestatInProtocol.closest('.switch'),
                        isChangeTrialsConditions = +data['protocol']['CHANGE_TRIALS_CONDITIONS'],
                        changeTrialsDate = +data['protocol']['CHANGE_TRIALS_DATE'] || data['is_deal_osk'];

                    dateBegin.val(data['dates_trials']['date_begin']);
                    dateBegin.prop('readonly', !changeTrialsDate);
                    dateBegin.data('protocol', protocolId);
                    changeTrialsDate ? dateBegin.removeClass('bg-light-secondary').addClass('bg-white') :
                        dateBegin.removeClass('bg-white').addClass('bg-light-secondary');

                    dateEnd.val(data['dates_trials']['date_end']);
                    dateEnd.prop('readonly', !changeTrialsDate);
                    dateEnd.data('protocol', protocolId);
                    changeTrialsDate ? dateEnd.removeClass('bg-light-secondary').addClass('bg-white') :
                        dateEnd.removeClass('bg-white').addClass('bg-light-secondary');

                    temp1.val(data['conditions']['TEMP_O']);
                    temp1.prop('readonly', !isChangeTrialsConditions);
                    isChangeTrialsConditions ? temp1.removeClass('bg-light-secondary').addClass('bg-white') :
                        temp1.removeClass('bg-white').addClass('bg-light-secondary');

                    temp2.val(data['conditions']['TEMP_TO_O']);
                    temp2.prop('readonly', !isChangeTrialsConditions);
                    isChangeTrialsConditions ? temp2.removeClass('bg-light-secondary').addClass('bg-white') :
                        temp2.removeClass('bg-white').addClass('bg-light-secondary');

                    wet1.val(data['conditions']['VLAG_O']);
                    wet1.prop('readonly', !isChangeTrialsConditions);
                    isChangeTrialsConditions ? wet1.removeClass('bg-light-secondary').addClass('bg-white') :
                        wet1.removeClass('bg-white').addClass('bg-light-secondary');

                    wet2.val(data['conditions']['VLAG_TO_O']);
                    wet2.prop('readonly', !isChangeTrialsConditions);
                    isChangeTrialsConditions ? wet2.removeClass('bg-light-secondary').addClass('bg-white') :
                        wet2.removeClass('bg-white').addClass('bg-light-secondary');

                    protocolInformation.find('.change-trials-date').prop('checked', +data['protocol']['CHANGE_TRIALS_DATE']);
                    changeTrialsConditions.data('protocol', protocolId);
                    changeTrialsConditions.prop('checked', +data['protocol']['CHANGE_TRIALS_CONDITIONS']);
                    protocolInformation.find('.output-in-protocol').prop('checked', +data['protocol']['OUTPUT_IN_PROTOCOL']);

                    //Информация об оборудовании
                    protocolInformation.find('.revert-default').data('protocolId', protocolId);
                    protocolInformation.find('#equipmentIds').val(data['equipment_ids_json']);

                    //Данные объекта испытаний
                    protocolInformation.find('.object-description').val(data['object_data']['DESCRIPTION']);
                    protocolInformation.find('.object').val(data['object_data']['OBJECT']);
                    protocolInformation.find('.place-probe').val(data['object_data']['PLACE_PROBE']);
                    protocolInformation.find('.date-probe').val(data['object_data']['DATE_PROBE']);
                    protocolInformation.find('.additional-information').val(data['protocol']['DOP_INFO']);
                    protocolInformation.find('.protocol-outside-lis').prop('checked', +data['protocol']['PROTOCOL_OUTSIDE_LIS']);
                    attestatInProtocol.prop('checked', +data['protocol']['ATTESTAT_IN_PROTOCOL']);
                    // attestatInProtocol.prop('disabled', !+data['protocol']['IN_ATTESTAT_DIAPASON']); // Если значение или методика у протокола НЕ в диапазоне аттестата(не соответствуют условиям аттестации), не в ОА, то выдать(выбрать) протокол "C аттестатом аккредитации" нельзя
                    //
                    // if (!+data['protocol']['IN_ATTESTAT_DIAPASON']) {
                    //     if (!switchAttestat.hasClass('opacity-30')) {
                    //         switchAttestat.addClass('opacity-30');
                    //         switchAttestat.prop('title', 'Значение или методика у протокола не в ОА или в ОА но не подтверждено');
                    //     }
                    // } else {
                    //     switchAttestat.removeClass('opacity-30');
                    //     switchAttestat.prop('title', '');
                    // }
                }

                $.magnificPopup.open({
                    items: {
                        src: protocolInformation,
                        type: 'inline',
                        fixedContentPos: false
                    },
                    closeOnBgClick: false
                });

                _this.prop('disabled', false);
                _this.find('.icon').removeClass('d-none');
                _this.find('.spinner-border').remove();

                protocolInformation.find('.select2').select2({
                    theme: 'bootstrap-5',
                    width: 'resolve',
                })
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
                    msg = '1 Requested JSON parse failed.';
                } else if (exception === 'timeout') {
                    msg = 'Time out error.';
                } else if (exception === 'abort') {
                    msg = 'Ajax request aborted.';
                } else {
                    msg = 'Uncaught Error.\n' + jqXHR.responseText;
                }
                console.log(msg)
            }
        });
    });

    $('.is-confirm-oa').on('click', function () {
        let confirmOaSwitch = $(this).closest('.wrapper-confirm-oa').find('.confirm-oa-switch'),
            confirmOaElem = $(this).closest('.wrapper-confirm-oa').find('.confirm-oa-elem');

        $(this).addClass('d-none');
        confirmOaSwitch.removeClass('d-none');
        confirmOaElem.prop('disabled', false);
    });

});

function getHtmlRooms(room, gostId) {
    return `<label class="list-group-item">
                <input class="form-check-input me-1 room" type="checkbox" name="rooms[${gostId}][]" value="${room['ID']}">
                ${room['name']}
            </label>`;
}

function getHtmlMethod(method, roomsHtml) {
    return `<div class="mb-3 method-wrapper" data-method="${method['id']}">${method['view_gost']}${roomsHtml}</div>`;
}

function getHtmlProbe(material, probe, methodHtml) {
    return `<div class="accordion-item probe-item" data-probe="${probe['probe_id']}">
                <h2 class="accordion-header" id="panelsStayOpen-heading${material['material_id']}-${probe['probe_id']}">
                    <div class="accordion-button w-auto" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapse${material['material_id']}-${probe['probe_id']}" aria-expanded="true" aria-controls="panelsStayOpen-collapse${material['material_id']}-${probe['probe_id']}">
                        ${probe['cipher']}
                    </div>
                </h2>
                <div id="panelsStayOpen-collapse${material['material_id']}-${probe['probe_id']}" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-heading${material['material_id']}-${probe['probe_id']}">
                    <div class="accordion-body border-box">
                        <div class="row">
                            <div class="col">
                                <div class="list-group">
                                    ${methodHtml}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>`;
}

function getHtmlMaterial(material, probeHtml) {
    return `<div class="accordion-item material-item" data-material="${material['material_id']}">
                    <h2 class="accordion-header" id="flush-heading${material['material_id']}">
                        <div class="accordion-button ps-0 collapsed w-auto" data-bs-toggle="collapse" data-bs-target="#flush-collapse${material['material_id']}" aria-expanded="false" aria-controls="flush-collapse${material['material_id']}">
                            <input class="form-check-input ms-3 me-3 material-check" type="checkbox" data-bs-toggle="collapse" data-bs-target="#qq" title="Применить ко всем пробам">
                            ${material['material_name']}
                        </div>
                    </h2>
                    <div id="flush-collapse${material['material_id']}" class="accordion-collapse collapse" aria-labelledby="flush-heading${material['material_id']}" data-bs-parent="#accordionFlush">
                        <div class="accordion-body border-box">

                            <div class="row justify-content-end mb-3">
                                <div class="col-auto">
                                    <button type="button" class="btn btn-primary btn-square expand-all" title="Свернуть все пробы">
                                        <i class="fa-solid fa-angles-up"></i>
                                    </button>
                                </div>

                                <div class="col-auto">
                                    <button type="button" class="btn btn-primary btn-square collapse-all" title="Развернуть все пробы">
                                        <i class="fa-solid fa-angles-down"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="line-dashed"></div>

                            <div class="accordion probe-block" id="accordionPanelsStayOpen${material['material_id']}">
                                ${probeHtml}
                            </div>
                        </div>
                    </div>
                </div>`;
}
