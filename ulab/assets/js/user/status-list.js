$(function ($) {
    let body = $('body');
    let $journal = $('#journal_users');

    let userForUpdateStatus = [];
    let userForUpdateReplacement = [];
    let userForUpdateNotes = [];
    let userForUpdateJob = [];

    let usersUpdateStatusButton = $('.users-update-status-trigger');

    let statusList = [];
    let usersList = [];

    let activeAjaxRequests = 0;

    $.ajax({
        type: 'GET',
        url: '/ulab/user/getStatusListAjax/',
        dataType: 'json',
        beforeSend: () => {
            activeAjaxRequests++;
            if (activeAjaxRequests > 0)
                $('#ajax-loading-message').show()

            $('#ajax-loading-message').trigger('toast-check');
        },
        complete: () => {
            activeAjaxRequests--;
            if (activeAjaxRequests <= 0)
                $('#ajax-loading-message').hide();

            $('#ajax-loading-message').trigger('toast-check');
        }
    })
    .then((statusData) => {
        statusList = statusData;

        return $.ajax({
            type: 'GET',
            url: '/ulab/user/getUsersListAjax/',
            dataType: 'json',
            beforeSend: () => {
                activeAjaxRequests++;
                if (activeAjaxRequests > 0)
                    $('#ajax-loading-message').show()

                $('#ajax-loading-message').trigger('toast-check');
            },
            complete: () => {
                activeAjaxRequests--;
                if (activeAjaxRequests <= 0)
                    $('#ajax-loading-message').hide();

                $('#ajax-loading-message').trigger('toast-check');
            }
        });
    })
    .then((userData) => {
        usersList = userData;

        let journalDataTable = $journal.DataTable({
            processing: true,
            serverSide: true,
            autoWidth: false,
            ajax: {
                type : 'POST',
                url : '/ulab/user/getUsersForStatusAjax/',
                dataSrc: function (json) {
                    return json.data
                },
                beforeSend: () => {
                    activeAjaxRequests++;
                    if (activeAjaxRequests > 0)
                        $('#ajax-loading-message').show()

                    $('#ajax-loading-message').trigger('toast-check');
                },
                complete: () => {
                    activeAjaxRequests--;
                    if (activeAjaxRequests <= 0)
                        $('#ajax-loading-message').hide();

                    $('#ajax-loading-message').trigger('toast-check');
                }
            },
            columns: [
                {
                    data: 'FULL_NAME'
                },
                {
                    data: 'USER_STATUS',
                    render: function (data, type, item) {
                        let formLayout = '';
                        formLayout += `<form id="updateStatus-${item.ID}" class="card" action="/ulab/user/updateStatus/" method="post">`;
                        formLayout += `<input type="hidden" name="user_id" value="${item.ID}">`;
                        formLayout += `<select name="status_id" class="form-control section status_id">`;

                        let selectedStatusId = null;
                        const existingUser = userForUpdateStatus.find(user => user.user_id == item.ID);
                        if (existingUser) {
                            selectedStatusId = existingUser.status_id;
                        } else {
                            selectedStatusId = item.USER_STATUS || 'default';
                        }

                        Object.entries(statusList).forEach(([key, value]) => {
                            formLayout += `<option value="${key}" ${key == selectedStatusId ? 'selected' : ''}>${value}</option>`;
                        });

                        formLayout += ` </select>`;
                        formLayout += `</form>`;

                        return formLayout;
                    }
                },
                 {
                    data: 'REPLACEMENT_USER_ID',
                    width: '350px',
                    render: function (data, type, item) {
                        let formLayout = '';
                        formLayout += `<form id="updateReplacementUser-${item.ID}" class="card" action="/ulab/user/updateReplacement/" method="post">`;
                        formLayout += `<input type="hidden" name="user_id" value="${item.ID}">`;

                        let selectedStatusId = null;
                        const existingUserStatus = userForUpdateStatus.find(user => user.user_id == item.ID);
                        if (existingUserStatus) {
                            selectedStatusId = existingUserStatus.status_id;
                        } else {
                            selectedStatusId = item.USER_STATUS || 'default';
                        }

                        formLayout += `<select name="replacement_id" class="form-control section replacement_id" ${selectedStatusId === 'default' ? 'disabled' : ''}>`;

                        let selectedReplacementId = null;
                        const existingUserReplacement = userForUpdateReplacement.find(user => user.user_id == item.ID);
                        if (existingUserReplacement) {
                            selectedReplacementId = existingUserReplacement.replacement_id;
                        } else {
                            selectedReplacementId = item.REPLACEMENT_USER_ID || null;
                        }

                        formLayout += `<option value="NULL">Нет замены</option>`;
                        usersList.forEach(user => {
                            if (user.ID != item.ID)
                                formLayout += `<option value="${user.ID}" ${user.ID == selectedReplacementId ? 'selected' : ''} data-position="${user.WORK_POSITION}">${user.FULL_NAME}</option>`;
                        })

                        formLayout += ` </select>`;
                        formLayout += `</form>`;

                        return formLayout;
                    }
                },
                 {
                    data: 'JOB_TITLE',
                     render: function (data, type, item) {
                        let formLayout = '';
                        formLayout += `<form id="updateJob-${item.ID}" class="card" action="/ulab/user/updateJob/" method="post">`;
                        formLayout += `<input type="hidden" name="user_id" value="${item.ID}">`;

                        let selectedJob = null;
                        const existingUser = userForUpdateJob.find(user => user.user_id == item.ID);
                        if (existingUser) {
                            selectedJob = existingUser.job_title;
                        } else {
                            selectedJob = item.JOB_TITLE || '';
                        }

                        formLayout += `<input type="text" name="job_title" class="form-control job_title" placeholder="Должность" value="${selectedJob}">`;
                        formLayout += `</form>`;

                        return formLayout;
                    }
                },
                 {
                    data: 'REPLACEMENT_NOTE',
                     render: function (data, type, item) {
                        let formLayout = '';
                        formLayout += `<form id="updateReplacementNote-${item.ID}" class="card" action="/ulab/user/updateNote/" method="post" ${item.REPLACEMENT_NOTE ? 'data-bs-placement="left" data-bs-trigger="hover" data-bs-toggle="tooltip"' : ''} title="${item.REPLACEMENT_NOTE ?? ''}">`;
                        formLayout += `<input type="hidden" name="user_id" value="${item.ID}">`;

                        let selectedReplacementNote = null;
                        const existingUser = userForUpdateNotes.find(user => user.user_id == item.ID);
                        if (existingUser) {
                            selectedReplacementNote = existingUser.replacement_text;
                        } else {
                            selectedReplacementNote = item.REPLACEMENT_NOTE || '';
                        }

                        formLayout += `<input type="text" name="replacement_text" class="form-control replacement_text" placeholder="Введите заметку о пользователе" value="${selectedReplacementNote}">`;
                        formLayout += `</form>`;

                        return formLayout;
                    }
                },
            ],
            language:{
                processing: 'Подождите...',
                search: '',
                searchPlaceholder: "Поиск...",
                lengthMenu: 'Отображать _MENU_  ',
                info: 'Записи с _START_ до _END_ из _TOTAL_ записей',
                infoEmpty: 'Записи с 0 до 0 из 0 записей',
                infoFiltered: '(отфильтровано из _MAX_ записей)',
                infoPostFix: '',
                loadingRecords: 'Загрузка записей...',
                zeroRecords: 'Записи отсутствуют.',
                emptyTable: 'В таблице отсутствуют данные',
                paginate: {
                    first: 'Первая',
                    previous: 'Предыдущая',
                    next: 'Следующая',
                    last: 'Последняя'
                },
                aria: {
                    sortAscending: ': активировать для сортировки столбца по возрастанию',
                    sortDescending: ': активировать для сортировки столбца по убыванию'
                }
            },
            lengthMenu: [[10, 25, 50, 100, -1], [10,25, 50, 100, "Все"]],
            pageLength: 25,
            dom: 'frt<"bottom"lip>',
            colReorder: false,
            bSortCellsTop: true,
            order: [[ 0, 'asc' ]],
            drawCallback: function() {
                const replacementList = $('.replacement_id');
                replacementList.select2({
                    language: {
                        noResults: () => "Ничего не найдено"
                    }
                });
                replacementList.on('select2:open', function() {
                    $('.select2-search--dropdown .select2-search__field').attr("placeholder", "Начните вводить сотрудника");
                });
                replacementList.on('select2:select', function (e) {
                    const selectedElement = e.params.data.element;
                    const userPosition = selectedElement.getAttribute('data-position');

                    const formUser = selectedElement.parentElement.parentElement.parentElement.parentElement;
                    const jobInput = $(formUser).find('input[name="job_title"]');
                    jobInput.val(userPosition);
                });

                const tooltipUserTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
                const tooltipUserList = [...tooltipUserTriggerList].map(tooltipUserEl => new bootstrap.Tooltip(tooltipUserEl))

                // СТАТУС
                $('.form-control.section.status_id').on('change', function (e) {
                    const formUser = e.currentTarget.parentElement;

                    const userId = formUser.querySelector('input[name="user_id"]').value;
                    const statusId = formUser.querySelector('select[name="status_id"]').value;

                    const existingIndex = userForUpdateStatus.findIndex(user => user.user_id === userId);

                    if (existingIndex !== -1) {
                    // Если объект с таким user_id уже есть, обновляем его status_id
                        userForUpdateStatus[existingIndex].status_id = statusId;
                    } else {
                    // Если объекта с таким user_id нет, добавляем новый объект
                        userForUpdateStatus.push({ "user_id": userId, "status_id": statusId });
                    }

                    if (userForUpdateStatus.length > 0 || userForUpdateReplacement.length > 0 || userForUpdateNotes.length > 0 || userForUpdateJob.length > 0)
                        usersUpdateStatusButton.removeClass('disabled');
                    else
                        usersUpdateStatusButton.addClass('disabled');


                    let replacement_select = $(formUser.parentElement.parentElement).find('select[name="replacement_id"]');
                    let job_input = $(formUser.parentElement.parentElement).find('input[name="job_title"]');
                    if (statusId === 'default') {
                        replacement_select.prop("disabled", true);
                        replacement_select.val('NULL').trigger('change.select2');

                        job_input.val('')
                        job_input.prop('disabled', true);
                    }
                    else {
                        replacement_select.prop("disabled", false);
                        job_input.prop('disabled', false);
                    }

                    const toast = new bootstrap.Toast($('.toast-save-permission'));
                    toast.show();
                });

                // ЗАМЕНА
                $('.form-control.section.replacement_id').on('change', function (e) {
                    const formUser = e.currentTarget.parentElement;

                    const userId = formUser.querySelector('input[name="user_id"]').value;
                    const replacementId = formUser.querySelector('select[name="replacement_id"]').value;

                    const existingIndex = userForUpdateReplacement.findIndex(user => user.user_id === userId);

                    if (existingIndex !== -1) {
                    // Если объект с таким user_id уже есть, обновляем его replacement_id
                        userForUpdateReplacement[existingIndex].replacement_id = replacementId;
                    } else {
                    // Если объекта с таким user_id нет, добавляем новый объект
                        userForUpdateReplacement.push({ "user_id": userId, "replacement_id": replacementId });
                    }

                    if (userForUpdateStatus.length > 0 || userForUpdateReplacement.length > 0 || userForUpdateNotes.length > 0 || userForUpdateJob.length > 0)
                        usersUpdateStatusButton.removeClass('disabled');
                    else
                        usersUpdateStatusButton.addClass('disabled');

                    const toast = new bootstrap.Toast($('.toast-save-permission'));
                    toast.show();
                });

                // ДОЛЖНОСТЬ
                $('.form-control.job_title').on('change', function (e) {
                    const formUser = e.currentTarget.parentElement;

                    const userId = formUser.querySelector('input[name="user_id"]').value;
                    const jobTitle = formUser.querySelector('input[name="job_title"]').value;

                    const existingIndex = userForUpdateJob.findIndex(user => user.user_id === userId);

                    if (existingIndex !== -1) {
                    // Если объект с таким user_id уже есть, обновляем его replacement_text
                        userForUpdateJob[existingIndex].job_title = jobTitle;
                    } else {
                    // Если объекта с таким user_id нет, добавляем новый объект
                        userForUpdateJob.push({ "user_id": userId, "job_title": jobTitle });
                    }

                    if (userForUpdateStatus.length > 0 || userForUpdateReplacement.length > 0 || userForUpdateNotes.length > 0 || userForUpdateJob.length > 0)
                        usersUpdateStatusButton.removeClass('disabled');
                    else
                        usersUpdateStatusButton.addClass('disabled');

                    const toast = new bootstrap.Toast($('.toast-save-permission'));
                    toast.show();
                });

                // ЗАПИСКА
                $('.form-control.replacement_text').on('change', function (e) {
                    const formUser = e.currentTarget.parentElement;

                    const userId = formUser.querySelector('input[name="user_id"]').value;
                    const replacementText = formUser.querySelector('input[name="replacement_text"]').value;

                    const existingIndex = userForUpdateNotes.findIndex(user => user.user_id === userId);

                    if (existingIndex !== -1) {
                    // Если объект с таким user_id уже есть, обновляем его replacement_text
                        userForUpdateNotes[existingIndex].replacement_text = replacementText;
                    } else {
                    // Если объекта с таким user_id нет, добавляем новый объект
                        userForUpdateNotes.push({ "user_id": userId, "replacement_text": replacementText });
                    }

                    if (userForUpdateStatus.length > 0 || userForUpdateReplacement.length > 0 || userForUpdateNotes.length > 0 || userForUpdateJob.length > 0)
                        usersUpdateStatusButton.removeClass('disabled');
                    else
                        usersUpdateStatusButton.addClass('disabled');

                    const toast = new bootstrap.Toast($('.toast-save-permission'));
                    toast.show();
                });

                $('.form-control.section.status_id').each((i, e) => {
                    const formUser = e.parentElement.parentElement.parentElement;
                    let job_input = $(formUser).find('input[name="job_title"]');
                    const statusId = formUser.querySelector('select[name="status_id"]').value;

                    if (statusId === 'default')
                        job_input.prop('disabled', true);
                    else
                        job_input.prop('disabled', false);
                });
             },
        });

        let searchTimeouts = {};
        journalDataTable.columns().every(function () {
            let columnIndex = this.index();

            $(this.header()).closest('thead').find('.search:eq(' + columnIndex + ')').on('keyup change clear', function () {
                clearTimeout(searchTimeouts[columnIndex]);

                let inputElement = this;
                searchTimeouts[columnIndex] = setTimeout(function () {
                    journalDataTable
                        .column($(inputElement).parent().index())
                        .search(inputElement.value)
                        .draw();
                }, 500);
            });
        });

        /*journal filters*/
        $('.filter-btn-search').on('click', function () {
            $('#journal_filter').addClass('is-open')
            $('.filter-btn-search').hide()
        })

        $(document).on('click', function(event) {
            if (!$(event.target).closest('#filter_search').length && $('#filter_everywhere').val() === '') {
                $('#journal_filter').removeClass('is-open');
                $('.filter-btn-search').show();
            }
        });

        let debounceTimeout;
        $('.filter').on('change', function () {
           clearTimeout(debounceTimeout);

            debounceTimeout = setTimeout(function () {
                journalDataTable.draw()
            }, 350);
        })

        $('.filter-btn-reset').on('click', function () {
            location.reload()
        })
    })
    .catch(error => {
        // Обработка ошибок
    });

    usersUpdateStatusButton.click(function() {
        let oldText = usersUpdateStatusButton.text();
        usersUpdateStatusButton.text('Подождите');
        usersUpdateStatusButton.addClass('disabled');

         $.ajax({
            type: "POST",
            url: "/ulab/user/updateUsersNote/",
            data: {
                array_update_users: userForUpdateNotes
            },
            success: function(response) {
                $.ajax({
                    type: "POST",
                    url: "/ulab/user/updateUsersReplacement/",
                    data: {
                        array_update_users: userForUpdateReplacement
                    },
                    success: function(response) {
                        $.ajax({
                            type: "POST",
                            url: "/ulab/user/updateUsersJob/",
                            data: {
                                array_update_users: userForUpdateJob
                            },
                            success: function(response) {
                                $.ajax({
                                    type: "POST",
                                    url: "/ulab/user/updateUsersStatus/",
                                    data: {
                                        array_update_users: userForUpdateStatus
                                    },
                                    success: function(response) {
                                        usersUpdateStatusButton.text(oldText);
                                        location.reload();
                                    },
                                    error: function(error) {
                                        usersUpdateStatusButton.text('Произошла ошибка');
                                        console.error("Ошибка запроса:", error);
                                    }
                                });
                            },
                            error: function(error) {
                                usersUpdateStatusButton.text('Произошла ошибка');
                                console.error("Ошибка запроса:", error);
                            }
                        });
                    },
                    error: function(error) {
                        usersUpdateStatusButton.text('Произошла ошибка');
                        console.error("Ошибка запроса:", error);
                    }
                });
            },
            error: function(error) {
                usersUpdateStatusButton.text('Произошла ошибка');
                console.error("Ошибка запроса:", error);
            }
        });
    });


    function observeAjaxLoadingMessage() {
        const toastSaveMessage = $('.toast-save-permission');
        if (toastSaveMessage.is(':visible')) {
            $('#ajax-loading-message').css('margin-bottom', '94px');
        } else {
            $('#ajax-loading-message').css('margin-bottom', '0');
        }
    }

    $('.toast-save-permission').on('shown.bs.toast', () => observeAjaxLoadingMessage());
    $('.toast-save-permission').on('hidden.bs.toast', () => observeAjaxLoadingMessage());
    $('#ajax-loading-message').on('toast-check', () => observeAjaxLoadingMessage());
})
