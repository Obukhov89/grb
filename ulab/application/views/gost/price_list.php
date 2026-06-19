<header class="header-requirement mb-3">
	<nav class="header-menu">
		<ul class="nav">
			<li class="nav-item me-2">
				<button type="button" class="btn bg-white btn-square mt-0 get-price"
						title="Экспорт прайса">
					<i class="fa-solid fa-file-arrow-down"></i>
				</button>
			</li>
			<li class="nav-item me-2">
				<button type="button" class="btn bg-white btn-square mt-0 set-price"
						title="Импорт прайса">
					<i class="fa-solid fa-file-arrow-up"></i>
				</button>
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
        <th scope="col" class="text-nowrap">№ ОА</th>
        <th scope="col" class="text-nowrap">Документ</th>
        <th scope="col" class="text-nowrap">Пункт</th>
        <th scope="col" class="text-nowrap">Год</th>
        <th scope="col">Определяемая характеристика / показатель</th>
        <th scope="col" class="text-nowrap">Метод</th>
        <th scope="col" class="text-nowrap">Цена</th>
        <th scope="col" class="text-nowrap">Новая цена</th>
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
        </th>
        <th scope="col">
        </th>
    </tr>
    </thead>
    <tbody>
    </tbody>
</table>

<div id="setPriceForm" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative set-price-form">

	<div class="title mb-3 h-2">
		Импорт прайс-листа на методики
	</div>

	<div class="line-dashed-small"></div>

	<div class="mb-3">
		<label for="formFile" class="form-label">Выберете файл для импорта</label>
		<input class="form-control" type="file" name="importXLSX" id="formFile">
	</div>

	<div class="form-check requiredCheckBox">
		<input class="form-check-input" type="checkbox" value="" id="requiredCheckBox" required>
		<label class="form-check-label" for="flexCheckDefault">
			Я проверил(а) файл с ценами и согласна на изменение цен
		</label>
	</div>
	<div class="line-dashed-small"></div>

	<button type="button" class="btn btn-primary submitButton" disabled>Отправить</button>
</div>
