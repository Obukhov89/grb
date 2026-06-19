<style>
    .toast-save-permission {
        min-width: 500px;
    }
    .user-edit {
        text-decoration: none;
    }
    .arrowRight {
        top: 232px;
    }
    .arrowLeft {
        top: 173px;
    }
</style>

<header class="header-requirement mb-3 pt-0">
    <nav class="header-menu w-100">
        <?php if ($_SESSION['SESS_AUTH']['USER_ID'] === 61): ?>
        <ul class="nav w-100">
            <li class="nav-item me-3">
                <a class="nav-link fa-solid icon-nav fa-arrow-left disabled" id="back-button" style="font-size: 22px;" title="Назад" data-bs-toggle="tooltip">
                </a>
            </li>
            <li class="nav-item me-3">
                <a class="nav-link fa-solid icon-nav fa-rectangle-list" href="<?= URI ?>/import/list" style="font-size: 22px;" title="Профиль лаборатории" data-bs-toggle="tooltip">
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link icon-nav fa-solid fa-user disabled" style="font-size: 22px; margin: 2px 0 0 1px;" href="<?=URI?>/user/list/" title="Пользователи (вы здесь)" data-bs-toggle="tooltip">
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link icon-nav fa-solid fa-exchange" style="font-size: 22px; margin: 2px 0 0 1px;" href="<?=URI?>/user/status/" title="Статусы" data-bs-toggle="tooltip">
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link fa-solid fa-cog icon-nav" style="font-size: 22px; margin: 2px 0 0 1px;" href="<?=URI?>/permission/list/" title="Роли" data-bs-toggle="tooltip">
                </a>
            </li>
<!--            <li class="nav-item me-5">-->
<!--                <a class="nav-link fa-solid fa-user-tie icon-nav" style="font-size: 22px; margin: 2px 0 0 1px;" href="--><?php //=URI?><!--/import/companyEmployees/" title="Руководители" data-bs-toggle="tooltip">-->
<!--                </a>-->
<!--            </li>-->

            <li class="nav-item me-3 ms-auto">
                <a class="nav-link fa-solid fa-plus icon-nav popup-with-form add-user" style="font-size: 25px;" href="#" title="Добавить пользователя" data-bs-toggle="tooltip">
                </a>
            </li>
            <li class="nav-item">
                <button class="btn btn-gradient users-update-role-trigger disabled" title="Обновить роли и/или отделы" data-bs-toggle="tooltip">Сохранить изменения</button>
            </li>
        </ul>
        <?php endif; ?>
    </nav>
</header>

<table id="journal_users" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-nowrap">Логин</th>
        <th scope="col" class="text-nowrap">ФИО</th>
        <th scope="col" class="text-nowrap">Почта</th>
        <th scope="col" class="text-nowrap">Должность</th>
        <th scope="col" class="text-nowrap">Отдел</th>
        <th scope="col" class="text-nowrap">Роль</th>
    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" class="form-control search" placeholder="Введите логин">
        </th>
        <th scope="col">
            <input type="text" class="form-control search" placeholder="Введите часть имени или фамилии">
        </th>
        <th scope="col">
            <input type="text" class="form-control search" placeholder="Введите почту">
        </th>
        <th scope="col">
            <select class="form-control search select2 select2-users">
                <option value="">Выбрать должность</option>
                <option value="Должность не указана">Должность не указана</option>
                <?php foreach ($this->data['position_list'] as $row): ?>
                    <option value="<?=$row?>"><?=$row?></option>
                <?php endforeach; ?>
            </select>
        </th>
        <th scope="col">
            <select  class="form-control search">
                <option value="">Выбрать отдел</option>
                <option value="Отдел не указан">Отдел не указан</option>
                <?php if (isset($this->data['department_all'])): ?>
                    <?php foreach ($this->data['department_all'] as $department): ?>
                        <option value="<?=$department['ID']?>" <?= $this->data['DEPARTMENT_ID'] == $department['ID'] ? 'selected' : '' ?> ><?=$department['NAME']?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </th>
        <th scope="col">
            <select  class="form-control search">
                <option value="">Выбрать роль</option>
                <?php foreach ($this->data['role_list'] as $permission): ?>
                    <option value="<?=$permission['name']?>"><?=$permission['name']?></option>
                <?php endforeach; ?>
            </select>
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="line-dashed"></div>

