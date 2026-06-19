<header class="header-requirement mb-3">
	<nav class="header-menu">
		<ul class="nav">
			<li class="nav-item me-2">
				<a class="nav-link" href="/ulab/reactive/list_reactive/" title="Вернуться к списку">
					<svg class="icon" width="20" height="20">
						<use xlink:href="/ulab/assets/images/icons.svg#list"></use>
					</svg>
				</a>
			</li>
		</ul>
	</nav>
</header>
<div class="filters mb-3">
    <div class="row">
        <div class="col-auto">
            <div class="two-button">
<!--                <button type="button" name="add_entry"-->
<!--                        class="btn btn-primary popup-first btn-add-entry w-100 mw-100 mt-0 btn-reactive">-->
<!--                    Добавить реактив-->
<!--                </button>-->
                <button type="button" name="add_entry"
                        class="btn btn-primary popup-second btn-add-entry w-100 mw-100 mt-0 btn-reactive">
                    Провести реактив
                </button>
            </div>
        </div>
    </div>
</div>
<!--./filters-->

<table id="recipe_journal" class="table  table-striped  text-center">
    <thead>
    <tr class="table-light">
        <th scope="col" class="text-nowrap">Название</th>
        <th scope="col" class="text-nowrap">№ партии</th>
        <th scope="col" class="text-nowrap">Закуп. док.</th>
        <th scope="col" class="text-nowrap">Дата пост.</th>
        <th scope="col" class="text-nowrap">Количество</th>
        <th scope="col" class="text-nowrap">Дата произв.</th>
        <th scope="col" class="text-nowrap">Срок годности</th>
        <th scope="col" class="text-nowrap">Агрег.</th>
        <th scope="col" class="text-nowrap">Квал.</th>
        <th scope="col" class="text-nowrap">НД</th>
        <th scope="col" class="text-nowrap">Лаборатория</th>
        <th scope="col" class="text-nowrap">Тип</th>
        <th scope="col" class="text-nowrap">Провел</th>
        <th scope="col" class="text-nowrap"></th>
        <th scope="col" class="text-nowrap"></th>
        <th scope="col" class="text-nowrap"></th>
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
            <select class="form-control bg-white search">
                <option value="">Все</option>
                <?php foreach ($this->data['laboratory'] as $val):?>
                    <option value="<?=$val['id_dep']?>"><?=$val['short_name']?></option>
                <?php endforeach;?>
            </select>
        </th>
        <th scope="col">
            <select class="form-control bg-white search">
                <option value="">Все</option>
                <option value="1">Реактивы</option>
                <option value="2">Расходники</option>
            </select>
        </th>
        <th scope="col"></th>
		<th scope="col"></th>
		<th scope="col"></th>
		<th scope="col"></th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<form id="add-entry-modal-form-first" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
      action="/ulab/reactive/addReactiveModel/" method="post">
    <div class="title mb-3 h-2">
        Добавьте реактив
    </div>
    <div class="line-dashed-small"></div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label" for="nameReactive">Название реактива</label>
            <input type="text" name="toSQL[reactive_model][name]" class="form-control" id="nameReactive" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Агрегатное состояние</label>
            <select name="toSQL[reactive_model][id_aggregate_state]" class="form-control bg-white" required>
                <option value="" selected disabled></option>
                <?php
                foreach ($this->data['aggregate'] as $val): ?>
                    <option value="<?= $val['id'] ?? '' ?>"><?= $val["name"] ?></option>
                <?php
                endforeach; ?>
            </select>
        </div>
    </div>
    <div class="line-dashed-red"></div>
    <div class="row mb-3">
        <div class="col">
            <input name="toSQL[reactive_model][is_precursor]" class="form-check-input" type="checkbox" value="1"
                   id="is_precursor">
            <label class="form-check-label" for="is_precursor">
                Реактив является прекурсором или подлежит особому контролю
            </label>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Отправить</button>
</form>

<form id="add-entry-modal-form-second" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
      action="/ulab/reactive/addReactiveAndReceive/" method="post">
    <div class="title mb-3 h-2">
        Добавьте НД и квалификацию
    </div>

    <div class="line-dashed-small"></div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Реактив</label>
            <select class="form-control select-reactive h-auto" required id="reactive-name">
                <option value="" selected disabled></option>
                <?php
                foreach ($this->data['reactive'] as $val): ?>
                    <option value="<?= $val['id'] ?? '' ?>"><?= $val["name"] ?></option>
                <?php
                endforeach; ?>
            </select>
        </div>
    </div>
	<input type="hidden" name="receive[id_reactive]" id="id-reactive">
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Нормативный документ реактива</label>
            <input type="text" class="form-control name-recipe" id="reactive-nd" readonly>
        </div>
    </div>
    <div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Квалификация</label>
				<input type="text" class="form-control name-recipe" id="quality" readonly>
            </div>
        </div>
