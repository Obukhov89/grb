var CustomStuff = BX.namespace('CustomStuff');

CustomStuff.selectOfSelect = function() {
    BX.addCustomEvent('onAjaxSuccessFinish', CustomStuff.selectorChange);
};

CustomStuff.selectorChange = function (change_opt = false) {

    var idOborud = BX.findChildByClassName(document, 'id-oborud',true);
    if(!idOborud){
        idOborud = parseInt(document.getElementsByName('UF_CRM_TEST')[0].value);
    }else{
        idOborud = parseInt(idOborud.innerHTML);
    }

    select_2 = document.getElementsByName('UF_CRM_OBORUD')[0];

    document.getElementsByName('UF_CRM_GOST')[0].value = idOborud;
    document.getElementsByName('UF_CRM_NORM1')[0].value = idOborud;
    document.getElementsByName('UF_CRM_NORM2')[0].value = idOborud;
    document.getElementsByName('UF_CRM_ED')[0].value = idOborud;

    if(select_2) {
        if(change_opt)
            select_2.options[0].selected=true;

        select_2 = select_2.querySelectorAll('option');
        select_2.forEach(function (item, i, arr) {
            if (i != 0 && arr[i].dataset.ids != idOborud) {
                arr[i].style.display = 'none';
            } else {
                arr[i].style.display = 'block';
                if(arr[i].dataset.ids === idOborud)
                    arr[i].setAttribute('selected','selected');
                else
                    arr[i].removeAttribute('selected');
            }
        });
    }

};

