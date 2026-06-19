<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/gost/new/" title="Новый ГОСТ">
                    <i class="fa-solid fa-plus"></i>
                </a>
            </li>

            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/gost/listGost/" title="Журнал ГОСТов">
                    <i class="fa-solid fa-list"></i>
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


<table id="journal_gost" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col"></th>
        <th scope="col" class="text-nowrap">№ ОА</th>
        <th scope="col" class="text-nowrap">Номер документа</th>
        <th scope="col" class="text-nowrap">Наименование документа</th>
        <th scope="col" class="text-nowrap">Год</th>
        <th scope="col" class="text-nowrap">Наименование объекта</th>
        <th scope="col">Определяемая характеристика / показатель</th>
        <th scope="col" class="text-nowrap">Пункт документа</th>
        <th scope="col" class="text-nowrap">Метод</th>
        <th scope="col" class="text-nowrap">Единица измерения</th>
        <th scope="col" class="text-nowrap">В ОА</th>
        <th scope="col" class="text-nowrap">РОА</th>
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
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <select class="form-control search">
                <option value=""></option>
                <option value="1">В ОА</option>
                <option value="0">Не в ОА</option>
            </select>
        </th>
        <th scope="col">
            <select class="form-control search">
                <option value=""></option>
                <option value="1">РОА</option>
                <option value="0">Не РОА</option>
            </select>
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
