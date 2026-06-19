<div class="filters mb-4">
    <div class="row">
<!--        <div class="col-auto filter-search" id="filter_search">-->
<!--            <button class="filter-btn-search">-->
<!--                <svg class="bi" width="20" height="20">-->
<!--                    <use xlink:href="--><?//=URI?><!--/assets/images/icons.svg#icon-search"/>-->
<!--                </svg>-->
<!--            </button>-->
<!--            <div id="journal_filter" class="dataTables_filter">-->
<!--                <label>-->
<!--                    <input id="filter_everywhere" type="search" class="form-control filter" placeholder="Поиск..." aria-controls="journal_requests">-->
<!--                </label>-->
<!--            </div>-->
<!--        </div>-->
        <div class="col">
            <input type="date" id="inputDateStart" class="form-control filter filter-date-start" value="<?= $this->data['date_start'] ?? '' ?>" placeholder="Введите дату начала:">
        </div>

        <div class="col">
            <input type="date" id="inputDateEnd" class="form-control filter filter-date-end" value="<?= date('Y-m-d') ?>" placeholder="Введите дату окончания:">
        </div>

        <div class="col">
            <select id="selectStage" class="form-control filter filter-stage">
                <option value="all" selected>Все счета</option>
                <option value="0">Счет не оплачен</option>
                <option value="1">Счет оплачен не полностью</option>
                <option value="2">Счет оплачен полностью</option>
<!--                <option value="3">УПД сформирован и не отправлен</option>-->
            </select>
        </div>

        <div class="col">
            <select id="selectLab" class="form-control filter filter-lab">
                <option value='0' selected>Bсе лаборатории</option>
                <?php if ($this->data['lab']): ?>
                    <?php foreach ($this->data['lab'] as $lab): ?>
                        <option value="<?= $lab['DEPARTMENT'] ?? '' ?>"><?= $lab['NAME'] ?? '' ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
        </div>
    </div>
</div>

<table id="journal_invoice" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-nowrap"></th>
        <th scope="col" class="text-nowrap">№</th>
        <th scope="col" class="text-nowrap">Дата</th>
        <th scope="col" class="text-nowrap">Сумма</th>
        <th scope="col" class="text-nowrap">Контрагент</th>
        <th scope="col" class="text-nowrap">Материал</th>
        <th scope="col" class="text-nowrap">Ответственный</th>
        <th scope="col" class="text-nowrap">Договор</th>
        <th scope="col" class="text-nowrap">Заявка</th>
        <th scope="col" class="text-nowrap">УПД</th>
        <th scope="col" class="text-nowrap">Дата УПД</th>
        <th scope="col" class="text-nowrap">Дата отправки УПД</th>
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

<div class="line-dashed"></div>

<!--<a href="/probe_test.php">Вернуться на старый дизайн</a>-->