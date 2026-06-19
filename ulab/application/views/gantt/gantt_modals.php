<!-- Modals -->
<!--create user modal-->
<form id="gantt-create-user-modal" method="POST" action="/ulab/gantt/addUser"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Добавить пользователя
    </div>
    <div class="line-dashed-small"></div>
    <div class="row mb-3">
        <div class="col">
            <label for="username" class="form-label">Ф.И.О. Пользователя</label>
            <input id="username" name="name" type="text" class="form-control"/>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="position" class="form-label">Должность</label>
            <input id="position" name="position" type="text" class="form-control"/>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="salary" class="form-label">Оклад</label>
            <input id="salary" name="salary" type="number" class="form-control"/>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" onclick="submitCreateForm('gantt-create-user-modal');" class="btn btn-primary">Сохранить
        </button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary"
                style="margin-left: 5px">Закрыть
        </button>
    </div>
</form>

<!--create project modal-->
<form id="gantt-create-project-modal" method="POST" action="/ulab/gantt/addProject"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" onsubmit="generateProjectColor();">
    <div class="title mb-3 h-2">
        Добавить проект
    </div>
    <div class="line-dashed-small"></div>
    <div class="row mb-3">
        <div class="col">
            <label for="project_name" class="form-label">Название проекта</label>
            <input id="project_name" name="project_name" type="text" class="form-control"/>
            <input id="color1" name="color1" type="hidden"/>
            <input id="color2" name="color2" type="hidden"/>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" onclick="generateProjectColor();submitCreateForm('gantt-create-project-modal');"
                class="btn btn-primary">Сохранить
        </button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary"
                style="margin-left: 5px">Закрыть
        </button>
    </div>
</form>

<!--show user edit modal-->
<form id="gantt-show-user-modal" method="POST" action="/ulab/gantt/editUser"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2 mt-3">
        <input id="user_name" name="name" type="text" class="form-control"/>
    </div>
    <div class="line-dashed-small"></div>
    <div class="row mb-3">
        <div class="col-md">
<!--            <label for="user_salary" class="form-label">Оклад</label>-->
            <div class="input-group">
<!--                <input id="user_salary" name="salary" type="number" class="form-control"/>-->
                <button onclick="showSalaryInfo(event);" id="edit_salary_button" title="Редактировать оклад" class="btn btn-secondary" type="button">Редактировать оклад &nbsp;<i class="fa fa-edit"></i></button>
            </div>
            <input id="user_id" name="user_id" type="hidden"/>
            <input id="user_project_id" name="user_project_id" type="hidden"/>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="user_position" class="form-label">Должность</label>
            <input id="user_position" name="position" type="text" class="form-control"/>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <button onclick="openInfoModal(event);" class="btn btn-secondary" type="button">Информация</button>
        </div>
    </div>

    <div id="timelines_part">
        <div class="line-dashed-small"></div>

        <h2>Таймлайны</h2>
        <div id="timeline_container">

        </div>
    </div>

    <div class="d-flex">
        <button type="button" onclick="submitCreateForm('gantt-show-user-modal');"
                class="btn btn-primary">Сохранить
        </button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary"
                style="margin-left: 5px">Закрыть
        </button>
    </div>
</form>

<!--show user project modal-->
<form id="gantt-show-project-modal" method="POST" action="/ulab/gantt/editProject"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Редактирование проекта
    </div>
    <div class="line-dashed-small"></div>
    <div class="row mb-3">
        <div class="col">
            <label for="project_name" class="form-label">Название проекта</label>
            <input disabled id="proj_name" name="project_name" type="text" class="form-control"/>
            <input id="project_id" name="project_id" type="hidden" class="form-control"/>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="add_users_select" class="form-label">Добавить в проект: </label>
            <select class="form-select" id="add_users_select" name="project_member_id">
                <option value="-1">Выбрать</option>
            </select>
        </div>
    </div>

    <div id="timelines_part_2">
        <div class="line-dashed-small"></div>

        <h2>Таймлайны</h2>
        <div id="timeline_container_2">

        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" onclick="submitCreateForm('gantt-show-project-modal');"
                class="btn btn-primary">Сохранить
        </button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary"
                style="margin-left: 5px">Закрыть
        </button>
    </div>
</form>

