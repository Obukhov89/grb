<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/request/list/" title="Вернуться к списку">
                    <svg class="icon" width="20" height="20">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#list"/>
                    </svg>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/request/card/<?=$this->data['deal_id']?>" title="Вернуться в карточку">
                    <svg class="icon" width="20" height="20">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#card"/>
                    </svg>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link"
                   href="/protocol_generator/label_probe_all.php?ID=<?=$this->data['deal_id']?>&TZ_ID=<?=$this->data['tz_id']?><?= ($this->data['deal_id'] == 15554 || $this->data['deal_id'] == 16166 || $this->data['deal_id'] == 17409) ? '&ACT_ID=' . $this->data['act_id'] : ''?>"
                   title="Скачать этикетки на все пробы">
                    <svg class="icon" width="20" height="20">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#download"/>
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</header>

<h2 class="d-flex mb-3">
    Заявка <?= $this->data['deal_title'] ?? '' ?>
</h2>

<div class="panel panel-default">
    <header class="panel-heading">
        Данные акта
        <span class="tools float-end">
            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
            <a href="#" class="fa fa-chevron-up"></a>
        </span>
    </header>
    <div class="panel-body">
        <div class="wrapper-add-info mt-2 flex-column">
            <div class="row mb-2">
                <div class="col-4">
                    <div>Номер акта</div>
                    <div><strong><?= htmlspecialchars($this->data['act_number']) ?></strong></div>
                </div>

                <div class="col-4">
                    <div>Проба отобрана не заказчиком</div>
                    <div>
                        <label class="switch">
                            <input class="form-check-input selection-type"
                                   data-id="<?=$this->data['deal_id']?>"
                                   name="act[SELECTION_TYPE]"
                                   type="checkbox"
                                   value="1"
                                <?=$this->data['selection_type'] ? 'checked' : ''?>>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <div class="col-4">
                    <div>Дата поступления проб в ИЦ (дата доставки проб)</div>
                    <div><strong><?= htmlspecialchars($this->data['act_date']) ?></strong></div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php
// Кол-во колонок: 6 базовых + 3 доп. = 9 + условные
$colspan = 10;
if ((int)$this->data['user_id'] === 156) { $colspan++; }
if ((int)$this->data['user_id'] === 61)  { $colspan++; }
?>

<div class="panel panel-default">
    <header class="panel-heading">
        Информация о пробах (образцах)
        <span class="tools float-end">
            <a href="javascript:;" class="fa fa-star-of-life bg-transparent text-danger d-none"></a>
            <a href="#" class="fa fa-chevron-up"></a>
        </span>
    </header>

    <div class="panel-body">

        <!-- ВАЖНО: action укажите на ваш контроллерный метод,
             который сохранит: cipher/date_probe/place/quarry_id по id пробы -->
        <form id="act-probes-form" action="<?=URI?>/probe/saveActChanges/" method="post"
              data-user-id="<?=(int)$_SESSION['SESS_AUTH']['USER_ID']?>"
              data-act-num="<?= htmlspecialchars($this->data['act_number']) ?>"
              data-act-date="<?= htmlspecialchars($this->data['act_date']) ?>">

            <input type="hidden" id="deal_id" name="deal_id" value="<?=$this->data['deal_id']?>">
            <input type="hidden" name="tz_id" value="<?=$this->data['tz_id']?>">

            <div class="d-flex justify-content-between align-items-center mb-2 gap-2 flex-wrap">
                <button type="button" class="btn btn-outline-secondary" id="toggle-extra-cols">
                    Дата/Место(свернуть/развернуть)
                </button>
                <div class="form-group col-sm-2">
                    <label class="form-label mb-1" for="protocolDeadline">Место отбора (общее)</label>
                    <input type="text" class="form-control bg-white w-100" id="place_all" name="act[place_all]">
                </div>
                <div class="form-group col-sm-2">
                    <label class="form-label mb-1" for="protocolDeadline">Дата отбора (общее)</label>
                    <input type="date" class="form-control bg-white w-100" id="date_all" name="act[date_all]">
                </div>
