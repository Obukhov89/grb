<header class="header-requirement mb-3">
    <nav class="header-menu">
        <ul class="nav">
            <li class="nav-item me-2">
                <a class="nav-link" href="<?=URI?>/oborud/new/" title="Создать оборудование">
                    <i class="fa-solid fa-plus icon-fix"></i>
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="/protocol_generator/oborud_card2.php?ID=0" title="Скачать карточки">
                    Скачать карточки
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="/protocol_generator/metr_request.php" title="Скачать заявку на поверку">
                    Скачать заявку на поверку
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link" href="/protocol_generator/passport_rc.php" title="Скачать формы 2-5">
                    Скачать формы 2-5
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link popup-with-form" href="#verification_graph" title="Скачать график">
                    Скачать график
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link popup-with-form" href="#inv_graph" title="Скачать график">
                    Скачать Инв. ведомость
                </a>
            </li>
            <li class="nav-item me-2">
                <a class="nav-link popup-help" href="/ulab/help/LIMS_Manual_Stand/Lists/Equipment_list/equipment_list.html" title="ПОМОГИТЕ">
                    <i class="fa-solid fa-question"></i>
                </a>
            </li>
        </ul>
    </nav>
</header>

<div class="filters mb-4">
    <div class="row">
        <div class="col">
            <input id="filter_everywhere" type="search" class="form-control filter" placeholder="Поиск..." aria-controls="journal_requests">
        </div>
        
        <div class="col">
            <select id="selectStage" class="form-control filter filter-stage">
                <option value="all" selected="">Все статусы</option>
                <option value="norm">Нет замечаний</option>
                <option value="unchecked">Не проверено</option>
                <option value="poverka">Истекает срок поверки</option>
                <option value="poverka_alarm">Истек срок поверки</option>
                <option value="no_certificate">Нет сертификатов</option>
                <option value="archive">Архив</option>
                <option value="longstorage">На длительном</option>
                <option value="vagon">Вагоны</option>
                <option value="in_poverka">В поверке/калибровке</option>
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

<table id="journal_oborud" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col"></th>
        <th scope="col" class="text-nowrap">Измеряемая харак-ка</th>
        <th scope="col" class="text-nowrap">Наименование</th>
        <th scope="col" class="text-nowrap">Тип</th>
        <th scope="col" class="text-nowrap">Идент</th>
        <th scope="col" class="text-nowrap">Заводской номер</th>
        <th scope="col" class="text-nowrap">Инв. номер</th>
        <th scope="col" class="text-nowrap">Ввод в экспл.</th>
        <th scope="col" class="text-nowrap">Диапазон измерения</th>
        <th scope="col" class="text-nowrap">Класс точности</th>
        <th scope="col" >Документ об аттестации/поверки/калибровки</th>
        <th scope="col" class="text-nowrap">От</th>
        <th scope="col" class="text-nowrap">До</th>
        <th scope="col" class="text-nowrap">Место поверки</th>
        <th scope="col" class="text-nowrap">Право собственности</th>
        <th scope="col" class="text-nowrap">Лаборатория</th>
        <th scope="col" class="text-nowrap">Место установки</th>
        <th scope="col" class="text-nowrap">В области акк.</th>
        <th scope="col" class="text-nowrap">Проверено</th>
        <th scope="col" class="text-nowrap">Производитель</th>
        <th scope="col" class="text-nowrap">Примечание</th>
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
<!--            <input type="text" class="form-control search">-->
            <select class="form-control search">
                <option value=""></option>
                <option value="SI">СИ</option>
                <option value="IO">ИО</option>
                <option value="VO">ВО</option>
                <option value="TS">ТС</option>
                <option value="SO">CO</option>
                <option value="KO">КО</option>
                <option value="REACT">Реактивы</option>
                <option value="OOPP">Оборудование для отбора/подготовки проб</option>
            </select>
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
        <th scope="col"></th>
        <th scope="col"></th>
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
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <select class="form-control search">
                <option value=""></option>
                <option value="1">В области</option>
                <option value="0">Не в области</option>
            </select>
        </th>
        <th scope="col"></th>
        <th scope="col"></th>
        <th scope="col"></th>
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

<div class="line-dashed"></div>

