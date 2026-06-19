function getUrlSearchParams() {

    let queryString = window.location.search;

    let urlParams = new URLSearchParams(queryString);

    return urlParams;
}

var openFile = function () {

    // $('#res').val("Подождите, идет загрузка документа!");
    // var strGET = window.location.search.replace( '?', '');
    //
    // var getStrId = strGET.split('ID=')[1];
    // var getId = getStrId.split('&DATE')[0];
    //
    // let checkIdInArray = '<?= $checkIdInArray ?>';
    //
    // if (checkIdInArray) {
    //     var getStrProtocolId = strGET.split('NUMP=')[1];
    //     var getProtocolId = getStrProtocolId.split('&ID_P')[0];
    // } else {
    //     var getProtocolId = strGET.split('NUMP=')[1];
    // }
    //
    // var getStrDate = strGET.split('DATE=')[1];
    // var getDate = getStrDate.split('&NUMP')[0];
    // var getYear = $('.dateArchive').val();//getDate.split('.')[2];
    //
    //
    // var urlParams = getUrlSearchParams();
    //
    // var idP = urlParams.get('ID_P');



    // if (checkIdInArray) {
    //     var urlToPdf = "/protocol_generator/archive/" + getId + getYear + "/" + idP + "/Протокол №" + getProtocolId + " от " + getDate + ".pdf";
    //
    // } else {
    //     var urlToPdf = "/protocol_generator/archive/"+getId+getYear+"/Протокол №"+getProtocolId+" от "+getDate+".pdf";
    // }

    var urlToPdf = "/ulab/upload/fsa/protocols/protocol_2528_22_0109102346.xml"
    // console.log(getProtocolId+" - "+getId+" - "+getDate+" - "+getYear);
    function _arrayBufferToBase64( buffer ) {
        var binary = '';
        var bytes = new Uint8Array( buffer );
        var len = bytes.byteLength;
        for (var i = 0; i < len; i++) {
            binary += String.fromCharCode( bytes[ i ] );
        }
        return window.btoa( binary );
    }

    var xhrOverride = new XMLHttpRequest();
    xhrOverride.responseType = 'arraybuffer';

    $.ajax({
        url: urlToPdf,
        method: 'GET',
        xhr: function() {
            return xhrOverride;
        }
    }).then(function (responseData) {


        encodedString = _arrayBufferToBase64(responseData);

        $('#res').val(encodedString);

    });

};

var getContent = function()
{
    if($('#res').val()!='')
        return $('#res').val();
    else
        alert('Дождитесь, пожалуйста, окончания загрузки кода протокола!');

}

var txtDataToSign = "Hello World";
document.getElementById("SignatureTxtBox").innerHTML = "";
var canPromise = !!window.Promise;
if(isEdge()) {
    ShowEdgeNotSupported();
} else {
    if(canPromise) {
        cadesplugin.then(function () {
                Common_CheckForPlugIn();
            },
            function(error) {
                document.getElementById('PluginEnabledImg').setAttribute("src", "Img/red_dot.png");
                document.getElementById('PlugInEnabledTxt').innerHTML = error;
            }
        );
    } else {
        window.addEventListener("message", function (event){
                if (event.data == "cadesplugin_loaded") {
                    CheckForPlugIn_NPAPI();
                } else if(event.data == "cadesplugin_load_error") {
                    document.getElementById('PluginEnabledImg').setAttribute("src", "Img/red_dot.png");
                    document.getElementById('PlugInEnabledTxt').innerHTML = "Плагин не загружен";
                }
            },
            false);
        window.postMessage("cadesplugin_echo_request", "*");
    }
}

$(document).ready(function() {
    openFile();
});