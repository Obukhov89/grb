$(function () {
    let body = $('body');

    body.on('click', '.add-user', function () {
        $.magnificPopup.open({
            items: {
                src: '#user-modal-form',
                type: 'inline'
            },
            fixedContentPos: false,
            fixedBgPos: true,
            closeOnBgClick: false,
            callbacks: {
                open: function () {
                    const userModalForm = $('#user-modal-form');
                    userModalForm.find('.form-button').text('Добавить нового пользователя');

                    userModalForm.find('.hidePassword').show();
                    userModalForm.find('.hidePasswordConfirm').show();

                    let passwordField = $('.hidePassword').parent().find('input');
                    passwordField.attr('type', 'password');
                    $('.hidePassword').removeClass('fa-eye-slash').addClass('fa-eye');

                    let passwordFieldConfirm = $('.hidePasswordConfirm').parent().find('input');
                    passwordFieldConfirm.attr('type', 'password');
                    $('.hidePasswordConfirm').removeClass('fa-eye-slash').addClass('fa-eye');

                    if (userModalForm.find('#userId').val() != '') {
                        userModalForm.find('#userId').val('');
                        userModalForm.find('#userId').val('');
                        userModalForm.find('#name').val('');
                        userModalForm.find('#lastName').val('');
                        userModalForm.find('#secondName').val('');
                        userModalForm.find('#email').val('');
                        userModalForm.find('#login').val('');
                        userModalForm.find('#workPosition').val('');
                        userModalForm.find('#newPassword').val('');
                        userModalForm.find('#newPasswordConfirm').val('');
                        userModalForm.find('#departmentId').val('');

                        userModalForm.find('.user-delete').remove();
                    }

                }
            }
        })
    })


    /**
     * обновить данные пользователя
     */
    body.on('click', '.user-edit', function (e) {
        e.preventDefault();
        let editButton = $(this);
        let userId = editButton.data('userId');
        let oldText = editButton.text();
        editButton.text('Подождите');

        const userModalForm = $('#user-modal-form');
        userModalForm.find('.form-button').text('Сохранить пользователя');

        $.ajax({
            method: 'POST',
            url: '/ulab/import/getUserAjax',
            data: {
                user_id: userId
            },
            dataType: "json",
            success: function (data) {
                editButton.text(oldText);

                if (data['ID']) {
                    const userModalForm = $('#user-modal-form');

                    // глазик
                    userModalForm.find('.hidePassword').show();
                    userModalForm.find('.hidePasswordConfirm').show();

                    let passwordField = $('.hidePassword').parent().find('input');
                    passwordField.attr('type', 'password');
                    $('.hidePassword').removeClass('fa-eye-slash').addClass('fa-eye');

                    let passwordFieldConfirm = $('.hidePasswordConfirm').parent().find('input');
                    passwordFieldConfirm.attr('type', 'password');
                    $('.hidePasswordConfirm').removeClass('fa-eye-slash').addClass('fa-eye');
                    // глазик

                    userModalForm.find('#userId').val(userId);
                    userModalForm.find('#name').val(data['NAME']);
                    userModalForm.find('#lastName').val(data['LAST_NAME']);
                    userModalForm.find('#secondName').val(data['SECOND_NAME']);
                    userModalForm.find('#email').val(data['EMAIL']);
                    userModalForm.find('#login').val(data['LOGIN']);
                    userModalForm.find('#workPosition').val(data['WORK_POSITION']);

                    userModalForm.find('#newPassword').val('');
                    userModalForm.find('#newPasswordConfirm').val('');

                    if (userModalForm.find('#departmentId').find('option[value="' + data['DEPARTMENT_ID'] + '"]').length > 0) {
                        userModalForm.find('#departmentId').val(data['DEPARTMENT_ID']);
                    } else {
                        userModalForm.find('#departmentId').val('');
                    }

                    userModalForm.find('.user-delete').remove();
                    if (data['ID'] != 1) {
                        userModalForm.append(`<button type="button" class="btn btn-danger ms-2 user-delete" 
                            data-user-id="${userId}">Удалить</button>`);
                    }

                    $.magnificPopup.open({
                        items: {
                            src: userModalForm,
                            type: 'inline',
                            fixedContentPos: false
                        },
                        closeOnBgClick: false
                    });
                }
            },
            error: function (jqXHR, exception) {
                editButton.text('Ошибка');

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
            }
        });
    });

    /**
     * удалить данные пользователя
     */
    body.on('click', '.user-delete', function () {
        let userId = $(this).data('userId');

        if (!confirm('Вы действительно хотите удалить пользователя? После удаления, данные нельзя будет восстановить')) {
            return false;
        }
        const userModalForm = $('#user-modal-form');
        let oldText = userModalForm.find('.user-delete').text();
        userModalForm.find('.user-delete').text('Подождите, происходит удаление');
        userModalForm.find('.form-button').addClass('disabled');

        $.ajax({
            method: 'POST',
            url: '/ulab/import/deleteUserAjax',
            data: {
                user_id: userId
            },
            dataType: "json",
            success: function (data) {
                $.magnificPopup.close()

                if (data['success']) {
                    userModalForm.find('.user-delete').text(oldText);
                    userModalForm.find('.form-button').removeClass('disabled');
                    location.reload();
                } else {
                    $('.alert-title').text('Внимание!')
                    $('.alert-content').text(data['error']['message'])

                    userModalForm.find('.form-button').text('Ошибка');
                    userModalForm.find('.form-button').removeClass('disabled');

                    $.magnificPopup.open({
                        items: {
                            src: $('#alert_modal'),
                            type: 'inline',
                            fixedContentPos: false
                        },
                        closeOnBgClick: false
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
            }
        });
    });

    $('#user-modal-form').on('submit', function () {
        $(this).find('.form-button').text('Пожалуйста подождите');
        $(this).find('.form-button').addClass('disabled');

        let userDeleteButton = $(this).find('.user-delete');
        if (userDeleteButton.length) {
            userDeleteButton.remove();
        }
    });

    $('.hidePassword').click(function () {
        let passwordField = $('.hidePassword').parent().find('input');
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

    // Добавляем обработчик для поля подтверждения пароля
    $('.hidePasswordConfirm').click(function () {
        let passwordField = $('.hidePasswordConfirm').parent().find('input');
        if (passwordField.attr('type') === 'password') {
            passwordField.attr('type', 'text');
            $(this).removeClass('fa-eye').addClass('fa-eye-slash');
        } else {
            passwordField.attr('type', 'password');
            $(this).removeClass('fa-eye-slash').addClass('fa-eye');
        }
    });

});