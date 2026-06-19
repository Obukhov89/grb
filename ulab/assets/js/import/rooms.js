/**
 * Карточка несение сведений об отделах и помещениях
 */

$(function ($) {
    const body = $('body')

    $('.oborud-method').on('click', function () {

        let roomId = $(this).data('roomid')

        $.ajax({
            method: 'POST',
            url: '/ulab/import/getMethodOborudForRoomAjax',
            data: {
                id: roomId
            },
            dataType: "json",
            success: function (data) {

                let oboruds = data.oboruds

                console.log(data)

                document.querySelector('.room-number').innerHTML = `Помещение: ${data.room}`

                $('.method-block').empty()
                $('.oborud-block').empty()

                $.each(oboruds, function (v, x) {
                    $('.oborud-block').append(
                        `
                        <p>${x['OBJECT']}</p>
                        `
                    )
                })

                let methods = data.methods

                $.each(methods, function (v, x) {
                    $('.method-block').append(
                        `
                        <p>${x['name']}</p>
                        `
                    )
                })

                $.magnificPopup.open({
                    items: {
                        src: '#oborud-method',
                        type: 'inline'
                    },
                    fixedContentPos: false,
                    fixedBgPos: true,
                })
            },
            error: function (jqXHR, exception) {
                editButton.find('i').removeClass('fa-spinner');
                editButton.find('i').addClass('fa-xmark');

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


    })

    // /** modal */
    // $('.popup-with-form').magnificPopup({
    //     items: {
    //         src: '#room-modal-form',
    //         type: 'inline'
    //     },
    //     fixedContentPos: false,
    //     fixedBgPos: true,
    //     callbacks: {
    //         open: function() {
    //             const roomModalForm = $('#room-modal-form');
    //             roomModalForm.find('.form-button').text('Добавить помещение');
    //
    //             if (roomModalForm.find('#roomId').val() != '')
    //             {
    //                 roomModalForm.find('#roomId').val('');
    //                 roomModalForm.find('#number').val('');
    //                 roomModalForm.find('#name').val('');
    //                 roomModalForm.find('#spec').val('');
    //                 roomModalForm.find('#purpose').val('');
    //                 roomModalForm.find('#area').val('');
    //                 roomModalForm.find('#params').val('');
    //                 // roomModalForm.find('#specEquip').val('');
    //                 roomModalForm.find('#docs').val('');
    //                 roomModalForm.find('#placement').val('');
    //                 roomModalForm.find('#comment').val('');
    //
    //                 roomModalForm.find('.room-delete').remove();
    //             }
    //
    //              $.ajax({
    //                 method: 'POST',
    //                 url: '/ulab/import/getUnboundOborudAjax',
    //                 dataType: "json",
    //                 success: function (data) {
    //                     updateSelects(data['equipment_storaged'], data['equipment_operating']);
    //                 },
    //                 error: function (jqXHR, exception) {
    //                     let msg = '';
    //                     if (jqXHR.status === 0) {
    //                         msg = 'Not connect.\n Verify Network.';
    //                     } else if (jqXHR.status === 404) {
    //                         msg = 'Requested page not found. [404]';
    //                     } else if (jqXHR.status === 500) {
    //                         msg = 'Internal Server Error [500].';
    //                     } else if (exception === 'parsererror') {
    //                         msg = '1 Requested JSON parse failed.';
    //                     } else if (exception === 'timeout') {
    //                         msg = 'Time out error.';
    //                     } else if (exception === 'abort') {
    //                         msg = 'Ajax request aborted.';
    //                     } else {
    //                         msg = 'Uncaught Error.\n' + jqXHR.responseText;
    //                     }
    //                     console.log(msg)
    //                 }
    //             });
    //         }
    //     }
    // })

    body.on('click', '.popup-with-form', function () {
        $.magnificPopup.open({
            items: {
                src: '#room-modal-form',
                type: 'inline'
            },
            fixedContentPos: false,
            fixedBgPos: true,
            callbacks: {
                open: function() {
                    const roomModalForm = $('#room-modal-form');
                    roomModalForm.find('.form-button').text('Добавить помещение');

                    if (roomModalForm.find('#roomId').val() != '')
                    {
                        roomModalForm.find('#roomId').val('');
                        roomModalForm.find('#number').val('');
                        roomModalForm.find('#name').val('');
                        roomModalForm.find('#spec').val('');
                        roomModalForm.find('#purpose').val('');
                        roomModalForm.find('#area').val('');
                        roomModalForm.find('#params').val('');
                        // roomModalForm.find('#specEquip').val('');
                        roomModalForm.find('#docs').val('');
                        roomModalForm.find('#placement').val('');
                        roomModalForm.find('#comment').val('');

                        roomModalForm.find('.room-delete').remove();
                    }

                    $.ajax({
                        method: 'POST',
                        url: '/ulab/import/getUnboundOborudAjax',
                        dataType: "json",
                        success: function (data) {
                            updateSelects(data['equipment_storaged'], [], 0);
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
                }
            }
        })
    })

    /**
     * выбрать отдел
     */
    body.on('change', '#labs', function () {
        let labId = $(this).val();

        location.href=`/ulab/import/rooms/${labId}`;
    });

    /**
     * обновить данные помещения
     */
    body.on('click', '.room-edit', function () {
        let roomId = $(this).data('roomId');

        let editButton = $(this);
        let oldText = editButton.text();
        editButton.find('i').addClass('fa-spinner');
        editButton.find('i').removeClass('fa-pencil');
        editButton.find('i').removeClass('fa-xmark');

        $.ajax({
            method: 'POST',
            url: '/ulab/import/getRoomAjax',
            data: {
                id: roomId
            },
            dataType: "json",
            success: function (data) {
                editButton.find('i').removeClass('fa-spinner');
                editButton.find('i').addClass('fa-pencil');

                if (data['ID']) {
                    const roomModalForm = $('#room-modal-form');

                    roomModalForm.find('.form-button').text('Сохранить помещение');

                    roomModalForm.find('#roomId').val(roomId);
                    //roomModalForm.find('#labId').val(data['LAB_ID']);
                    roomModalForm.find('#number').val(data['NUMBER']);
                    roomModalForm.find('#name').val(data['NAME']);
                    roomModalForm.find('#spec').val(data['SPEC']);
                    roomModalForm.find('#purpose').val(data['PURPOSE']);
                    roomModalForm.find('#area').val(data['AREA']);
                    roomModalForm.find('#params').val(data['PARAMS']);
                    //roomModalForm.find('#specEquip').val(data['SPEC_EQUIP']);
                    roomModalForm.find('#docs').val(data['DOCS']);
                    roomModalForm.find('#placement').val(data['PLACEMENT']);
                    roomModalForm.find('#comment').val(data['COMMENT']);

                    updateSelects(data['equipment_storaged'], [], roomId);

                    roomModalForm.find('.room-delete').remove();
                    roomModalForm.append(`<button type="button" class="btn btn-danger ms-2 room-delete" 
                        data-room-id="${roomId}">Удалить</button>`);

                    $.magnificPopup.open({
                        items: {
                            src: roomModalForm,
                            type: 'inline',
                            fixedContentPos: false
                        }
                    });
                }
            },
            error: function (jqXHR, exception) {
                editButton.find('i').removeClass('fa-spinner');
                editButton.find('i').addClass('fa-xmark');

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

    /**
     * удалить данные помещения
     */
    body.on('click', '.room-delete', function () {
        let roomId = $(this).data('roomId');

        if (!confirm('Вы действительно хотите удалить помещение? После удаления, данные нельзя будет востановить')) {
            return false;
        }

        const roomModalForm = $('#room-modal-form');
        let oldText = roomModalForm.find('.room-delete').text();
        roomModalForm.find('.room-delete').text('Подождите, происходит удаление');
        roomModalForm.find('.form-button').addClass('disabled');

        $.ajax({
            method: 'POST',
            url: '/ulab/import/deleteRoomAjax',
            data: {
                id: roomId
            },
            dataType: "json",
            success: function (data) {
                $.magnificPopup.close()

                if (data['success']) {
                    roomModalForm.find('.room-delete').text(oldText);
                    roomModalForm.find('.form-button').removeClass('disabled');
                    location.reload();
                } else {
                    $('.alert-title').text('Внимание!')
                    $('.alert-content').text(data['error']['message'])

                    roomModalForm.find('.form-button').text('Ошибка');
                    roomModalForm.find('.form-button').removeClass('disabled');

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

    $('#room-modal-form').on('submit', function () {
        $(this).find('.form-button').text('Пожалуйста подождите');
        $(this).find('.form-button').addClass('disabled');

        let roomDeleteButton = $(this).find('.room-delete');
        if (roomDeleteButton.length) {
            roomDeleteButton.remove();
        }
    });

    function updateSelects (equipment_storaged, equipment_operating, room_id) {
        const roomModalForm = $('#room-modal-form');
        const selectStoraged = roomModalForm.find('#equipment_storaged');
        // const selectOperating = roomModalForm.find('#equipment_operating');

        selectStoraged.empty();
        // selectOperating.empty();

        var storagedOptions = '<option value="" disabled>Не выбрано</option>';
        // var operatingOptions = '<option value="" disabled>Не выбрано</option>';



        if(room_id === 0) {
            $.each(equipment_storaged, function (key, value) {
                storagedOptions += `
                <option value="${value['id']}">
                    ${value['name']}
                </option>
            `;
            })
        }else {
            $.each(equipment_storaged, function (key, value) {
                storagedOptions += `
                <option value="${value['id']}" ${Number(value['id_storage_room']) === Number(room_id) ? 'selected="selected"' : ''}>
                    ${value['name']}
                </option>
            `;
            })
        }

        // $.each(equipment_operating, function (key, value) {
        //     operatingOptions += `
        //         <option value="${value['id']}" ${value['id_operating_room'] !== null ? 'selected="selected"' : ''}>
        //             ${value['name']}
        //         </option>
        //     `;
        // })

        selectStoraged.append(storagedOptions);
        // selectOperating.append(operatingOptions);

        selectStoraged.select2({ theme: "bootstrap-5" });
        // selectOperating.select2({ theme: "bootstrap-5" });
    }


});
