<div class="filters mb-4">
    <div class="row">
        <div class="col">
            <input type="date" id="inputDateStart" class="form-control filter filter-date-start" value="2022-12-15" placeholder="Введите дату начала:">
        </div>

        <div class="col">
            <input type="date" id="inputDateEnd" class="form-control filter filter-date-end" value="<?= date('Y-m-d') ?>" placeholder="Введите дату окончания:">
        </div>

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
                <?php foreach ($this->data['lab_list'] as $item): ?>
                    <option value="<?=$item['ID']?>"><?=$item['NAME']?></option>
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
        <th scope="col" class="text-nowrap">Номер документа</th>
        <th scope="col" class="text-nowrap">Пункт документа</th>
<!--        <th scope="col" class="text-nowrap">Наименование документа</th>-->
        <th scope="col" class="text-nowrap">Наименование объекта</th>
        <th scope="col">Определяемая характеристика / показатель</th>
        <th scope="col" class="">Кол-во использований</th>
        <th scope="col" class="">Количество ВЛК</th>
        <th scope="col" class="text-nowrap">В ОА</th>
    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
<!--        <th scope="col">-->
<!--            <input type="text" class="form-control search">-->
<!--        </th>-->
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col">
<!--            <select class="form-control search">-->
<!--                <option value="">Все</option>-->
<!--                <option value="1">В ОА</option>-->
<!--                <option value="0">Не в ОА</option>-->
<!--            </select>-->
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
