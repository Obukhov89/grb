<style>
    .ms-container{
        width: 100%!important;
    }
    .header-menu .nav-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="oborud-to-method-wrapper import">
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
                    <a class="nav-link fa-solid icon-nav fa-link disabled" href="<?= URI ?>/import/oborudToMethod/" style="font-size: 22px;" title="Привязка оборудования к методикам" data-bs-toggle="tooltip">
                    </a>
                </li>

                <li class="nav-item me-5">
                    <a class="nav-link fa-solid icon-nav fa-link" href="<?= URI ?>/import/labUserToMethod/" style="font-size: 22px;" title="Привязка отделов и сотрудников к методикам" data-bs-toggle="tooltip">
                    </a>
                </li>

                <li class="nav-item ms-auto">
                    <div class="col">
                        <a class="btn btn-gradient btn-oborud-to-method btn-oborud-to-method-link disabled" href="/ulab/gost/method/<?= $this->data['method_id'] ?>">Перейти в методику</a>
                    </div>
                </li>
                <li class="nav-item ms-2">
                    <div class="col">
                        <button form="formOborudToMethod" class="btn btn-gradient btn-oborud-to-method disabled" type="submit" name="save">
                            Сохранить
                        </button>
                    </div>
                </li>

            </ul>
        </nav>
    </header>

    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-default">
                <header class="panel-heading">
                    Оборудования и методики
                    <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="#" class="fa fa-chevron-up"></a>
                         </span>
                </header>
                <div class="panel-body">
                    <form class="form-horizontal" id="formOborudToMethod" method="post"
                          action="<?= URI ?>/import/insertOborudToMethod/">
                        <div class="form-group row mb-4">
                            <div class="col">
                                <div class="signature">ГОСТ</div>
                                <select class="form-select select2 gosts" id="gosts" name="gost" required>
                                    <option value="">Выберите ГОСТ</option>
                                    <?php foreach ($this->data['gosts'] as $gost): ?>
                                        <option value="<?= $gost['id'] ?>" <?= $this->data['gost_id'] === $gost['id'] ? 'selected' : '' ?>><?= $gost['reg_doc'] ?> <?= $gost['description'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-4">
                            <div class="col">
                                <div class="signature">Методики</div>
                                <select class="form-select select2 methods" name="method" required>
                                    <?php foreach ($this->data['methods'] as $method): ?>
                                        <option value="<?= $method['id'] ?>" <?= $this->data['method_id'] === $method['id'] ? 'selected' : '' ?>><?= $method['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-4 oborud-wrapper <?= empty($this->data['method_id']) ? 'd-none' : '' ?>">
                            <div class="col">
                                <select name="oborud" class="multi-select" multiple="" id="oborud">
                                    <?php foreach ($this->data['oboruds'] as $oborud): ?>
                                        <option value="<?= $oborud['eq_g_id'] ?>">
                                            <?= $oborud['name'] ?: '' ?>, инв.
                                            номер <?= $oborud['inventory_number'] ?: '' ?>
                                            <?=trim($oborud['TYPE_OBORUD']) != '' ? '(' . mb_strtolower($oborud['TYPE_OBORUD']) . ')' : ''?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="hidden_oborud" id="hiddenOborud">
                    </form>
                </div>
                <!--./panel-body-->
            </div>
        </div>
    </div>
    <!--./row-->

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>
    <!--./alert_modal-->
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3" style="display: none" id="ajax-loading-message">
    <div class="toast show" style="width: 400px;">
        <div class="toast-header">
            <strong class="me-auto">Пожалуйста, подождите, данные загружаются...</strong>
        </div>
    </div>
</div>