document.getElementById("SignatureTxtBox").innerHTML = "";
let canPromise = !!window.Promise;
if (isEdge()) {
    ShowEdgeNotSupported();
} else {
    let $infoBlock = $('#info')
    let $textBlock = $('#PlugInEnabledTxt')
    if (canPromise) {
        cadesplugin.then(function () {
                Common_CheckForPlugIn();
            },
            function (error) {
                $infoBlock.show()
                $textBlock.text(error)
            }
        );
    } else {
        window.addEventListener("message", function (event) {
                if (event.data == "cadesplugin_loaded") {
                    CheckForPlugIn_NPAPI();
                } else if (event.data == "cadesplugin_load_error") {
                    $infoBlock.show()
                    $textBlock.text('Плагин не загружен')
                }
            },
            false);
        window.postMessage("cadesplugin_echo_request", "*");
    }
}

$(function () {

    $('body').on('click', '#btn_outside_lis_sig_file', function () {
        // console.log('aaaa')
        // let tmp = '{"x": 160,"y": 597,"width": 240,"height": 50}'
        // let imgParams = JSON.parse(tmp);
        //
        // console.log(imgParams)
        //
        // addImgToPdf('/testapi/qqq.pdf', '/sign_61.png', imgParams)
    })

    $('body').on('click', '#btn_sig_file', function () {
        let $button = $(this)

        let isOutsideLis = $('#outside_lis').val()
        let newPath = $('#new_pdf_path').val()
        let outsideLisPathPdf = $('#outside_lis_path_pdf').val()
        let userId = $('#user_id').val()
        let dateToday = $('#today').val()

        fileContent = $('#res').val()
        file_name = $('#file_name').val()
        protocol_id = $('#protocol_id').val()
        controller_url = '/ulab/protocol/saveSigAjax/'

        let btnHtml = $button.html()
        $button.html(`<i class="fa-solid fa-arrows-rotate spinner-animation"></i>`)
        $button.addClass('disabled')

        if ( isOutsideLis == 0 ) {

            $.ajax({
                url: '/ulab/protocol/sigAjax/',
                method: "POST",
                dataType: 'json',
                data: {protocol_id: protocol_id},
                success: function (data) {
                    if (data.success) {
                        fileContent = data.file_base64
                        $('#res').val(data.file_base64)

                        file_name = data.pdf_file_name
                        $('#file_name').val(data.pdf_file_name)
                        $('#place_file_name').text(data.pdf_file_name)

                        $('#url_file').val(data.url_file)

                        Common_SignCadesBES_File('CertListBox', data.url_file)
                    } else {
                        showErrorMessage('Ошибка создания файла для подписи: ' + data.error)
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
                    showErrorMessage('Ошибка: не удалось подготовить файл для подписи: ' + msg)
                },
                complete: function () {
                    $button.html(btnHtml)
                    $button.removeClass('disabled')
                }
            })
        } else {
            $.ajax({
                url: '/ulab/protocol/getProtocolTypeAjax/',
                method: "POST",
                dataType: 'json',
                data: {protocol_id: protocol_id},
                success: function (data) {
                    let imgParams = ''

                    if (data.type_id == 33) {
                        imgParams = JSON.parse('{"x": 150,"y": 630,"width": 240,"height": 50}');
                    }else {
                        imgParams = JSON.parse('{"x": 150,"y": 600,"width": 240,"height": 50}');
                    }

                    addImgToPdf(outsideLisPathPdf, `/sign_${userId}.png`, imgParams, newPath).then(
                        function (result) {

                            fileContent = result
                            $('#res').val(result)

                            Common_SignCadesBES_File('CertListBox', '')

                            $button.html(btnHtml)
                            $button.removeClass('disabled')
                        }
                    )
                },
            })


        }
    })
})


function sigFile() {
    let url = $('#url_file').val()
    fileContent = $('#res').val()
    file_name = $('#file_name').val()
    protocol_id = $('#protocol_id').val()
    controller_url = '/ulab/protocol/saveSigAjax/'

    $.ajax({
        url: '/ulab/protocol/sigAjax/',
        method: "POST",
        dataType: 'json',
        data: {protocol_id: protocol_id},
        success: function (data) {
            console.log(data)
            if ( data.success ) {
                fileContent = data.file_base64
                $('#res').val(data.file_base64)

                file_name = data.pdf_file_name
                $('#file_name').val(data.pdf_file_name)

                Common_SignCadesBES_File('CertListBox', url)
            } else {
                showErrorMessage('Ошибка создания файла для подписи: ' + data.error)
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
            showErrorMessage('Ошибка: не удалось подготовить файл для подписи: ' + msg)
        },
        complete: function () {

        }
    })
}
