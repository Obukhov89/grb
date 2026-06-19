<div class="filters mb-4">
    <div class="row">
        <div class="col">
            <input type="date" id="inputDateStart" class="form-control filter filter-date-start" value="<?= $this->data['date_start'] ?? '' ?>" placeholder="Введите дату начала:">
        </div>

        <div class="col">
            <input type="date" id="inputDateEnd" class="form-control filter filter-date-end" value="<?= date('Y-m-d') ?>" placeholder="Введите дату окончания:">
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

        <div class="col">
            <select id="selectStageGroup" class="form-control filter filter-stage-group">
                <option value="" selected>Все</option>
                <option value="no_fail">Исключить провальные заявки</option>
            </select>
        </div>

        <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
        </div>
    </div>
</div>

<table id="journal_probe" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-center">№ Акта приёма-передачи проб (образцов)</th>
        <th scope="col" class="text-center">Шифр проб (образцов)</th>
        <th scope="col" class="text-center">Основание</th>
        <th scope="col" class="text-center">Дата получения проб (образцов)</th>
        <th scope="col" class="text-center">Заказчик</th>
        <th scope="col" class="text-center">Объект испытания</th>
        <th scope="col" class="text-center">Ответственный</th>
        <th scope="col" class="text-center">№ заявки</th>
        <th scope="col" class="text-center">Лаборатория</th>
        <th scope="col" class="text-center">№ протокола</th>
        <th scope="col" class="text-center">Дата формирования протокола испытаний</th>
        <th scope="col" class="text-center">Утилизация</th>
        <th scope="col" class="text-center">В работе</th>
    </tr>
    <tr class="header-search">
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
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <select id="selectIrrevocable" class="form-control filter">
                <option value='0' selected>Нет</option>
                <option value='1'>Да</option>
            </select>
        </th>
        <th scope="col">
            <select id="selectAcceptProbe" class="form-control filter">
                <option value='0' selected>Нет</option>
                <option value='1'>Да</option>
            </select>
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div class="line-dashed"></div>