<form id="user-modal-form" class="bg-light mfp-hide col-md-5 m-auto p-3 position-relative"
      action="<?= URI ?>/import/insertUpdateUser/" method="post">
    <div class="title mb-3 h-2">
        Пользователь
    </div>

    <div class="line-dashed-small"></div>

    <input type="hidden" id="userId" name="user_id" value="<?= $this->data['user_id'] ?>">

    <div class="mb-3">
        <label for="name" class="form-label mb-1">Имя</label>
        <input type="text" name="NAME" class="form-control" id="name" placeholder="Введите имя"
               value="<?= $this->data['NAME'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label for="lastName" class="form-label mb-1">Фамилия</label>
        <input type="text" name="LAST_NAME" class="form-control" id="lastName" placeholder="Введите фамилию"
               value="<?= $this->data['LAST_NAME'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label for="secondName" class="form-label mb-1">Отчество</label>
        <input type="text" name="SECOND_NAME" class="form-control" id="secondName" placeholder="Введите отчество"
               value="<?= $this->data['SECOND_NAME'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label for="email" class="form-label mb-1">E-mail</label>
        <input type="email" name="EMAIL" class="form-control" id="email" placeholder="_@_._"
               value="<?= $this->data['EMAIL'] ?? '' ?>" required>
    </div>

    <div class="mb-3">
        <label for="login" class="form-label mb-1">Логин (мин. 3 символа)</label>
        <input type="text" name="LOGIN" class="form-control" id="login" placeholder="Введите логин"
               value="<?= $this->data['LOGIN'] ?? '' ?>" minlength="3" required>
    </div>

    <!--
    <div class="mb-3">
        <label for="workPosition" class="form-label mb-1">Должность</label>
        <input type="text" name="WORK_POSITION" class="form-control" id="workPosition"
               value="<?= $this->data['WORK_POSITION'] ?? '' ?>" minlength="3" required>
    </div>
    -->

    <div class="mb-3">
        <label for="workPosition" class="form-label mb-1">Должность</label>
        <input id="workPosition" class="form-control" list="workPosition_list"   placeholder="Выберите или введите должность"
               type="text" name="WORK_POSITION" value="<?= $this->data['WORK_POSITION'] ?? '' ?>" autocomplete="on" required>
        <datalist id="workPosition_list">
            <?php if (isset($this->data['position_list'])): ?>
                <?php foreach ($this->data['position_list'] as $position): ?>
                    <option data-value="<?=$position?>"><?=$position?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </datalist>
    </div>

    <div class="mb-3">
        <label for="departmentId" class="form-label mb-1">Отдел</label>
        <select name="DEPARTMENT_ID" id="departmentId" class="form-control" required>
            <?php if (isset($this->data['department_all'])): ?>
                <?php foreach ($this->data['department_all'] as $department): ?>
                    <option value="<?=$department['ID']?>" <?= $this->data['DEPARTMENT_ID'] == $department['ID'] ? 'selected' : '' ?> ><?=$department['NAME']?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="mb-3">
        <label for="newPassword" class="form-label mb-1">Новый пароль</label>
        <div class="w-100 d-flex justify-content-center align-items-center">
            <input type="password" name="NEW_PASSWORD" class="w-100 form-control" id="newPassword"
                   value="<?= $this->data['NEW_PASSWORD'] ?? '' ?>" minlength="6" maxlength="255"  placeholder="Введите новый пароль" style="min-width: auto"
                   autocomplete="new-password">
            <div class="ms-2 hidePassword fa-solid fa-eye" style="cursor: pointer; color: #5724ad; width: 21px"></div>
        </div>
    </div>

    <div class="mb-3">
        <label for="newPasswordConfirm" class="form-label mb-1">Подтверждение нового пароля</label>
        <div class="w-100 d-flex justify-content-center align-items-center">
            <input type="password" name="NEW_PASSWORD_CONFIRM" class="w-100 form-control" id="newPasswordConfirm" placeholder="Введите подтверждение нового пароля" style="min-width: auto"
                   value="<?= $this->data['NEW_PASSWORD_CONFIRM'] ?? '' ?>" minlength="6" maxlength="255"
                   autocomplete="new-password">
            <div class="ms-2 hidePasswordConfirm fa-solid fa-eye" style="cursor: pointer; color: #5724ad; width: 21px"></div>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary form-button">Сохранить</button>
</form>
<!--./user-modal-form-->


<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast toast-save-permission w-175" role="alert" aria-live="assertive" aria-atomic="true"  data-bs-delay="7000">
        <div class="toast-header">
            <strong class="me-auto">Внимание!</strong>
            <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            Не забудьте нажать на кнопку "Сохранить изменения" вверху страницы.
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="display: none" id="ajax-loading-message">
    <div class="toast show" style="width: 400px;">
        <div class="toast-header">
            <strong class="me-auto">Пожалуйста, подождите, данные загружаются...</strong>
        </div>
    </div>
</div>