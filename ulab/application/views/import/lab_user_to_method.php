<style>
    #workarea input.form-control, #workarea select.form-control, .mfp-content .form-control {
        min-width: auto;
    }
    .header-menu .nav-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="lab-user-to-method import">
    <header class="header-requirement mb-4 pt-0">
        <nav class="header-menu w-100">
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
                    <a class="nav-link fa-solid icon-nav fa-list" href="<?= URI ?>/gost/list/" style="font-size: 22px;" title="Список областей аккредитации" data-bs-toggle="tooltip">
                    </a>
                </li>

                <li class="nav-item me-2">
                    <a class="nav-link fa-solid icon-nav fa-file-import" href="<?= URI ?>/import/methods/" style="font-size: 22px;" title="Импорт областей аккредитации и методик" data-bs-toggle="tooltip">
                    </a>
                </li>

                <li class="nav-item me-2">
                    <a class="nav-link fa-solid icon-nav fa-link" href="<?= URI ?>/import/oborudToMethod/" style="font-size: 22px;" title="Привязка оборудования к методикам" data-bs-toggle="tooltip">
                    </a>
                </li>

                <li class="nav-item me-5">
                    <a class="nav-link fa-solid icon-nav fa-link disabled" href="<?= URI ?>/import/labUserToMethod/" style="font-size: 22px;" title="Привязка отделов и сотрудников к методикам" data-bs-toggle="tooltip">
                    </a>
                </li>

                <li class="nav-item ms-auto">
                    <div class="col">
                        <a class="btn btn-gradient btn-oborud-to-method btn-lab-to-users-link disabled" href="/ulab/gost/method/<?= $this->data['method_id'] ?>">Перейти в методику</a>
                    </div>
                </li>
                <li class="nav-item ms-2">
                    <div class="col">
                        <button form="formLabUserToMethod" class="btn btn-gradient btn-lab-to-users disabled" type="submit" name="save">
                            Сохранить
                        </button>
                    </div>
                </li>

            </ul>
        </nav>
    </header>

    <form class="form-horizontal" id="formLabUserToMethod" method="post"
          action="<?= URI ?>/import/addLabUserToMethod/">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <header class="panel-heading">
                        ГОСТы и методики
                        <span class="tools float-end">
                                <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                                <a href="#" class="fa fa-chevron-up"></a>
                             </span>
                    </header>
                    <div class="panel-body">
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">ГОСТ</label>
                            <div class="col-sm-8">
                                <select class="form-select select2 gosts" id="gosts" name="gost" required>
                                    <option value="">Выберите ГОСТ</option>
                                    <?php foreach ($this->data['gosts'] as $gost): ?>
                                        <option value="<?= $gost['id'] ?>" <?= $this->data['gost_id'] === $gost['id'] ? 'selected' : '' ?>><?= $gost['reg_doc'] ?> <?= $gost['description'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-2"></div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Методики</label>
                            <div class="col-sm-8">
                                <select class="form-select select2 methods" name="method" required>
                                    <?php foreach ($this->data['methods'] as $method): ?>
                                        <option value="<?= $method['id'] ?>" <?= $this->data['method_id'] === $method['id'] ? 'selected' : '' ?>><?= $method['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-sm-2"></div>
                        </div>
                    </div>
                    <!--./panel-body-->
                </div>
                <!--./panel-default-->
            </div>
            <!--./col-md-12-->
        </div>
        <!--./row-->

        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <header class="panel-heading">
                        Отделы и сотрудники
                        <span class="tools float-end"><a href="#" class="fa fa-chevron-up"></a></span>
                    </header>
                    <div class="panel-body">
                        <div class="message <?= empty($this->data['method_id']) ? '' : 'd-none' ?>">
                            Выберите ГОСТ и Методику, чтобы добавить отделы и сотрудников
                        </div>
                        <div class="lab-user-wrapper <?= empty($this->data['method_id']) ? 'd-none' : '' ?>">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Отделы</label>
                                <div class="col-sm-8">
                                    <select id="select-lab" class="form-control select2" name="form[lab][]"
                                            multiple="multiple">
                                <?php foreach ($this->data['lab_list'] as $item) {
                                    $isSelected = in_array($item['ID'], $this->data['lab'] ?? []) ? 'selected' : '';
                                    ?>
                                    <option value="<?= $item['ID'] ?>" <?= $isSelected ?>><?= $item['NAME'] ?></option>
                                <?php } ?>

                                    </select>
                                </div>
                                <div class="col-sm-2"></div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Помещения</label>
                                <div class="col-sm-8">
                                    <select id="select-room" class="form-control select2" name="form[room][]"
                                            multiple="multiple">
                                        <?php if (empty($this->data['room_list'])): ?>
                                            <option value="" disabled>Сначала выберите отделы</option>
                                        <?php endif; ?>

                                        <?php foreach ($this->data['room_list'] as $item): ?>
                                            <option value="<?= $item['ID'] ?>" <?= in_array($item['ID'], $this->data['room'] ?? []) ? 'selected' : '' ?>><?= $item['name'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-2"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">Ответственные</label>
                                <div class="col-sm-8">
                                    <select id="select-assigned" class="form-control select2" name="form[assigned][]"
                                            multiple="multiple">
                                        <?php if (empty($this->data['assigned_list'])): ?>
                                            <option value="" disabled>Сначала выберите отделы</option>
                                        <?php endif; ?>

                                        <?php foreach ($this->data['assigned_list'] as $item): ?>
                                            <option value="<?= $item['ID'] ?>" <?= in_array($item['ID'], $this->data['assigned'] ?? []) ? 'selected' : '' ?>><?= $item['LAST_NAME'] ?> <?= $item['NAME'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-sm-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!--./col-md-12-->
        </div>
        <!--./row-->
    </form>

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->

    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="display: none" id="ajax-loading-message">
        <div class="toast show" style="width: 400px;">
            <div class="toast-header">
                <strong class="me-auto">Пожалуйста, подождите, данные загружаются...</strong>
            </div>
        </div>
    </div>
</div>