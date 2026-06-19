<div class="filters mb-4">
    <div class="row">
        <div class="col">
            <input type="date" id="inputDateStart" class="form-control filter filter-date-start" value="2024-01-01">
        </div>

        <div class="col">
            <input type="date" id="inputDateEnd" class="form-control filter filter-date-end" value="2025-01-01">
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
        <th scope="col" class="text-nowrap">Дата выдачи ТЗ</th>
        <th scope="col" class="text-nowrap">Дата сдачи работ</th>
        <th scope="col" class="">Номер заявки</th>
        <th scope="col" class="">Номер протокола</th>
        <th scope="col" class="text-nowrap">Контрагент</th>
        <th scope="col" class="text-nowrap">Лаборатория</th>
        <th scope="col" class="text-nowrap">ГОСТ</th>
        <th scope="col" class="text-nowrap">Вид испытаний</th>
        <th scope="col" class="">Трудозатраты, чел./час (на 1 испытание)</th>
        <th scope="col" class="">Количество испытаний, шт</th>
        <th scope="col" class="">Стоимость испытаний, руб</th>
    </tr>
    <tr class="header-search">
        <th scope="col"></th>
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
        <th scope="col"></th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