<!--        <div class="row mb-3">-->
<!--            <div class="col">-->
<!--                <label class="form-label">Регистрационный номер</label>-->
<!--				<input type="text" class="form-control name-recipe" id="number" readonly>-->
<!--            </div>-->
<!--        </div>-->
    </div>

    <div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label" ">Закупочная документация</label>
                <input type="text" name="receive[doc_receive]" class="form-control" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Дата поступления</label>
                <input name="receive[date_receive]" type="date" class="form-control filter filter-date-start"
                       value="<?= $this->data['current_date'] ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Номер партии/лот</label>
                <input type="text" name="receive[number_batch]" class="form-control" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Количество</label>
                <input type="number" name="receive[quantity]" step="0.1" min="1" max="10000"
                       class="form-control bg-white"
                       value="" required>
            </div>
            <div class="col">
                <label class="form-label">Размерность</label>
                <select name="receive[id_unit_of_measurement]" class="form-control " required>
                    <option value="" selected disabled></option>
                    <?php
                    foreach ($this->data['unit_of_measurement'] as $val): ?>
                        <option value="<?= $val['id'] ?? '' ?>"><?= $val['name'] ?></option>
                    <?php
                    endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Дата производства</label>
                <input name="receive[date_production]" type="date" class="form-control filter filter-date-start"
                       value="<?= $this->data['current_date'] ?>" required>
            </div>
            <div class="col">
                <label class="form-label">Срок годности</label>
                <input name="receive[date_expired]" type="date" class="form-control filter filter-date-start"
                       value="<?= $this->data['current_date'] ?>" required>
            </div>
        </div>
    </div>
    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-primary">Отправить</button>
</form>

<form id="add-entry-modal-form-third" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
      action="/ulab/reactive/addReceiveReactive/" method="post">
    <div class="title mb-3 h-2">
        Провести реактив
    </div>

    <div class="line-dashed-small"></div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Реактив</label>
            <select name="receive[id_reactive]" class="form-control select-reactive h-auto" required>
                <option value="" selected disabled></option>
                <?php
                foreach ($this->data['full_reactive'] as $val): ?>
                    <option value="<?= $val['id'] ?? '' ?>"><?= $val["full_name"] ?></option>
                <?php
                endforeach; ?>
            </select>
        </div>
    </div>
    <div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label" for="nameDoc">Закупочная документация</label>
                <input type="text" name="receive[doc_receive]" class="form-control name-recipe" id="nameDoc" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Дата поступления</label>
                <input name="receive[date_receive]" type="date" class="form-control filter filter-date-start"
                       value="<?= $this->data['current_date'] ?>" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label" for="nameDoc">Номер партии/лот</label>
                <input type="text" name="receive[number_batch]" class="form-control name-recipe" id="nameDoc" required>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Количество</label>
                <input type="number" name="receive[quantity]" step="0.1" min="1" max="10000"
                       class="form-control bg-white"
                       value="" required>
            </div>
            <div class="col">
                <label class="form-label">Квалификация</label>
                <select name="receive[id_unit_of_measurement]" class="form-control " required>
                    <option value="" selected disabled></option>
                    <?php
                    foreach ($this->data['unit_of_measurement'] as $val): ?>
                        <option value="<?= $val['id'] ?? '' ?>"><?= $val['name'] ?></option>
                    <?php
                    endforeach; ?>
                </select>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label class="form-label">Срок годности</label>
                <input name="receive[date_expired]" type="date" class="form-control filter filter-date-start"
                       value="<?= $this->data['current_date'] ?>" required>
            </div>
        </div>
    </div>
    <button type="submit" class="btn btn-primary">Отправить</button>

</form>

<form id="add-control-modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative"
      action="/ulab/reactive/addReceiveControl/" method="post">
    <div class="title mb-3 h-2">
        Добавить расход
    </div>
    <div class="line-dashed-small"></div>

    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Дата списания:</label>
            <input name="receive[date_expired]" type="date" class="form-control filter filter-date-start"
                   value="<?= $this->data['current_date'] ?>" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Цель списания:</label>
            <input name="receive[date_expired]" type="date" class="form-control filter filter-date-start"
                   value="<?= $this->data['current_date'] ?>" required>
        </div>
    </div>
    <div class="row mb-3">
        <div class="col">
            <label class="form-label">Количество:</label>
            <input name="receive[date_expired]" type="number" step="0.001" min="0.001" class="form-control" required>
        </div>
    </div>

    <div class="line-dashed-small"></div>

    <button type="submit" class="btn btn-danger">Добавить</button>

</form>
