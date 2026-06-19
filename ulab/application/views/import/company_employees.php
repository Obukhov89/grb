<style>
    .header-menu, .nav {
        width: 100%;
    }

    .header-menu .nav-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .select2-container .select2-selection--single {
        padding: 0.25rem 0.15rem;
        height: auto;
        border-radius: 0.25rem;
        box-sizing: border-box;
        min-width: 100%;
        border: var(--bs-border-width) solid var(--bs-border-color);
    }
    #workarea input.form-control, #workarea select.form-control, .mfp-content .form-control {
        min-width: auto;
    }
</style>

<div class="company-info-wrapper import">
    <header class="header-requirement mb-3 pt-0">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-3">
                    <a class="nav-link fa-solid icon-nav fa-arrow-left disabled" id="back-button" style="font-size: 22px;" title="Назад" data-bs-toggle="tooltip"></a>
                </li>
                <li class="nav-item me-3">
                    <a class="nav-link fa-solid icon-nav fa-rectangle-list" href="<?= URI ?>/import/list" style="font-size: 22px;" title="Профиль лаборатории" data-bs-toggle="tooltip"></a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link fa-solid icon-nav fa-user" style="font-size: 22px; margin: 2px 0 0 1px;" href="<?=URI?>/user/list/" title="Пользователи" data-bs-toggle="tooltip"></a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link fa-solid icon-nav fa-exchange" style="font-size: 22px; margin: 2px 0 0 1px;" href="<?=URI?>/user/status/" title="Статусы" data-bs-toggle="tooltip"></a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link fa-solid fa-cog icon-nav" style="font-size: 22px; margin: 2px 0 0 1px;" href="<?=URI?>/permission/list/" title="Роли" data-bs-toggle="tooltip"></a>
                </li>
                <li class="nav-item me-5">
                    <a class="nav-link fa-solid fa-user-tie icon-nav disabled" style="font-size: 22px; margin: 2px 0 0 1px;" href="<?=URI?>/import/companyEmployees/" title="Руководители" data-bs-toggle="tooltip"></a>
                </li>
            </ul>
        </nav>
    </header>

    <form class="form-horizontal" method="post" id="form_emp" action="<?= URI ?>/import/insertUpdateEmployees/">
        <div class="panel panel-default">
            <header class="panel-heading">
                Директор
                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
            </header>
            <div class="panel-body">

                <input type="hidden" name="id" value="<?= $this->data['form']['id'] ?? '' ?>">

                <fieldset class="border rounded-3 p-3">
                    <legend class="float-none w-auto px-3">
                        <div class="col-sm-12">
                            <select name="form[director_id]" class="form-control section director_id" style="min-width: 750px" required>
                                <?php
                                    $haveDirector = empty($this->data['form']['director_id']) ? 'selected' : '';
                                    echo "<option value='' disabled $haveDirector >Выберите руководителя из списка сотрудников в системе</option>";
                                ?>
                                <?php
                                    foreach ($this->data['users'] as $user) {
                                        $userId = $user['ID'];
                                        $userFullName = trim(htmlspecialchars($user['USER'])); // ФИО руководителя
                                        $userFullNameGenitive = trim(htmlspecialchars($user['USER_GENITIVE'])); // ФИО в родительном падеже
                                        $userWorkPosition = trim(htmlspecialchars($user['WORK_POSITION'])); // Должность руководителя
                                        $userWorkPositionGenitive = trim(htmlspecialchars($user['WORK_POSITION_GENITIVE'])); // Должность руководителя в родительном падеже
                                        $userShortName = trim(htmlspecialchars($user['SHORT_NAME'])); // Короткая запись имени руководителя

                                        $replacementUser = trim(htmlspecialchars($user['REPLACEMENT_SHORT_NAME'])) ?? NULL;
                                        $dataReplacement = ($replacementUser !== NULL && !empty($replacementUser)) ? 'data-replacement="' . htmlspecialchars($replacementUser) . '"' : '';

                                        $selected = ($userId == $this->data['form']['director_id']) ? 'selected' : '';

                                        echo '<option value="' . $userId . '" 
                                        data-director="' . $userFullName . '" 
                                        data-director_genitive="' . $userFullNameGenitive . '" 
                                        data-position="' . $userWorkPosition . '" 
                                        data-position_genitive="' . $userWorkPositionGenitive . '" 
                                        data-director_short="' . $userShortName . '" ' . $dataReplacement . ' ' . $selected . '>';
                                        echo $userFullName;
                                        echo '</option>';
                                    }
                                ?>
                            </select>
                            <label class="col-form-label" style="font-weight: 500;" >: исполняет роль директора</label>
                        </div>
                    </legend>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">Должность руководителя</label>
                        <div class="col-sm-8">
                            <input type="text" name="form[position]" class="form-control clearable" required
                                   value="<?= $this->data['form']['position'] ?? '' ?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">Должность руководителя в родительном падеже</label>
                        <div class="col-sm-8">
                            <input type="text" name="form[position_genitive]" class="form-control clearable" required
                                   value="<?= $this->data['form']['position_genitive'] ?? '' ?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row align-items-center mt-5">
                        <label class="col-sm-2 col-form-label">ФИО руководителя</label>
                        <div class="col-sm-8">
                            <input type="text" name="form[director]" class="form-control clearable" required
                                   value="<?= $this->data['form']['director'] ?? '' ?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">ФИО в родительном падеже</label>
                        <div class="col-sm-8">
                            <input type="text" name="form[director_genitive]" class="form-control clearable" required
                                   value="<?= $this->data['form']['director_genitive'] ?? '' ?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">Короткая запись имени руководителя</label>
                        <div class="col-sm-8">
                            <input type="text" name="form[director_short]" class="form-control clearable" required
                                   value="<?= $this->data['form']['director_short'] ?? '' ?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row align-items-center mt-5">
                        <label class="col-sm-2 col-form-label">Действует на основании</label>
                        <div class="col-sm-8">
                            <input type="text" name="form[acts_basis]" class="form-control" required
                                   value="<?= $this->data['form']['acts_basis'] ?? '' ?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>
                </fieldset>
            </div>
        </div>

        <!-- Бухгалтер -->
        <div class="panel panel-default mt-2">
            <header class="panel-heading">
                Главный бухгалтер
                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
            </header>
            <div class="panel-body">
                <fieldset class="border rounded-3 p-3 mt-0">
                    <legend class="float-none w-auto px-3">
                        <div class="col-sm-12">
                            <select name="form[accountant_id]" class="form-control section accountant_id" style="min-width: 750px" required>
                                <?php
                                    $haveDirector = empty($this->data['form']['accountant_id']) ? 'selected' : '';
                                    echo "<option value='' disabled $haveDirector >Выберите бухгалтера из списка сотрудников в системе</option>";
                                ?>
                                <?php
                                    foreach ($this->data['users'] as $user) {
                                        $userId = $user['ID'];
                                        $userFullName = trim(htmlspecialchars($user['USER'])); // ФИО бухгалтера
                                        $userWorkPosition = trim(htmlspecialchars($user['WORK_POSITION'])); // Должность бухгалтера
                                        $userShortName = trim(htmlspecialchars($user['SHORT_NAME'])); // Короткая запись имени бухгалтера

                                        $replacementUser = trim(htmlspecialchars($user['REPLACEMENT_SHORT_NAME'])) ?? NULL;
                                        $dataReplacement = ($replacementUser !== NULL && !empty($replacementUser)) ? 'data-replacement="' . htmlspecialchars($replacementUser) . '"' : '';

                                        $selected = ($userId == $this->data['form']['accountant_id']) ? 'selected' : '';

                                        echo '<option value="' . $userId . '" 
                                        data-accountant_position="' . $userWorkPosition . '" 
                                        data-accountant="' . $userShortName . '" ' . $dataReplacement . ' ' . $selected . '>';
                                        echo $userFullName;
                                        echo '</option>';
                                    }
                                ?>
                            </select>
                            <label class="col-form-label" style="font-weight: 500;">: исполняет роль главного бухгалтера</label>
                        </div>
                    </legend>

                    <div class="form-group row align-items-center mt-0">
                        <label class="col-sm-2 col-form-label">Бухгалтерская должность</label>
                        <div class="col-sm-8">
                            <input type="text" name="form[accountant_position]" class="form-control" required
                                   value="<?= $this->data['form']['accountant_position'] ?? '' ?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">Имя бухгалтера</label>
                        <div class="col-sm-8">
                            <input type="text" name="form[accountant]" class="form-control" required
                                   value="<?= $this->data['form']['accountant'] ?? '' ?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                </fieldset>
            </div>
        </div>

        <!-- Руководитель ИЦ -->
        <div class="panel panel-default mt-2">
            <header class="panel-heading">
                Руководитель ИЦ
                <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
            </header>
            <div class="panel-body">
                <fieldset class="border rounded-3 p-3 mt-0">
                    <legend class="float-none w-auto px-3">
                        <div class="col-sm-12">
                            <select name="form[head_ic_id]" class="form-control section head_ic" style="min-width: 750px" required>
                                <?php
                                    $haveDirector = empty($this->data['form']['head_ic_id']) ? 'selected' : '';
                                    echo "<option value='' disabled $haveDirector >Выберите руководителя ИЦ из списка сотрудников в системе</option>";
                                ?>
                                <?php
                                    foreach ($this->data['users'] as $user) {
                                        $userId = $user['ID'];
                                        $userFullName = trim(htmlspecialchars($user['USER'])); // ФИО бухгалтера
                                        $userWorkPosition = trim(htmlspecialchars($user['WORK_POSITION'])); // Должность бухгалтера
                                        $userShortName = trim(htmlspecialchars($user['SHORT_NAME'])); // Короткая запись имени бухгалтера

                                        $replacementUser = trim(htmlspecialchars($user['REPLACEMENT_SHORT_NAME'])) ?? NULL;
                                        $dataReplacement = ($replacementUser !== NULL && !empty($replacementUser)) ? 'data-replacement="' . htmlspecialchars($replacementUser) . '"' : '';

                                        $selected = ($userId == $this->data['form']['head_ic_id']) ? 'selected' : '';

                                        echo '<option value="' . $userId . '" 
                                        data-head-ic_position="' . $userWorkPosition . '" 
                                        data-head-ic="' . $userShortName . '" ' . $dataReplacement . ' ' . $selected . '>';
                                        echo $userFullName;
                                        echo '</option>';
                                    }
                                ?>
                            </select>
                            <label class="col-form-label" style="font-weight: 500;">: исполняет роль руководителя ИЦ</label>
                        </div>
                    </legend>

                    <div class="form-group row align-items-center mt-0">
                        <label class="col-sm-2 col-form-label">Руководитель ИЦ должность</label>
                        <div class="col-sm-8">
                            <input type="text" name="form[head_ic_position]" class="form-control" required
                                   value="<?= $this->data['form']['head_ic_position'] ?? '' ?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                    <div class="form-group row align-items-center">
                        <label class="col-sm-2 col-form-label">Имя руководителя ИЦ</label>
                        <div class="col-sm-8">
                            <input type="text" name="form[head_ic]" class="form-control" required
                                   value="<?= $this->data['form']['head_ic'] ?? '' ?>">
                        </div>
                        <div class="col-sm-2"></div>
                    </div>

                </fieldset>
            </div>
        </div>

    </form>

    <button form="form_emp" class="btn btn-primary block-after-click mb-0" type="submit" name="save">
        Сохранить
    </button>

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->
</div>