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
        <th scope="col" class="text-nowrap">Номер документа</th>
        <th scope="col">Характеристика / показатель</th>
        <th scope="col">Время затраченное на испытание по ГОСТ, ч</th>
        <th scope="col">Фактически затраченное время сотрудником на испытание, ч</th>
        <th scope="col">Фактически затраченное время оборудования (без сотрудника), ч</th>
        <th scope="col">Фактически затраченное время на пробоподготовку, ч</th>
        <th scope="col">Общая длительность, ч</th>
		<th scope="col">Количество сотрудников</th>
		<th scope="col">Трудозатраты, чел/ч</th>
        <th scope="col">Сохранить</th>
    </tr>
    <tr class="header-search">
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
        </th>
        <th scope="col">
        </th>
        <th scope="col">
        </th>
        <th scope="col">
        </th>
        <th scope="col">
        </th>
        <th scope="col">
        </th>
        <th scope="col">
        </th>
        <th scope="col">
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>