<!--                <div class="form-group col-sm-2">-->
<!--                    <label class="form-label mb-1">Карьер</label>-->
<!--                    <div class="input-group">-->
<!--                        <select class="form-control select2" id="quarry_all" name="act[quarry_all]">-->
<!--                            <option value="">Нет карьера</option>-->
<!--                            --><?php //foreach($this->data['quarry'] as $quarry): ?>
<!--                                <option value="--><?php //=$quarry['ID']?><!--">--><?php //=$quarry['NAME']?><!--</option>-->
<!--                            --><?php //endforeach; ?>
<!--                        </select>-->
<!--                    </div>-->
<!--                </div>-->
                <button type="submit" class="btn btn-primary">
                    Сохранить изменения
                </button>
            </div>
            <table id="journal_material_2" class="table table-striped journal table-hover table-sm table-light">
                <thead>
                <tr>
                    <th scope="col">
                        <div class="form-check">
                            <input class="form-check-input material-check-all"
                                   type="checkbox"
                                   id="material_name_all">
                            <label class="form-check-label" for="material_name_all">
                                Шифр пробы
                            </label>
                        </div>
                    </th>
                    <th scope="col">Группа</th>
                    <th scope="col">Маркировка заказчика (при наличии)</th>
                    <th scope="col" class="js-extra-col d-none">Место отбора</th>
                    <th scope="col" class="js-extra-col d-none">Дата отбора</th>
                    <th scope="col" class="js-extra-col d-none">Карьер</th>
                    <th scope="col">Лаборатория</th>
                    <th scope="col">История</th>
                    <th scope="col">Статус нахождения</th>
                    <th scope="col">Управление</th>
                    <?php if ((int)$this->data['user_id'] === 156): ?>
                        <th scope="col">Готовность пробы</th>
                    <?php endif; ?>
                    <?php if ((int)$this->data['user_id'] === 61): ?>
                        <th scope="col">Невозвратность пробы</th>
                    <?php endif; ?>
                </tr>
                <tr class="header-search">
                    <th scope="col"><input type="text" class="form-control search"></th>
                    <th scope="col"><input type="text" class="form-control search"></th>
                    <th scope="col"><input type="text" class="form-control search"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <?php if ((int)$this->data['user_id'] === 156): ?>
                        <th scope="col"></th>
                    <?php endif; ?>
                    <?php if ((int)$this->data['user_id'] === 61): ?>
                        <th scope="col"></th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </form>
    </div>
</div>

<form id="edit-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/probe/editProbeInfo/" method="post">
    <div class="title mb-3 h-2">Редактирование пробы</div>
    <div class="line-dashed-small"></div>

    <input type="hidden" name="id" class="probe_id" value="">

    <div class="mb-3">
        <label class="form-label">Маркировка заказчика (информация об объекте испытания)</label>
        <input type="text" name="form[name_for_protocol]" class="form-control name_for_protocol" value="">
    </div>

    <div class="mb-3">
        <input type="checkbox" name="" class="form-check-input" value="">
        <label class="form-label">Выводить только маркировку заказчика</label>
    </div>

    <div class="mb-3">
        <label class="form-label">Место отбора</label>
        <input type="text" name="form[place]" class="form-control probe_place" value="">
    </div>

    <div class="mb-3">
        <label class="form-label">Дата отбора</label>
        <input type="date" name="form[date_probe]" class="form-control probe_date" value="">
    </div>

    <div class="mb-3">
        <label class="form-label">Карьер</label>
        <select name="form[quarry_id]" class="form-control probe_quarry_id">
            <?php foreach($this->data['quarry'] as $value): ?>
                <option value='<?=$value['ID']?>'><?=htmlspecialchars($value['NAME'])?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="line-dashed-small"></div>
    <button type="submit" class="btn btn-primary">Сохранить</button>
</form>

<div id="history-modal-form" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2">
        История пробы <span class="cipher"></span>
    </div>
    <div class="line-dashed-small"></div>
    <div class="history-info"></div>
</div>