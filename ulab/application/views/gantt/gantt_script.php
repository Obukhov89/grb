<script>
    const editProject = (projectId, userId = null) => {
        let url = "/ulab/gantt/getProject";
        let data = {
            'id': projectId,
            'user_id': userId,
        };

        $.post(url, data, function (response) {
            response = JSON.parse(response);
            if (response.error) {
                console.log(response.message);
                return;
            }

            const project = response.data;

            $('#proj_name').val(project.project_name);
            $('#project_id').val(project.id);

            // вывод таймлайнов пользователя по выбранному проекту
            if (response.data.timelines) {
                let timeLineContainer = $("#timeline_container_2").empty();
                let timeLines = response.data.timelines;
                let html = "";

                for (let i = 0; i < timeLines.length; i++) {
                    let timeLine = timeLines[i];
                    let start_date = deleteTimeFromDate(timeLine.start_date);
                    let end_date = deleteTimeFromDate(timeLine.end_date);

                    let item = `
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label">Дата начала</label>
                                        <input name="dates[${i}][start_date]" required value="${start_date}" type="date" class="form-control" />
                                        <input name="dates[${i}][row_id]" value="${timeLine.id}" type="hidden" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label">Дата завершения</label>
                                        <input name="dates[${i}][end_date]" value="${end_date}" type="date" class="form-control" />
                                    </div>
                                </div>
                                <div class="line-dashed-small"></div>`;

                    html += item;
                }
                timeLineContainer.append(html);
            } else {
                $('#timelines_part_2').empty();
            }


            $.magnificPopup.open({
                items: {
                    src: '#gantt-show-project-modal',
                    type: 'inline'
                },
                fixedContentPos: false,
            });
            focusFirstInput("gantt-show-project-modal");

            let users = [
                <?php
                foreach ($this->data['table']['users'] as $user) {
                    // 'salary': {$user['salary']},
                    echo "
                  {
                        'id': {$user['id']},
                        'name': '" . $user['name'] . "',
                        'position': '" . $user['position'] . "',
                  },
                  ";
                }?>];
            // todo: тут надо вычислить каких пользователей нет в проекте и сунуть их в селект

            putUsersInSelect(users);
        });
    }

    const getUsers = () => {
        let users = [
            <?php
            foreach ($this->data['table']['users'] as $user) {
                //'salary': {$user['salary']},
                echo "
                  {
                        'id': {$user['id']},
                        'name': '" . $user['name'] . "',
                        'position': '" . $user['position'] . "',
                  },
                  ";
            }?>];

        return users;
    };

    const putUsersInSelect = (users) => {
        let select = $('#add_users_select');
        select.empty();
        select.append(`<option value="-1">Выбрать</option> `);
        for (let user of users) {
            select.append(`<option value='${user.id}'>${user.name}</option>`);
        }
    }

    const deleteTimeFromDate = (date) => {
        if (!date) {
            return date;
        }

        return date.trim().split(" ")[0];
    }

    const editUser = (userId, projectId = null) => {
        let url = "/ulab/gantt/getUser";
        let data = {
            'id': userId,
            'project_id': projectId,
        };

        $.post(url, data, function (response) {
            response = JSON.parse(response);
            if (response.error) {
                console.log(response.message);
                return;
            }

            // $('#edit_salary_button').on('click', (event) => {
            //     event.stopPropagation();
            //     editSalary(response.data);
            // });

            $('#user_name').val(response.data.name);
            // $('#user_salary').val(response.data.salary);
            $('#user_position').val(response.data.position);
            $('#user_id').val(response.data.id);
            $('#user_project_id').val(projectId);

            // вывод таймлайнов пользователя по выбранному проекту
            if (response.data.timelines) {
                let timeLineContainer = $("#timeline_container").empty();
                let timeLines = response.data.timelines;
                let html = "";

                for (let i = 0; i < timeLines.length; i++) {
                    let timeLine = timeLines[i];
                    let start_date = deleteTimeFromDate(timeLine.start_date);
                    let end_date = deleteTimeFromDate(timeLine.end_date);

                    let item = `
                                <div class="row mb-3">
                                    <div class="col">
                                        <label class="form-label">Дата начала</label>
                                        <input name="dates[${i}][start_date]" required value="${start_date}" type="date" class="form-control" />
                                        <input name="dates[${i}][row_id]" value="${timeLine.id}" type="hidden" />
                                    </div>
                                    <div class="col">
                                        <label class="form-label">Дата завершения</label>
                                        <div class="input-group">
                                            <input name="dates[${i}][end_date]" value="${end_date}" type="date" class="form-control" />
                                            <button onclick="openEditHoursByRangeModal(event, ${projectId}, ${response.data.id}, '${response.data.name}', '${timeLine.id}');" title="Редактировать часы" class="btn btn-outline-secondary edit_hours_by_range_button" style="margin-left: 5px;" type="button"><i class="fa-regular fa-clock"></i></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="line-dashed-small"></div>`;

                    html += item;
                }
                timeLineContainer.append(html);
            } else {
                $('#timelines_part').empty();
            }


            $.magnificPopup.open({
                items: {
                    src: '#gantt-show-user-modal',
                    type: 'inline'
                },
                fixedContentPos: false,
            });
            focusFirstInput("gantt-show-user-modal");
        });
    }

    const openEditHoursByRangeModal = (event, projectId, userId, userName, timelineId) => {
        event.stopPropagation();

        $('#gantt-edit-hours-by-range .title_emp_name').text(userName);
        $('#gantt-edit-hours-by-range [name="timeline_id"]').val(timelineId);
        $('#gantt-edit-hours-by-range [name="user_id"]').val(userId);
        $('#gantt-edit-hours-by-range [name="project_id"]').val(projectId);

        $.magnificPopup.open({
            items: {
                src: '#gantt-edit-hours-by-range',
                type: 'inline'
            },
            fixedContentPos: false,
        });
    }

    const openInfoModal = (event) => {
        event.stopPropagation();
        $('#info-list').empty();
        $('#info-list').text("Загрузка...");
        $("#info-modal-username").text("");

        const formatter = new Intl.NumberFormat('ru-RU', {
            style: 'currency',
            currency: 'RUB',
        });

        const data = {
            'user_id': $('#user_id').val(),
        };
        const url = "/ulab/gantt/getUserInfo";

        $.post(url, data, function (res) {
            let response = JSON.parse(res);
            $('#info-list').empty();
            if (response.length === 0) {
                $('#info-list').text("Нет данных.");

                return;
            }

            let currentUser = getUsers().find(user => parseInt(user.id) === parseInt(data.user_id));
            $("#info-modal-username").text(currentUser.name);

            if (response.data.length === 0) {
                $('#info-list').text("Нет данных.");

                return;
            }

            for (const item of response.data) {
                let html = `<div class="list-group-item flex-column align-items-start">
            <div class="d-flex mt-0 mb-0 w-100">
                <p class="mb-0" style="font-weight: bold;">Месяц:&nbsp;</p> <p class="mb-0">${item.month_name}</p>
            </div>
            <div class="d-flex mt-0 mb-0 w-100">
                <p class="mb-0" style="font-weight: bold;">Кол-во рабочих часов в месяце:&nbsp;</p> <p class="mb-0">${item.work_hours_count}</p>
            </div>
            <div class="d-flex mt-0 mb-0 w-100">
                <p class="mb-0" style="font-weight: bold;">Стоимость часа сотрудника в месяце:&nbsp;</p> <p class="mb-0">${formatter.format(item.avg_hour_rate)}</p>
            </div>
            <div class="d-flex mt-0 mb-0 w-100">
                <p class="mb-0" style="font-weight: bold;">Кол-во рабочих часов сотрудника:&nbsp;</p> <p class="mb-0">${item.user_work_hours}</p>
            </div>
            <div class="d-flex mt-0 mb-0 w-100">
                <p class="mb-0" style="font-weight: bold;">Сумма:&nbsp;</p> <p class="mb-0">${formatter.format(item.salary_by_hours)}</p>
            </div>
        </div>
        <hr />`;

                $('#info-list').append(html);
            }
        });

        $.magnificPopup.open({
            items: {
                src: '#gantt-info-modal',
                type: 'inline'
            },
            fixedContentPos: false,
        });
    }

    const submitCreateForm = (formId) => {
        let form = $(`#${formId}`);

        let inputs = $(`#${formId} input[type='text']`);

        let error = false;
        for (let elem of inputs) {
            if ($(elem).val().trim().length === 0) {
                $(elem).addClass('is-invalid');
                error = true;
            } else {
                $(elem).removeClass('is-invalid');
            }
        }

        if (!error) {
            form.submit();
            return;
        }
    };


    const generateRandomContrastColor = () => {
        // const colors = ['blue', '#3fe54f', '#e53fdd', '#7d23e2', '#14e7e6', '#00adff'];
        const colors = ['#808080', '#f0f0f0', '#e6e6e6', '#d9d9d9',
            '#cccccc', '#bfbfbf', '#b3b3b3', '#a6a6a6',
            '#999999', '#8c8c8c', '#808080', '#737373',
            '#666666', '#595959', '#4c4c4c', '#404040',
            '#333333', '#f5f5f5', '#ececec', '#e2e2e2',
            '#d9d9d9', '#cfcfcf'];
        let firstIndex = Math.floor(Math.random() * colors.length);
        let secondIndex = Math.floor(Math.random() * colors.length);

        if (firstIndex === secondIndex) {
            secondIndex = colors.length - 1 - firstIndex;
        }

        let color1 = colors[firstIndex];
        let color2 = colors[secondIndex];

        return [color1, color2];
    }

    const generateProjectColor = () => {
        const colors = generateRandomContrastColor();
        const color1 = colors[0];
        const color2 = colors[1];


        $('#color1').val(color1);
        $('#color2').val(color2);

        // var timelineElement = document.getElementById("timeline2");
        // var gradient = "linear-gradient(to right, " + color1 + ", " + color2 + ")";
        // timelineElement.style.background = gradient;
    }

    const focusFirstInput = (modalId) => {
        setTimeout(() => {
            $(`#${modalId} input`).first()[0].focus();
        }, 100);
    }

    function addLeadingZero(num) {
        return num < 10 ? '0' + num : num;
    }

    document.addEventListener("DOMContentLoaded", () => {
        $("body").on("click", "[data-js-create-user]", function (e) {
            $.magnificPopup.open({
                items: {
                    src: '#gantt-create-user-modal',
                    type: 'inline'
                },
                fixedContentPos: false,
            });
            focusFirstInput("gantt-create-user-modal");
        });

        $("body").on("click", "[data-js-show-product-calendar]", function (e) {
            $.magnificPopup.open({
                items: {
                    src: '#gantt-production-calendar',
                    type: 'inline'
                },
                fixedContentPos: false,
            });
            focusFirstInput("gantt-production-calendar");
        });

        $("body").on("click", "[data-js-create-project]", function (e) {
            $.magnificPopup.open({
                items: {
                    src: '#gantt-create-project-modal',
                    type: 'inline'
                },
                fixedContentPos: false,
            });
            focusFirstInput("gantt-create-project-modal");
        });

        $('.time_cell').on('click', function (e){
            const day = e.target.dataset.day;
            const month = e.target.dataset.month;
            const project_id = e.target.dataset.project_id;
            const user_id = e.target.dataset.user_id;
            const year = e.target.dataset.year;
            const timelineId = e.target.dataset.timeline_id;

            if (!project_id || !user_id) {
                return;
            }

            const date = new Date(year, month - 1, day);
            const formattedDate = addLeadingZero(date.getDate()) + '.' + addLeadingZero(date.getMonth() + 1) + '.' + date.getFullYear();
            const SQLDate = `${date.getFullYear()}-${addLeadingZero(date.getMonth() + 1)}-${addLeadingZero(date.getDate())}`;

            const currentUser = getUsers().find(user => parseInt(user.id) === parseInt(user_id));

            document.getElementById("title_emp_name").textContent = currentUser.name + " " + formattedDate;
            document.querySelector('#gantt-edit-hours [name="user_id"]').value = user_id;
            document.querySelector('#gantt-edit-hours [name="project_sql_date"]').value = SQLDate;
            document.querySelector('#gantt-edit-hours [name="project_id"]').value = project_id;
            document.querySelector('#gantt-edit-hours [name="timeline_id"]').value = timelineId;

            if (e.target.innerText.trim().length) {
                document.getElementById('project_hours').value = e.target.innerText;
                document.getElementById('gantt-edit-hours').action = "/ulab/gantt/editHours";
            } else{
                document.getElementById('gantt-edit-hours').action = "/ulab/gantt/addHours";
            }

            $.magnificPopup.open({
                items: {
                    src: '#gantt-edit-hours',
                    type: 'inline'
                },
                fixedContentPos: false,
            });
        });
    });

    const grabTimeLinesToTargetCells = (timelinesInfo) => {
        for (let elem of timelinesInfo) {
            for (let userTimeLine of elem.projectTimeLines) {
                const endDate = getEndDate(userTimeLine.end_date);

                let timelines = $(`.table_timeline[data-project_id="${userTimeLine.project_id}"][data-user_id="${userTimeLine.user_id}"][data-timeline_id="${userTimeLine.id}"]`);

                let lastCell = $('#gantt_table tbody tr:first .table_cell').last();

                let endDateTimeStamp = new Date(endDate.year, endDate.month - 1, endDate.day);
                let lastDateTimeStamp = new Date(lastCell.attr('data-year'), lastCell.attr('data-month') - 1, lastCell.attr('data-day'));

                let targetCell;
                if (endDateTimeStamp > lastDateTimeStamp) {
                    targetCell = $(`.table_cell[data-project_id="${userTimeLine.project_id}"][data-user_id="${userTimeLine.user_id}"]`).last();
                } else {
                    targetCell = $(`.table_cell[data-project_id="${userTimeLine.project_id}"][data-user_id="${userTimeLine.user_id}"][data-day="${endDate.day}"][data-month="${endDate.month}"][data-year="${endDate.year}"]`);
                }

                if (timelines.length && targetCell.length) {
                    v2grabByRightSide(timelines[0], targetCell[0]);
                }
            }
        }
    }

    const getEndDate = (end_date) => {
        let endDate = end_date;
        let endDateDay;
        let endDateMonth;
        let endDateYear;
        if (endDate == null || endDate === "0000-00-00 00:00:00") {
            endDate = new Date();
            endDateDay = endDate.getDate();
            endDateMonth = endDate.getMonth() + 1;
            endDateYear = endDate.getFullYear();
        } else {
            endDate = new Date(endDate.split(' ')[0]);
            endDateDay = endDate.getDate();
            endDateMonth = endDate.getMonth() + 1;
            endDateYear = endDate.getFullYear();
        }

        return {
            'day': endDateDay,
            'month': endDateMonth,
            'year': endDateYear,
        }
    }

    const openSecondView = (viewMode) => {
        let params = new URLSearchParams(window.location.search);
        let currentViewMode = parseInt(params.get('VIEW_MODE'));

        if (isNaN(currentViewMode) && viewMode === 1) {
            return;
        }
        if (viewMode === currentViewMode) {
            return;
        }

        if (parseInt(params.get('VIEW_MODE')) === 2) {
            params.set('VIEW_MODE', '1');
        } else {
            params.set('VIEW_MODE', '2');
        }

        window.history.replaceState({}, '', window.location.pathname + '?' + params.toString());
        window.location.reload();
    }

    const recalculateSum = (index, sumId) => {
        const salary = $(`input[name="salary[${index}][salary]"]`);
        const tax = $(`input[name="salary[${index}][tax]"]`);
        const grey_salary = $(`input[name="salary[${index}][grey_salary]"]`);

        const sum = (parseFloat(salary.val() || 0) + parseFloat(tax.val() || 0) + parseFloat(grey_salary.val() || 0)) || 0;

        $(`#${sumId}`).val(sum);

        return;
        let inputs = $('#user_salary_table.table input');
        let summa = 0;
        for (const item of inputs) {
            let input = $(item);
            if(input.attr('name') === "sum") {
                continue
            }

            summa += parseFloat(input.val()) || 0;
        }

        $(`[name="sum"]`).val(summa);
    }

    const editSalary = (userData) => {
        if (!userData) {
            //todo: тут избавиться от алерта, ну детский сад какой-то
            alert("Ошибка при получении данных пользователя");

            return;
        }

        $('[name="salary"]').val(userData.salary);
        $('[name="tax"]').val(userData.tax);
        $('[name="grey_salary"]').val(userData.grey_salary);
        $('[name="sum"]').val(userData.sum);
        $('#username_title').text(userData.name);
        $('[name="employer_id"]').val(userData.id);

        $.magnificPopup.open({
            items: {
                src: '#edit-salary-modal',
                type: 'inline'
            },
            fixedContentPos: false,
        });
    }

    const showSalaryInfo = (event) => {
        event.stopPropagation();
        $("#user_salary_table tbody").empty()

        const userId = $('#user_id').val();
        const user = getUsers().find(user => parseInt(user.id) === parseInt(userId));
        $('#username_title').text(user.name);

        const data = {
            'user_id': userId,
        };
        const url = "/ulab/gantt/getUserSalaryInfo";

        $.post(url, data, function (res) {
            let response = JSON.parse(res);
            if (response.data.length === 0) {
                return;
            }

            for (let index in response.data) {
                const elem = response.data[index];
                const sumId = `sum_${index}`;
                let html = `<tr><td>${elem.formatted_date}</td>
            <td><input type="number" min="0" class="form-control" oninput="recalculateSum('${index}', '${sumId}')" name="salary[${index}][salary]" value="${elem.salary}" /></td>
            <td><input type="number" min="0" class="form-control" oninput="recalculateSum('${index}', '${sumId}')" name="salary[${index}][tax]" value="${elem.tax}" /></td>
            <td><input type="number" min="0" class="form-control" oninput="recalculateSum('${index}', '${sumId}')" name="salary[${index}][grey_salary]" value="${elem.grey_salary}" /></td>
            <td><input type="number" class="form-control" value="${elem.sum}" id="${sumId}" disabled /></td>
            <input type="hidden" value="${elem.id}" name="salary[${index}][salary_id]" />
        </tr>`;

                $('#user_salary_table tbody').append(html);
            }
        });

        $.magnificPopup.open({
            items: {
                src: '#edit-salary-modal',
                type: 'inline'
            },
            fixedContentPos: false,
        });
    }


    document.addEventListener("DOMContentLoaded", () => {

        let url = "/ulab/gantt/collectTimeLineInfo";
        let data = {};
        $.post(url, data, function (response) {
            response = JSON.parse(response);
            // console.log(response.data)
            grabTimeLinesToTargetCells(response.data);

            // v2grabByRightSide();
        });
    });
</script>