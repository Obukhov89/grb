<div class="add-user-wrapper import">
    <header class="header-requirement mb-4 pt-0">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-1">
                    <a class="nav-link" href="<?= URI ?>/import/" title="Вернуться к началу работы с U-LAB">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </li>
                <li class="nav-item me-1">
                    <a class="nav-link" href="<?= URI ?>/user/list" title="Список пользователей">
                        <i class="fa-solid fa-list"></i>
                    </a>
                </li>
                <li class="nav-item me-1">
                    <a class="nav-link" href="<?= URI ?>/import/user/" title="Добавить нового пользователя">
                        <i class="fa-solid fa-plus"></i>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

    <form class="form-horizontal" method="post" action="<?= URI ?>/import/insertUpdateUser/">
        <div class="panel panel-default">
            <header class="panel-heading">
                Сотрудники
                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
            </header>
            <div class="panel-body">
                <div class="form-group row">
                    <label for="user" class="col-sm-2 col-form-label">Сотрудники</label>
                    <div class="col-sm-8">
                        <input id="user" class="form-control" list="user_list" type="text"
                               value=""
                               autocomplete="off" required>
                        <input type="hidden" name="user_id" id="user-hidden" value="<?= $this->data['user_id'] ?? '' ?>">
                        <datalist id="user_list">
                            <?php if (isset($this->data['users'])): ?>
                                <?php foreach ($this->data['users'] as $user): ?>
                                    <option data-value="<?= $user['ID'] ?>"><?= $user['USER'] ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </datalist>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <header class="panel-heading">
                Регистрационная информация
                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
            </header>
            <div class="panel-body">
                <div class="form-group row">
                    <label for="name" class="col-sm-2 col-form-label">Имя</label>
                    <div class="col-sm-8">
                        <input type="text" name="NAME" class="form-control clearable" id="name"
                               maxlength="30" value="<?= $this->data['NAME'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label for="lastName" class="col-sm-2 col-form-label">Фамилия</label>
                    <div class="col-sm-8">
                        <input type="text" name="LAST_NAME" class="form-control clearable" id="lastName"
                               value="<?= $this->data['LAST_NAME'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label for="secondName" class="col-sm-2 col-form-label">Отчество</label>
                    <div class="col-sm-8">
                        <input type="text" name="SECOND_NAME" class="form-control clearable" id="secondName"
                               value="<?= $this->data['SECOND_NAME'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">E-mail <span class="redStars">*</span></label>
                    <div class="col-sm-8">
                        <input type="email" name="EMAIL" class="form-control clearable" placeholder="_@_._"
                               value="<?= $this->data['EMAIL'] ?? '' ?>" required>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label for="login" class="col-sm-2 col-form-label">
                        Логин (мин. 3 символа) <span class="redStars">*</span>
                    </label>
                    <div class="col-sm-8">
                        <input type="text" name="LOGIN" class="form-control clearable" id="login"
                               value="<?= $this->data['LOGIN'] ?? '' ?>" minlength="3" required>
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label for="newPassword" class="col-sm-2 col-form-label">Новый пароль</label>
                    <div class="col-sm-8">
                        <input type="password" name="NEW_PASSWORD" class="form-control clearable" id="newPassword"
                               size="30" minlength="6" maxlength="255" autocomplete="new-password"
                               value="<?= $this->data['NEW_PASSWORD'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>

                <div class="form-group row">
                    <label for="newPasswordConfirm" class="col-sm-2 col-form-label">Подтверждение нового пароля</label>
                    <div class="col-sm-8">
                        <input type="password" name="NEW_PASSWORD_CONFIRM" class="form-control clearable"
                               id="newPasswordConfirm"
                               size="30" minlength="6" maxlength="255" autocomplete="new-password"
                               value="<?= $this->data['NEW_PASSWORD_CONFIRM'] ?? '' ?>">
                    </div>
                    <div class="col-sm-2"></div>
                </div>
            </div>
        </div>

        <button class="btn btn-primary block-after-click mb-4" type="submit" name="save">
            Сохранить
        </button>
    </form>
</div>