<style>
    .ms-container .ms-selection{
        float: right;
        margin-left: 157px;
    }
    .ms-container {
        display: flex;
        justify-content: space-between;
        width: unset!important;
    }
    .header-menu .nav-item {
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>

<div class="oborud-to-room-wrapper import">
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
                    <a class="nav-link icon-nav fa-solid fa-list" href="<?=URI?>/oborud/list/" style="font-size: 22px;" title="Список оборудования" data-bs-toggle="tooltip">
                    </a>
                </li>
                <li class="nav-item me-2">
                    <a class="nav-link icon-nav fa-solid fa-file-import" href="<?=URI?>/import/oborud/" style="font-size: 22px;" title="Импорт оборудования" data-bs-toggle="tooltip">
                    </a>
                </li>
                <li class="nav-item me-5">
                    <a class="nav-link icon-nav fa-solid fa-link disabled" href="<?=URI?>/import/oborudToRoom/" style="font-size: 22px;" title="Привязка оборудования к помещениям" data-bs-toggle="tooltip">
                    </a>
                </li>
                <li class="nav-item ms-auto">
                    <div class="col">
                        <button form="formOborudToRoom" class="btn btn-gradient btn-oborud-to-room disabled" type="submit" name="save">
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
                    Оборудования и помещения
                    <span class="tools float-end">
                            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
                            <a href="#" class="fa fa-chevron-up"></a>
                         </span>
                </header>
                <div class="panel-body">
                    <form class="form-horizontal" id="formOborudToRoom" method="post"
                          action="<?= URI ?>/import/insertOborudToRoom/">
                        <div class="form-group row mb-4">
                            <div class="col">
                                <div class="signature">Помещения</div>
                                <select class="form-select room" name="room" required>
                                    <option value="">Выберите помещение</option>
                                    <?php foreach ($this->data['rooms'] as $room): ?>
                                        <option value="<?= $room['ID'] ?>" <?= $this->data['room_id'] === $room['ID'] ? 'selected' : '' ?>><?= $room['name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row mb-4 oborud-wrapper <?= empty($this->data['room_id']) ? 'd-none' : '' ?>">
                            <div class="col">
                                <select name="oborud" class="multi-select" multiple="" id="oborud">
                                    <?php foreach ($this->data['oboruds'] as $oborud): ?>
                                        <option value="<?= $oborud['id'] ?>">
                                            <?= (string)$oborud['name'] ?: '' ?>
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