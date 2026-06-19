$(function () {
    const $body = $('body');

    $('.select2').select2({
        theme: 'bootstrap-5',
        width: 'resolve',
    });

    /** modal */
    $('.popup-with-component').magnificPopup({
        items: {
            src: '#component-modal-form',
            type: 'inline'
        },
        fixedContentPos: false
    });

    $('.delete_file').click(function () {
        $(this).parents('.input-group').find('input').val('');
    });

    /**
     * Сделать образец контроля не актуальным
     */
    $body.on('click', '.non-actual-sample', function () {
        if (!confirm('Отметить образец контроля как неактуальный?')) {
            return false;
        }
    });

    $body.on('click', '.popup-with-component', function () {
        $('#component-modal-form input:not("#stSampleId")').val('');
        $('#component-modal-form select').val(0).trigger('change');
        $('#component-modal-form .remove-component').remove();
    });

    /**
     * Обновить данные компонента
     */
    $body.on('click', '.update-component', function (e) {
        e.preventDefault();
        
        let componentId = $(this).data('component');

        $.ajax({
            method: 'POST',
            url: '/ulab/oborud/getComponentAjax',
            data: {
                id: componentId
            },
            dataType: "json",
            success: function (data) {
                if (data['id']) {
                    const componentModalForm = $('#component-modal-form');

                    componentModalForm.find('#componentId').val(componentId);
                    componentModalForm.find('#name').val(data['name']);
                    componentModalForm.find('#certifiedValue').val(data['certified_value']);
                    componentModalForm.find('#certifiedUnitId').val(data['certified_unit_id']).trigger('change');
                    componentModalForm.find('#uncertainty').val(data['uncertainty']);
                    componentModalForm.find('#uncertaintyUnitId').val(data['uncertainty_unit_id']).trigger('change');
                    componentModalForm.find('#errorCharacteristic').val(data['error_characteristic']);
                    componentModalForm.find('#characteristicUnitId').val(data['characteristic_unit_id']).trigger('change');

                    componentModalForm.find('.remove-component').remove();
                    if (data['is_may_change']) {
                        componentModalForm.append(`<button type="button" class="btn btn-danger ms-2 remove-component" 
                        data-component="${componentId}">Удалить</button>`);
                    }

                    $.magnificPopup.open({
                        items: {
                            src: componentModalForm,
                            type: 'inline',
                            fixedContentPos: false
                        },
                        closeOnBgClick: false
                    });
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
                console.log(msg);
            }
        });

    });

    /**
     * Удалить данные компонента
     */
    $body.on('click', '.remove-component', function () {
        let componentId = $(this).data('component'),
            name = $('#name').val(),
            stSampleId = $('#stSampleId').val();

        if (!confirm('Вы действительно хотите удалить данные метрологической характеристики? После удаления, данные нельзя будет востановить')) {
            return false;
        }

        $.ajax({
            method: 'POST',
            url: '/ulab/oborud/removeComponentAjax',
            data: {
                id: componentId,
                name: name,
                st_sample_id: stSampleId,
            },
            dataType: "json",
            success: function (data) {
                $.magnificPopup.close()

                if (data['success']) {
                    location.reload();
                } else {
                    $('.alert-title').text('Внимание!')
                    $('.alert-content').text(data['error']['message'])
                    $('#alert_modal').removeClass('col-md-8')

                    $.magnificPopup.open({
                        items: {
                            src: $('#alert_modal'),
                            type: 'inline',
                            fixedContentPos: false
                        },
                        closeOnBgClick: false
                    })

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

    $('#select-lab').change(function () {
        let $optionList = $('#select-lab').find('option:selected')
        let idList = []

        $.each($optionList, function (i, item) {
            idList.push($(item).val());
        });

        if ( idList.length > 0 ) {
            $.ajax({
                url: "/ulab/oborud/getRoomByLabIdAjax/",
                data: {lab: idList},
                dataType: "json",
                method: "POST",
                success: function (data) {
                    let html = `<option value="0">Выберите помещение</option>`;

                    $.each(data, function (i, item) {
                        if ( item.id < 100 ) {
                            html += `<option value="" disabled>${item.name}</option>`
                        } else {
                            html += `<option value="${item.id - 100}">${item.name}</option>`
                        }
                    });

                    $('#select-room ~ .select2-container').find('#select2-select-room-container').html('');
                    $('#select-room').html(html);
                }
            })
        } else {
            $('#select-room').html('<option value="" disabled>Сначала выберите лаборатории</option>');
        }
    });

});