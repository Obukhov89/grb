$(function ($) {
    if ($('#notify_lose').length) {
        $.magnificPopup.open({
            items: {
                src: '#notify_lose'
            },
            type: 'inline'
        })
    }

    $('[data-bs-toggle="popover"]').popover()
    
    $('.reloadS').on('click', function () {
        let url = $(this).data('href')
        window.open (url, '_blank')
        setTimeout( function() {location.reload()}, 1500);
    })

    $('.reload').on('click', function () {
        setTimeout( function() {location.reload()}, 1500);
    })

    $('.open-lose-comment').click(function () {
        let block = $('.lose-com-block');
        let array = block[0].classList

        console.log(block)
        console.log(array)

        if(array.contains('d-none')) {
            block.removeClass('d-none');
        } else {
            block.addClass('d-none');
        }
    })

    // Логика, связанная с отправкой Акта выполненных работ
    let emailCheckOpener = null;

    $('.popup-mail').on('click', function (e) {
        // Очищаем поле при каждом открытии модалки
        $('#additional-message-text').val('');

        emailCheckOpener = $(this);

        $('#TYPE').val($(this).data('type'))
        $('#TITLE').val($(this).data('title'))
        $('#ATTACH').val($(this).data('attach'))
        $('#SIG').val($(this).data('sig'))
        $('#PDF').val($(this).data('pdf'))
        $('#YEAR').val($(this).data('year'))
        $('#ID_P').val($(this).data('id_p'))
        $('#ID').val($(this).data('id'))
        $('#OL').val($(this).data('ol'))
    })

    $('.popup-group-mail').on('click' , function(e) {
        $('#GROUP-ID').val($(this).data('order-id'));
        $('#GROUP-ID2').val($(this).data('deal-id'));
        $('#GROUP-TZ_ID').val($(this).data('tz-id'));
        $('#GROUP-EMAIL').val($(this).data('email'));
        $('#GROUP-NAME').val($(this).data('name'));
        $('#GROUP-ATTACH1').val($(this).data('attach1'));
        $('#GROUP-ATTACH2').val($(this).data('attach2'));
        $('#GROUP-ATTACH3').val($(this).data('attach3'));
        $('#GROUP-TITLE').val($(this).data('title'));

        $('#group-email-check input[name="CHECK[]"]').remove();
        $('.check-mail:checked').each(function () {
            let what = $(this).data('text');
            $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'CHECK[]')
                .val(what)
                .appendTo('#group-email-check');
        });
    });

    $('.popup-group-protocol-mail').on('click' , function(e) {
        $('#PROTOCOL-GROUP-ID').val($(this).data('order-id'));
        $('#PROTOCOL-GROUP-ID2').val($(this).data('deal-id'));
        $('#PROTOCOL-GROUP-TZ_ID').val($(this).data('tz-id'));
        $('#PROTOCOL-GROUP-EMAIL').val($(this).data('email'));
        $('#PROTOCOL-GROUP-NAME').val($(this).data('name'));
        $('#PROTOCOL-GROUP-TITLE').val($(this).data('title'));

        $('#group-email-check input[name="CHECK[]"]').remove();
        $('.check-mail:checked').each(function () {
            let what = $(this).data('text');
            $('<input>')
                .attr('type', 'hidden')
                .attr('name', 'CHECK[]')
                .val(what)
                .appendTo('#group-email-check');
        });
    });

    $('.check-need-sended').on('change', function () {
        let val = $(this).is(':checked')
        let numberValue = 0

        let dealId = $('input[name=deal_id]').val()

        if(val) {
            numberValue = 1
        }else {
            numberValue = 0
        }

        $.ajax({
            method: 'POST',
            url: '/ulab/request/updateNeedleSend',
            data: {
                value: numberValue,
                id: dealId
            },
            success: function (textContent) {
                location.reload()
            },
            error: function (jqXHR, exception) {
                location.reload()
            }
        })
    })

    $('#group-email-check').on('submit', function(e) {
        // Собираем значения чекбоксов
        let checkValues = [];
        $('.check-mail:checked').each(function() {
            checkValues.push($(this).data('text'));
        });

        // Собираем остальные данные формы
        let formData = $(this).serializeArray();

        // Добавляем CHECK[] в formData
        checkValues.forEach(function(val) {
            formData.push({ name: 'CHECK[]', value: val });
        });
    });

    // При сабмите формы проверяем инициатора
    $('#email-check').on('submit', function (e) {
        console.log(1)
        if (emailCheckOpener) {
            console.log(2)
            const statusMap = {
                1: 8,
                2: 3,
                3: 7,
                8: 21
            };

            const type = emailCheckOpener.data('type');
            const statusId = statusMap[type];
            if (statusId !== undefined) {
                // updateStatusTz(statusId);
            }
        }
        // После отправки сбрасываем инициатора
        emailCheckOpener = null;
    });

    $('.popup-mail').magnificPopup({
        type: 'inline',
        closeBtnInside:true,
        fixedContentPos: false,
        closeOnBgClick: false,      // <--- запрещает закрытие по клику вне окна
        closeOnContentClick: false, // <--- запрещает закрытие по клику по контенту
        callbacks: {
            close: function () {
                emailCheckOpener = null;
            }
        }
    })

    $('.popup-group-mail').magnificPopup({
        type: 'inline',
        closeBtnInside:true,
        fixedContentPos: false,
        closeOnBgClick: false,      // <--- запрещает закрытие по клику вне окна
        closeOnContentClick: false, // <--- запрещает закрытие по клику по контенту
    })

    $('.popup-group-protocol-mail').magnificPopup({
        type: 'inline',
        closeBtnInside:true,
        fixedContentPos: false,
        closeOnBgClick: false,      // <--- запрещает закрытие по клику вне окна
        closeOnContentClick: false, // <--- запрещает закрытие по клику по контенту
    })

    $('.act-manual-edit').click(function () {
        $('.act-manual-block').toggleClass('visually-hidden')
    })

    // $('#act-probe-modal-form').submit(function () {
    //     let $inputActNumber     = $(this).find('input[name=actNumber]'),
    //         $inputActDate       = $(this).find('input[name=actDate]'),
    //         $inputSamplePlace   = $(this).find('input[name=samplePlace]'),
    //         $inputSampleDate    = $(this).find('input[name=sampleDate]'),
    //         $inputTzId          = $(this).find('input[name=tz_id]')
    //
    //     let tzId = $inputTzId.val()
    //
    //     $.ajax({
    //         method: "POST",
    //         url: "add_act_num.php",
    //         data: {
    //             act: $inputActNumber.val(),
    //             id_z: tzId,
    //             date_act: $inputActDate.val(),
    //             place_probe: $inputSamplePlace.val(),
    //             date_probe: $inputSampleDate.val()
    //         },
    //         success: function (data) {
    //             if (data) {
    //                 $inputActNumber.prop("disabled", true);
    //                 $inputActDate.prop("disabled", true);
    //                 $inputSamplePlace.prop("disabled", true);
    //                 $inputSampleDate.prop("disabled", true);
    //                 window.open(`/probe.php?ID=${tzId}`, '_blank');
    //             }
    //         }
    //     })
    //
    //     return false
    // })

    // $('#act-work-modal-form').submit(function () {
    //     let actNumber     = $(this).find('input[name=actNumber]').val(),
    //         actDate       = $(this).find('input[name=actDate]').val(),
    //         lead          = $(this).find('select[name=lead] option:selected').val(),
    //         dealId        = $(this).find('input[name=deal_id]').val(),
    //         tzId          = $(this).find('input[name=tz_id]').val(),
    //         accountEmail  = $(this).find('input[name=Email]').val()
    //
    //     window.open(`/protocol_generator/akt_vr.php?ID=${dealId}&TZ_ID=${tzId}&NUM=${actNumber}&DATE=${actDate}&LEAD=${lead}&ACCMAIL=${accountEmail}`, '_blank');
    // })

    $('#act-work-modal-form').submit(function () {
        $.magnificPopup.close();
        setTimeout(function() {
            window.location.reload(); // Перезагрузка через 100 мс (чтобы попап успел закрыться)
        }, 4000);
    })

    $('.akt-finish').click(function() {
        let tzId = $('#finish-modal-form').find('input[name=tz_id]').val()
        let stage = $(this).data('stage')
        let loseComment = $(this).data('text')

        let dealId = $('input[name=deal_id]').val()

        if(stage === 11) {
            let comment = $('.lose-comment').val();

            $.ajax({
                method: 'POST',
                url: '/ulab/request/updateLoseCommentAjax',
                data: {
                    deal_id: dealId,
                    comment: comment,
                },
                success: function(textContent) {
                    location.reload();
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
                    console.log(msg)
                }
            })
        }else {
            $.ajax({
                method: 'POST',
                url: '/update_stage_id.php',
                data: {
                    satge_id: stage,
                    tz_id: tzId,
                },
                success: function (textContent) {
                    if (textContent) {
                        if (stage === 4) {
                            updateStatusTz(6);
                        } else if (stage === 'WON') {
                            updateStatusTz(6);
                        } else if(stage === 'WON-NO-ACT') {
                            updateStatusTz(6);
                        }
                        else {
                            $.ajax({
                                method: 'POST',
                                url: '/ulab/request/updateLoseCommentAjax',
                                data: {
                                    deal_id: dealId,
                                    comment: loseComment,
                                },
                                success: function(textContent) {
                                    location.reload();
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
                                    console.log(msg)
                                }
                            })
                        }

                        location.reload()
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
                    console.log(msg)
                }
            })
        }
    })

    let $checkMail = $('.check-mail')
    let $btnSend = $('.btnOverall')

    $checkMail.click(function () {
        if ( $('.check-mail:checked').length === 0 ) {
            $btnSend.addClass('disabled')
        } else {
            $btnSend.removeClass('disabled')
        }
    })

    $('.btn-show-file').on('click', function () {
        let filePath = $(this).data('file')

        openDocumentModal(filePath)
    })

    function openDocumentModal(filePath) {
        const modalTitle = $('#documentModalLabel');
        const extension = filePath.split('.').pop().toLowerCase();

        let dealId = $('input[name=deal_id]').val()

        modalTitle.textContent = filePath.split('/').pop();

        if (extension === 'jpg' || extension === 'jpeg' || extension === 'png' || extension === 'pdf') {
            let host = window.location.hostname

            window.open(`http://${host}/ulab/upload/request/${dealId}/${filePath}`)
        }else {
            $.ajax({
                method: 'POST',
                url: `/ulab/request/docShow/${dealId}`,
                data: {
                    'file': filePath
                },
                success: function (data) {
                    $('#document-viewer').attr('src', JSON.parse(data).response)

                    $.magnificPopup.open({
                        items: {
                            src: '#show_document'
                        },
                        type: 'inline'
                    })
                },
                error: function (jqXHR, exception) {
                    console.log(exception)
                    $.magnificPopup.open({
                        items: {
                            src: '#show_document'
                        },
                        type: 'inline'
                    })
                }
            })
        }
    }

    // настройки summernote (поле с сопроводительным текстом)
    $('#additional-message-text').summernote({
        lang: 'ru-RU',
        height: 200,
        toolbar: [
            // Минимальный набор инструментов, без Picture, Video, Toolbar
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
        ],
        placeholder: 'Ваш текст...',
    });

    // настройки summernote (поле с сопроводительным текстом)
    $('#group-additional-message-text').summernote({
        lang: 'ru-RU',
        height: 200,
        toolbar: [
            // Минимальный набор инструментов, без Picture, Video, Toolbar
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
        ],
        placeholder: 'Ваш текст...',
    });

    // настройки summernote (поле с сопроводительным текстом)
    $('#group-protocol-additional-message-text').summernote({
        lang: 'ru-RU',
        height: 200,
        toolbar: [
            // Минимальный набор инструментов, без Picture, Video, Toolbar
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
        ],
        placeholder: 'Ваш текст...',
    });
})