<!--Редактирования оклада-->
<form id="edit-salary-modal" method="POST" action="/ulab/gantt/editUserSalary" class="bg-light mfp-hide col-md-6 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Редактирование оклада <span id="username_title"></span>
    </div>
    <div class="line-dashed-small"></div>

    <table class="table" id="user_salary_table">
        <thead>
        <tr>
            <th class="text-center">Месяц</th>
            <th class="text-center">Белая</th>
            <th class="text-center">Налог</th>
            <th class="text-center">Серая</th>
            <th class="text-center">Сумма</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
    <div class="d-flex">
        <button type="button" onclick="submitCreateForm('edit-salary-modal');" class="btn btn-primary">Сохранить</button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary" style="margin-left: 5px">Закрыть</button>
    </div>
</form>

<!--edit user's hours modal-->
<form id="gantt-edit-hours" action="/ulab/gantt/addHours" method="POST"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Редактирование часов <span id="title_emp_name"></span>
    </div>
    <div class="line-dashed-small"></div>
    <div class="row mb-3">
        <div class="col">
            <label for="project_hours" class="form-label">Часы</label>
            <input name="project_hours" id="project_hours" type="number" min="0" max="24" />

            <input name="project_sql_date" id="project_sql_date" type="hidden" />
            <input name="project_id" id="project_id" type="hidden" />
            <input name="user_id" id="user_id" type="hidden" />
            <input name="timeline_id" id="timeline_id" type="hidden" />
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" onclick="submitCreateForm('gantt-edit-hours');"
                class="btn btn-primary">Сохранить
        </button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary"
                style="margin-left: 5px">Закрыть
        </button>
    </div>
</form>

<!--edit user's hours modal by range-->
<form id="gantt-edit-hours-by-range" action="/ulab/gantt/editHoursByRange" method="POST"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Редактирование часов <span class="title_emp_name"></span>
    </div>
    <div class="line-dashed-small"></div>
    <div class="row mb-3">
        <div class="col">
            <label for="start_date" class="form-label">Дата начала</label>
            <input id="start_date" name="start_date" required type="date" class="form-control">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="end_date" class="form-label">Дата завершения</label>
            <input id="end_date" name="end_date" required type="date" class="form-control">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col">
            <label for="hours" class="form-label">Количество часов</label>
            <input id="hours" name="hours" min="0" max="24" required type="number" class="form-control">
            <input name="user_id" type="hidden">
            <input name="timeline_id" type="hidden">
            <input name="project_id" type="hidden">
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" onclick="submitCreateForm('gantt-edit-hours-by-range');"
                class="btn btn-primary">Сохранить
        </button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary"
                style="margin-left: 5px">Закрыть
        </button>
    </div>
</form>

<!--edit production calendar-->
<form id="gantt-production-calendar" action="/ulab/gantt/editProductionCalendar" method="POST"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Производственный календарь <?php echo date('Y'); ?>
    </div>
    <div class="line-dashed-small"></div>

    <table class="table table-bordered">
        <thead>
        <tr>
            <td>Название месяца</td>
            <td>Кол-во рабочих часов</td>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($this->data['production_calendar'] as $calendar) { ?>
            <tr>
                <td><?= $calendar['month_name'] ?></td>
                <td><input type="number" min="0.1" max="1000" step="0.1"
                           class="form-control"
                           name="work_hours[<?= $calendar['id']; ?>]"
                           value="<?= $calendar['work_hours_count'] ?>"
                    />
                </td>
            </tr
        <?php } ?>
        </tbody>
    </table>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" onclick="submitCreateForm('gantt-production-calendar');"
                class="btn btn-primary">Сохранить
        </button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary"
                style="margin-left: 5px">Закрыть
        </button>
    </div>
</form>


<!--show info modal-->
<form id="gantt-info-modal" action="#" method="POST"
      class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        Рабочее время <span id="info-modal-username"></span>
    </div>
    <div class="line-dashed-small"></div>

    <div class="list-group" id="info-list">
    </div>

    <div class="line-dashed-small"></div>

    <div class="d-flex">
        <button type="button" onclick="submitCreateForm('gantt-info-modal');"
                class="btn btn-primary">Сохранить
        </button>
        <button type="button" onclick="$.magnificPopup.close();" data-js-close-modal class="btn btn-secondary"
                style="margin-left: 5px">Закрыть
        </button>
    </div>
</form>
