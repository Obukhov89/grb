/**
 * Карточка внесения инфы о отделе
 */
$(function ($) {
    const body = $('body')

    const headList = $('.head_id');
    headList.select2({
        dropdownParent: "#lab-modal-form",
        language: {
            noResults: () => "Ничего не найдено"
        },
    });
    headList.on('select2:open', function() {
        $('.select2-search--dropdown .select2-search__field').attr("placeholder", "Начните вводить сотрудника");
    });

    /** modal */
    // $('.popup-with-form').magnificPopup({
    //     items: {
    //         src: '#lab-modal-form',
    //         type: 'inline'
    //     },
    //     fixedContentPos: false,
    //     fixedBgPos: true,
    //     callbacks: {
    //         open: function() {
    //             const labModalForm = $('#lab-modal-form');
    //             labModalForm.find('.form-button').text('Добавить отделение');
    //
    //             if (labModalForm.find('#deptId').val() != '')
    //             {
    //                 labModalForm.find('#deptId').val('');
    //
    //                 labModalForm.find('#name').val('');
    //                 labModalForm.find('#deptId').val('');
    //
    //                 labModalForm.find('#head_id').val('-1').trigger('change');
    //                 labModalForm.find('#parent_role').val('-1').trigger('change');
    //
    //                 labModalForm.find('.dept-delete').remove();
    //             }
    //         }
    //     }
    // })

    body.on('click', '.popup-with-form', function () {
        $.magnificPopup.open({
            items: {
                src: '#lab-modal-form',
                type: 'inline'
            },
            fixedContentPos: false,
            fixedBgPos: true,
            callbacks: {
                open: function() {
                    const labModalForm = $('#lab-modal-form');
                    labModalForm.find('.form-button').text('Добавить отделение');

                    if (labModalForm.find('#deptId').val() != '')
                    {
                        labModalForm.find('#deptId').val('');

                        labModalForm.find('#name').val('');
                        labModalForm.find('#deptId').val('');

                        labModalForm.find('#head_id').val('-1').trigger('change');
                        labModalForm.find('#parent_role').val('-1').trigger('change');

                        labModalForm.find('.dept-delete').remove();
                    }
                }
            }
        })
    })

    /**
     * обновить данные помещения
     */
    body.on('click', '.lab-edit', function () {
        let deptId = $(this).data('deptId');

        let editButton = $(this);
        let oldText = editButton.text();
        editButton.find('i').addClass('fa-spinner');
        editButton.find('i').removeClass('fa-pencil');
        editButton.find('i').removeClass('fa-xmark');

        $.ajax({
            method: 'POST',
            url: '/ulab/import/getLabAjax',
            data: {
                id: deptId
            },
            dataType: "json",
            success: function (data) {
                editButton.find('i').removeClass('fa-spinner');
                editButton.find('i').addClass('fa-pencil');

                if (data['ID']) {
                    const labModalForm = $('#lab-modal-form');

                    labModalForm.find('.form-button').text('Сохранить отделение');

                    labModalForm.find('#name').val(data['NAME']);
                    labModalForm.find('#deptId').val(data['ID']);

                    if (data['ROLE_USER_ID'])
                        labModalForm.find('#parent_role').val(data['ROLE_USER_ID']);

                    if (data['HEAD_ID'])
                        labModalForm.find('#head_id').val(data['HEAD_ID']).trigger('change');
                    else
                        labModalForm.find('#head_id').val('-1').trigger('change');

                    labModalForm.find('.dept-delete').remove();
                    if (data['ID'] != 53) {
                        labModalForm.append(`<button type="button" class="btn btn-danger ms-2 dept-delete" 
                            data-dept-id="${deptId}">Удалить</button>`);
                    }

                    $.magnificPopup.open({
                        items: {
                            src: labModalForm,
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
    body.on('click', '.dept-delete', function () {
        let labId = $(this).data('deptId');

        if (!confirm('Вы действительно хотите удалить отделение? После удаления, данные нельзя будет востановить')) {
            return false;
        }

        const labModalForm = $('#lab-modal-form');
        let oldText = labModalForm.find('.dept-delete').text();
        labModalForm.find('.dept-delete').text('Подождите, происходит удаление');
        labModalForm.find('.form-button').addClass('disabled');

        $.ajax({
            method: 'POST',
            url: '/ulab/import/deleteLabAjax',
            data: {
                id: labId
            },
            dataType: "json",
            success: function (data) {
                $.magnificPopup.close()

                if (data['success']) {
                    labModalForm.find('.dept-delete').text(oldText);
                    labModalForm.find('.form-button').removeClass('disabled');
                    location.reload();
                } else {
                    $('.alert-title').text('Внимание!')
                    $('.alert-content').text(data['error']['message'])

                    labModalForm.find('.form-button').text('Ошибка');
                    labModalForm.find('.form-button').removeClass('disabled');

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

    $('#lab-modal-form').on('submit', function () {
        $(this).find('.form-button').text('Пожалуйста подождите');
        $(this).find('.form-button').addClass('disabled');

        let deptDeleteButton = $(this).find('.dept-delete');
        if (deptDeleteButton.length) {
            deptDeleteButton.remove();
        }
    });

    $('#head_id').change(function() {
        let selectedValue = $(this).val();
        if (selectedValue && selectedValue != '-1') {
            $('#parent_role').removeAttr('disabled');
        } else {
            $('#parent_role').attr('disabled', 'disabled');
        }
    });
});