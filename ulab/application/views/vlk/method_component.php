<header class="mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link add-st-component" href="#" title="Назначить образец контроля c метрологической характеристикой для методики">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/oborud/sampleList/" title="Список образецов контроля">
                    <svg class="icon" width="25" height="25">
                        <use xlink:href="<?=URI?>/assets/images/icons.svg#card"/>
                    </svg>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link popup-help" href="/ulab/help/LIMS_Manual_Stand/VLK/Method_component_list/Method_component_list.html" title="ПОМОГИТЕ">
                    <i class="fa-solid fa-question"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<div class="filters mb-4">
    <div class="row">
        <div class="col">
            <select id="selectStage" class="form-control filter filter-stage">
                <option value="2">В ОА</option>
                <option value="3">РОА</option>
                <option value="5">Вне ОА</option>
                <option value="1">Актуальные</option>
                <option value="7">Не актуальные</option>
            </select>
        </div>

        <div class="col">
            <select id="selectLab" class="form-control filter filter-lab">
                <option value="0">Все лаборатории</option>
                <?php foreach ($this->data['lab'] as $item): ?>
                    <?php if ($item['id'] < 100): ?>
                        <option value="<?=$item['id']?>" style="font-weight:bold"><?=$item['name']?></option>
                    <?php else: ?>
                        <option value="<?=$item['id']?>"> -- <?=$item['name']?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
        </div>
    </div>
</div>

<table id="methodComponentList" class="table cell-border journal text-center w-100">
    <thead>
    <tr class="table-light">
        <th scope="col"></th>
        <th scope="col" class="text-nowrap">Определяемая характеристика / показатель</th>
        <th scope="col" class="text-nowrap">Пункт документа</th>
        <th scope="col" class="text-nowrap">Номер документа</th>
    </tr>
    <tr class="header-search">
        <th scope="col"></th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col"></th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div id="history-modal-form" class="bg-light mfp-hide col-md-6 m-auto p-3 position-relative">
    <div class="title mb-3 h-2"></div>

    <div class="line-dashed-small"></div>

    <div class="history-info"></div>
</div>

<form id="methodComponentModalForm" class="bg-light mfp-hide col-md-6 m-auto p-3 position-relative"
      action="/ulab/vlk/insertMethodComponent/" method="post">

    <div class="title mb-3 h-2">
        Данные
    </div>

    <div class="line-dashed-small"></div>

    <div class="mb-3">
        <label class="form-label" for="selectMethods">Методика</label>
        <select id="selectMethods" class="form-control" name="form[method_id]" required>
            <option value="" disabled selected>Выберите методику</option>
            <?php if (isset($this->data['methods'])): ?>
                <?php foreach ($this->data['methods'] as $method): ?>
                    <option value="<?=$method['id']?>"><?=$method['view_gost']?></option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label" for="selectComponents">Образец контроля с метрологической характеристикой</label>
        <select name="form[component_id]" id="selectComponents" class="form-control" required>
            <option value="" disabled selected>Выберите образец контроля с метрологической характеристикой</option>
            <?php foreach ($this->data['components'] as $item): ?>
                <option value="<?= $item['id'] ?>"><?= $item['ss_name'] ?? '-' ?> | <?= $item['ss_number'] ?? '-' ?> |
                    <?= $item['name'] ?? '-' ?> <?= $item['certified_value'] ?? '-' ?> <?= $item['udc_unit_rus'] ?? '-' ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Сохранить <i class="fa-solid fa-arrows-rotate"></i></button>
</form>
<!--./condition-modal-form-->

<div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
    <div class="title mb-3 h-2 alert-title"></div>

    <div class="line-dashed-small"></div>

    <div class="mb-3 alert-content"></div>
</div>
<!--./alert_modal-->