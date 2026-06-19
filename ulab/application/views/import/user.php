<header class="header-user mb-3">
    <div class="row">
        <div class="col">
            <nav class="header-menu">
                <ul class="nav">
                    <li class="nav-item me-1">
                        <a class="nav-link" href="<?= URI ?>/import/" title="Вернуться к началу работы с U-LAB">
                            <i class="fa-solid fa-arrow-left"></i>
                        </a>
                    </li>
                    <li class="nav-item me-1">
                        <a class="nav-link popup-with-form" href="#" title="Новый пользователь">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>

        <div class="col">
            <div class="filters">
                <div class="row">
                    <div class="col">
                        <select id="selectStage" class="form-control filter filter-stage">
                            <option value="Y">Активные</option>
                            <option value="N">Не активные</option>
                        </select>
                    </div>

                    <div class="col-auto">
                        <button type="button" class="btn btn-gradient filter-btn-reset bg-white">Сбросить</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>

<table id="journal_users" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-nowrap">Логин</th>
        <th scope="col" class="text-nowrap">Имя</th>
        <th scope="col" class="text-nowrap">Фамилия</th>
        <th scope="col" class="text-nowrap">Отчество</th>
        <th scope="col" class="text-nowrap">E-mail</th>
        <th scope="col">Подразделение</th>
    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class='arrowLeft'>
    <svg class="bi" width="40" height="40">
        <use xlink:href="<?=URI?>/assets/images/icons.svg#arrow-left"/>
    </svg>
</div>
<div class='arrowRight'>
    <svg class="bi" width="40" height="40">
        <use xlink:href="<?=URI?>/assets/images/icons.svg#arrow-right"/>
    </svg>
</div>

<form id="user-modal-form" class="bg-light mfp-hide col-md-5 m-auto p-3 position-relative"
      action="<?= URI ?>/import/insertUpdateUser/" method="post">
    <div class="title mb-3 h-2">
        Пользователь
    </div>

    <div class="line-dashed-small"></div>

    <input type="hidden" id="userId" name="user_id" value="<?= $this->data['user_id'] ?>">

    <div class="mb-3">
        <label for="name" class="form-label mb-1">Имя</label>
        <input type="text" name="NAME" class="form-control" id="name"
               value="<?= $this->data['NAME'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label for="lastName" class="form-label mb-1">Фамилия</label>
        <input type="text" name="LAST_NAME" class="form-control" id="lastName"
               value="<?= $this->data['LAST_NAME'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label for="secondName" class="form-label mb-1">Отчество</label>
        <input type="text" name="SECOND_NAME" class="form-control" id="secondName"
               value="<?= $this->data['SECOND_NAME'] ?? '' ?>">
    </div>

    <div class="mb-3">
        <label for="email" class="form-label mb-1">E-mail</label>
        <input type="email" name="EMAIL" class="form-control" id="email" placeholder="_@_._"
               value="<?= $this->data['EMAIL'] ?? '' ?>" required>
    </div>

    <div class="mb-3">
        <label for="login" class="form-label mb-1">Логин (мин. 3 символа)</label>
        <input type="text" name="LOGIN" class="form-control" id="login"
               value="<?= $this->data['LOGIN'] ?? '' ?>" minlength="3" required>
    </div>

    <div class="mb-3">
        <label for="newPassword" class="form-label mb-1">Новый пароль</label>
        <input type="password" name="NEW_PASSWORD" class="form-control" id="newPassword"
               value="<?= $this->data['NEW_PASSWORD'] ?? '' ?>" minlength="6" maxlength="255"
               autocomplete="new-password">
    </div>

    <div class="mb-3">
        <label for="newPasswordConfirm" class="form-label mb-1">Подтверждение нового пароля</label>
        <input type="password" name="NEW_PASSWORD_CONFIRM" class="form-control" id="newPasswordConfirm"
               value="<?= $this->data['NEW_PASSWORD_CONFIRM'] ?? '' ?>" minlength="6" maxlength="255"
               autocomplete="new-password">
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>
<!--./user-modal-form-->

<div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2 alert-title"></div>

    <div class="line-dashed-small"></div>

    <div class="mb-3 alert-content"></div>
</div>
<!--./alert_modal-->
