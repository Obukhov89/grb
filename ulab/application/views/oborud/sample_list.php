<header class="mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/oborud/sampleCard/" title="Новый образец контроля">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link popup-help" href="<?=URI?>/help/LIMS_Manual_Stand/VLK/Sample_list/Sample_list.html" title="ПОМОГИТЕ">
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
                <option value="all" selected="">Все статусы</option>
                <option value="unactual">Не актуальны</option>
                <option value="unlimited_expiry">Срок годности не ограничен</option>
            </select>
        </div>

        <div class="col">
            <select id="selectLab" class="form-control filter filter-lab">
                <option value="0">Все лаборатории</option>
                <option value="-1">Вне лабораторий</option>
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

<table id="sampleList" class="table table-striped text-center journal w-100">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-nowrap"></th>
        <th scope="col" class="text-nowrap">Наименование</th>
        <th scope="col" class="text-nowrap">Номер</th>
        <th scope="col" class="text-nowrap">Дата выпуска</th>
        <th scope="col" class="text-nowrap">Годен до</th>
        <th scope="col">Метрологические характеристики</th>
        <th scope="col">История</th>
    </tr>
    <tr class="header-search">
        <th scope="col"></th>
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
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div id="history-modal-form" class="bg-light mfp-hide col-md-5 m-auto p-3 position-relative">
    <div class="title mb-3 h-2"></div>

    <div class="line-dashed-small"></div>

    <div class="history-info"></div>
</div>