Dropzone.options.dropzoneExample = {
    paramName: "file", // The name that will be used to transfer the file
    maxFilesize: 300, // MB
    init: function() {
        this.on("complete", function (file) {
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                $('.dropzone-msg').append(
                    `<div class="alert alert-success d-flex align-items-center alert-dismissible fade show" role="alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                    </svg>
                    <div>
                        Файлы сохранены. <a href="#" onclick="document.location.reload();">Перезагрузите страницу</a>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`
                )
            }
        });
        this.on("success", function(file, responseText) {
            // console.log('success: ' + responseText);
        })

        this.on("sending", function(file, xhr, formData) {
            // console.log('sending', file, xhr, formData)
        })

        this.on("addedfile", file => {
            // console.log("A file has been added");
        })
    },
    error: function (file, message) {
        // console.log('error: ' + message)
        $('.dropzone-msg').append(
            `<div class="alert alert-danger d-flex align-items-center alert-dismissible fade show" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0 me-2" viewBox="0 0 16 16" role="img" aria-label="Warning:">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>
                <div>
                    Ошибка: ${message}
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>`
        )
    }
}

function updateStatusTz(status_id) {
    let userId = $('#inp-id-usr').val();
    let dealId = $('input[name=deal_id]').val()

    console.log(dealId)
    console.log(status_id)
    console.log(userId)

    $.ajax({
        method: 'POST',
        url: '/ulab/status/updateTzStatusAjax',
        data: {
            deal_id: dealId,
            status_id: status_id,
            user_id: userId
        },
        success: function(textContent) {
            // location.reload();
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
            console.log(msg)
        }
    })
}
