<style>
    .ms-container{
        width: 100%!important;
    }
    .bg-blue{
        background-color: #08c!important;
    }
</style>

<div class="department-wrapper import">
    <header class="header-requirement mb-4 pt-0">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-1">
                    <a class="nav-link" href="<?= URI ?>/import/" title="Вернуться к началу работы с U-LAB">
                        <i class="fa-solid fa-arrow-left"></i>
                    </a>
                </li>
                <?php if ($this->data['is_may_change']): ?>
                    <li class="nav-item me-1">
                        <a class="nav-link popup-with-form" href="#" title="Добавить подразделение">
                            <i class="fa-solid fa-plus"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <div class="panel panel-default">
        <header class="panel-heading">
            Структура компании
            <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
        </header>
        <div class="panel-body">
            <div class="row">
                <div class="col-6" id="flatTreeWrapper">
                    <div id="FlatTree" class="tree tree-plus-minus">
                        <div class="tree-folder" style="display:none;">
                            <div class="tree-folder-header">
                                <i class="fa fa-folder"></i>
                                <div class="tree-folder-name"></div>
                            </div>
                            <div class="tree-folder-content"></div>
                            <div class="tree-loader" style="display:none"></div>
                        </div>
                        <div class="tree-item" style="display:none;">
                            <i class="tree-dot"></i>
                            <div class="tree-item-name"></div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="structure d-none">
                        <div class="info-wrapper border p-2 mb-4">
                            <div class="form-group row mb-1">
                                <div class="col">
                                    <label for="department" class="form-label mb-0 p-0 signature d-block">Подразделение </label>
                                    <div id="department" class="d-inline-block"></div>
                                </div>
                                <div class="col">
                                    <label for="heads" class="form-label mb-0 p-0 signature">Руководитель</label>
                                    <div id="heads"></div>
                                </div>
                                <div class="col-auto">
                                    <?php if ($this->data['is_may_change']): ?>
                                        <button id="updateDepartment" class="btn btn-success btn-square me-1"
                                                title="Редактировать подразделение">
                                            <i class="fa-solid fa-pencil icon-fix"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-4 users-wrapper">
                            <div class="col">
                                <select name="users" class="multi-select" multiple="" id="users">
                                    <?php foreach ($this->data['users'] as $user): ?>
                                        <option value="<?= $user['ID'] ?>">
                                            <?= $user['NAME'] ?: '' ?> <?= $user['LAST_NAME'] ?: '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="hidden_users" id="hiddenUsers">

                        <?php if ($this->data['is_may_change']): ?>
                            <button class="btn btn-primary preservation d-none" type="button" disabled>
                                <div class="spinner-border spinner-border-sm d-inline-block" role="status"
                                     aria-hidden="true"></div>
                                Сохранение...
                            </button>

                            <button type="button" class="btn btn-primary user-to-department">Сохранить сотрудников
                            </button>
                        <? endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div id="department-modal-form" class="bg-light mfp-hide col-md-5 m-auto p-3 position-relative">
        <div class="title mb-3 h-2">
            Подразделение
        </div>

        <div class="line-dashed-small"></div>

        <input type="hidden" id="departmentId" name="department_id" value="">

        <div class="mb-3">
            <label for="name" class="form-label mb-1">Название подразделения</label>
            <input type="text" name="NAME" class="form-control" id="name"
                   value="" required>
            <div id="nameHelp" class="form-text text-danger"></div>
        </div>

        <div class="mb-3">
            <label for="parent" class="form-label mb-1">Вышестоящее подразделение</label>
            <select class="form-select parent w-100 mw-100" id="parent" name="PARENT" required>
                <?php foreach ($this->data['departments'] as $key => $department): ?>
                    <option value="<?= $key ?>"><?= $department['NAME'] ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="head" class="form-label mb-1">Руководитель</label>
            <input id="head" class="form-control" name="UF_HEAD" list="head_list" type="text"
                   value="" autocomplete="off" required>
            <input type="hidden" name="head_id" id="head-hidden" value="<?= $this->data['head_id'] ?? '' ?>">
            <datalist id="head_list">
                <?php if (isset($this->data['users'])): ?>
                    <?php foreach ($this->data['users'] as $user): ?>
                        <option data-value="<?= $user['ID'] ?>"><?= $user['USER'] ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </datalist>
        </div>

        <div class="line-dashed-small"></div>

        <button class="btn btn-primary preservation-department d-none" type="button" disabled>
            <div class="spinner-border spinner-border-sm d-inline-block" role="status"
                 aria-hidden="true"></div>
            Сохранение...
        </button>

        <button type="button" class="btn btn-primary save-department">Сохранить</button>
    </div>
    <!--./department-modal-form-->

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->
</div>