<a href="/oborud.php">Вернуться на старый дизайн</a>

<form id="verification_graph" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/generator/getVerificationGraph/" method="post" enctype="multipart/form-data">
	<div class="title mb-3 h-2">
		Выберите график
	</div>

	<div class="line-dashed-small"></div>

	<div class="form-group row">
		<label class="col-sm-4 col-form-label">
			Выберите тип графика:
		</label>
		<div class="col-sm-6">
			<select class="form-control" name="type">
				<option value="1">Поверка/калибровка СИ</option>
				<option value="2">Проверка ВО</option>
				<option value="3">Атестация ИО</option>
		<!--		<option value="4"></option>-->
			</select>
		</div>
		<div class="col-sm-2"></div>
	</div>

	<div class="form-group row">
		<label class="col-sm-4 col-form-label">
			Выберите год:
		</label>
		<div class="col-sm-6">
			<select class="form-control" name="year">
				<option value="2023" <?= date('Y') == 2023 ? 'selected' : ''?>>2023</option>
				<option value="2024" <?= date('Y') == 2024 ? 'selected' : ''?>>2024</option>
				<option value="2025" <?= date('Y') == 2025 ? 'selected' : ''?>>2025</option>
                <option value="2026" <?= date('Y') == 2026 ? 'selected' : ''?>>2026</option>
				<!--		<option value="4"></option>-->
			</select>
		</div>
		<div class="col-sm-2"></div>
	</div>

	<div class="form-group row">
		<div class="col-1"></div>
		<div class="form-check col-5">
			<input class="form-check-input" type="checkbox" value="1" id="in_oa" name="in_oa">
			<label class="form-check-label" for="in_oa">
				В области аккредетации
			</label>
		</div>
		<div class="form-check col-5">
			<input class="form-check-input" type="checkbox" value="1" id="month2" name="month2">
			<label class="form-check-label" for="month2">
				На 2 месяца
			</label>
		</div>
		<div class="col-sm-2"></div>
	</div>

	<div class="line-dashed-small"></div>

	<button type="submit" class="btn btn-primary">Скачать</button>
</form>

<form id="inv_graph" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative" action="/ulab/generator/getInventoryList/" method="post" enctype="multipart/form-data">
	<div class="title mb-3 h-2">
		Введите параметры
	</div>

	<div class="line-dashed-small"></div>

	<div class="form-group row">
		<!--		<div class="col-1"></div>-->
		<div class="form-check col-9">
			<label class="col-sm-10 col-form-label">
				Укажите номер приказа:
			</label>
			<input type="text" id="directive" name="directive" class="form-control"
				   value="">
		</div>
		<div class="col-sm-2"></div>
	</div>

	<div class="form-group row">
		<!--		<div class="col-1"></div>-->
		<div class="form-check col-9">
			<label class="col-sm-10 col-form-label">
				Укажите дату приказа:
			</label>
			<input type="date" id="directive_date" name="directive_date" class="form-control"
				   value="">
		</div>
		<div class="col-sm-2"></div>
	</div>

	<div class="form-group row">
<!--		<div class="col-1"></div>-->
		<div class="form-check col-9">
			<label class="col-sm-10 col-form-label">
				Укажите дату начала инвентаризации:
			</label>
			<input type="date" id="inputDateStart" name="invDateStart" class="form-control"
				   value="">
		</div>
		<div class="col-sm-2"></div>
	</div>

	<div class="form-group row">
<!--		<div class="col-1"></div>-->
		<div class="form-check col-9">
			<label class="col-sm-10 col-form-label">
				Укажите дату окончания инвентаризации:
			</label>
			<input type="date" id="inputDateEnd" name="invDateEnd" class="form-control"
				   value="">
		</div>
		<div class="col-sm-2"></div>
	</div>

	<div class="form-group row">
		<div class="col-1"></div>
		<div class="form-check col-9">
			<input class="form-check-input" type="checkbox" value="1" id="in_oa" name="in_oa">
			<label class="form-check-label" for="in_oa">
				В области аккредетации
			</label>
		</div>
		<div class="col-sm-2"></div>
	</div>

	<div class="line-dashed-small"></div>

	<button type="submit" class="btn btn-primary">Скачать</button>
</form>
