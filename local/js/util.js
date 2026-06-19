(function animatePlaceholder() {
    var input = document.querySelectorAll('.input-box input[type="number"]');
    [].forEach.call(input, function(elem) {
      var span = document.createElement('span'),
        value;
      elem.onfocus = function() {
        value = this.getAttribute('data-value');
        span.innerHTML = value;
        this.parentNode.insertBefore(span, this);
        if (this.value == "От" || this.value == "до") this.value = '';
        span.classList.add('placeholder');
        span.classList.add('placeholder-show');
      }
      elem.onblur = function() {
        span.classList.remove('placeholder-show');
        if(this.value == "") this.value = this.getAttribute('data-value'); 
      }
    });
  }());

function fileUpload(idCurrentProtocol){   
    console.log(idCurrentProtocol); 
	if (window.FormData === undefined) {
		alert('В вашем браузере FormData не поддерживается')
	} else {
		var formData = new FormData();
		formData.append('file', $("#js-file")[0].files[0]);
 
		$.ajax({
			type: "POST",
			url: '/local/upload/upload.php?protocol_id=' + idCurrentProtocol,
			cache: false,
			contentType: false,
			processData: false,
			data: formData,
			dataType : 'json',
			success: function(data){
				if (data.error == '') {
                    var protocolID = data.protocol_number;
                    var protocolName = data.protocol_name;
                    $('#link_protocol').attr('href', '/pdf/' + protocolID + '/' + protocolName);
                    $('#link_protocol').text(protocolName);
					// $("#js-file").hide();
					// $('#result').html(msg.success);
				} else {
					// $('#result').html(msg.error);
				}
			}
		});
	}
}  

var attrSign;
function display_hide (blockId){ 
    if ($(blockId).css('display') == 'none'){ 
        $(blockId).animate({height: 'show'}, 900); 
    } 
    else{     
        $(blockId).animate({height: 'hide'}, 900); 
    }

    $('.content_wrap').attr('data-sign', 'true');
    attrSign = $('.content_wrap').attr('data-sign');
    sessionStorage.setItem("autosave", attrSign.value);
}

if (sessionStorage.getItem("autosave")) {
    $('.content_wrap').css('display', 'block');
    field.value = sessionStorage.getItem("autosave");
}