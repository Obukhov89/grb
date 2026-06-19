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

function sigFile() {
    let url = $('#url_xml').val()
    fileContent = $('#res').val()
    protocol_id = $('#protocol_id').val()
    controller_url = '/ulab/fsa/saveSigAjax/'

    Common_SignCadesBES_File('CertListBox', url)
}
