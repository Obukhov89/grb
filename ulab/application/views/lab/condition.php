<div class="conditions-wrapper">
    <header class="header-requirement mb-3">
        <nav class="header-menu">
            <ul class="nav">
                <li class="nav-item me-2">
                    <button type="button" class="btn bg-white btn-square mt-0 add-conditions"
                            title="Добавить показатели условий">
                        <i class="fa-solid fa-plus icon-fix"></i>
                    </button>
                </li>
                <?php if ( !empty($this->data['may_edit_pressure']) ): ?>
                    <li class="nav-item me-1">
                        <a class="nav-link add-pressure" href="#" title="Добавить давление">
                            <i class="fa-solid fa-gauge-high icon-big"></i>
                        </a>
                    </li>
					<li class="nav-item me-1">
						<a class="nav-link edit-scale" href="#" title="Значение шкалы барометра">
							<i class="fa-solid fa-sliders icon-big"></i>
						</a>
					</li>
                <?php endif; ?>
                <li class="nav-item me-2 <?= !empty($this->data['room_id']) ? 'd-block' : 'd-none' ?>" id="navItemDropdown">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle mt-0" type="button"
                                id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                            Скачать
                        </button>
                        <ul class="dropdown-menu download-report" aria-labelledby="dropdownMenuButton1">
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['prior_year'] ?>"
                                   data-year-id="<?= $this->data['prior_year'] ?>">
                                    Скачать отчёт за <strong><?= $this->data['prior_year'] ?> год</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>"
                                   data-year-id="<?= $this->data['current_year'] ?>">
                                    Скачать отчёт за <strong><?= $this->data['current_year'] ?> год</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>&month=1"
                                   data-year-id="<?= $this->data['current_year'] ?>" data-month-id="1">
                                    Скачать отчёт за <strong>январь</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>&month=2"
                                   data-year-id="<?= $this->data['current_year'] ?>" data-month-id="2">
                                    Скачать отчёт за <strong>февраль</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>&month=3"
                                   data-year-id="<?= $this->data['current_year'] ?>" data-month-id="3">
                                    Скачать отчёт за <strong>март</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>&month=4"
                                   data-year-id="<?= $this->data['current_year'] ?>" data-month-id="4">
                                    Скачать отчёт за <strong>апрель</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>&month=5"
                                   data-year-id="<?= $this->data['current_year'] ?>" data-month-id="5">
                                    Скачать отчёт за <strong>май</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>&month=6"
                                   data-year-id="<?= $this->data['current_year'] ?>" data-month-id="6">
                                    Скачать отчёт за <strong>июнь</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>&month=7"
                                   data-year-id="<?= $this->data['current_year'] ?>" data-month-id="7">
                                    Скачать отчёт за <strong>июль</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>&month=8"
                                   data-year-id="<?= $this->data['current_year'] ?>" data-month-id="8">
                                    Скачать отчёт за <strong>август</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>&month=9"
                                   data-year-id="<?= $this->data['current_year'] ?>" data-month-id="9">
                                    Скачать отчёт за <strong>сентябрь</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>&month=10"
                                   data-year-id="<?= $this->data['current_year'] ?>" data-month-id="10">
                                    Скачать отчёт за <strong>октябрь</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>&month=11"
                                   data-year-id="<?= $this->data['current_year'] ?>" data-month-id="11">
                                    Скачать отчёт за <strong>ноябрь</strong>
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="/Condition/condition_doc_new.php?ID=<?= $this->data['room_id'] ?>&year=<?= $this->data['current_year'] ?>&month=12"
                                   data-year-id="<?= $this->data['current_year'] ?>" data-month-id="12">
                                    Скачать отчёт за <strong>декабрь</strong>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </nav>
    </header>
    <!--./header-top-->

    <div class="filters mb-4">
        <div class="row">
            <div class="col">
                <input type="date" id="inputDateStart" class="form-control filter filter-date-start bg-transparent"
                       value="<?= $this->data['date_start'] ?>" placeholder="Введите дату начала:">
            </div>
            <div class="col">
                <input type="date" id="inputDateEnd" class="form-control filter filter-date-end bg-transparent"
                       value="<?= $this->data['date_end'] ?>" placeholder="Введите дату окончания:">
            </div>
            <div class="col">
                <select name="select_room" id="selectRoom" class="form-control filter-room filter">
                    <option value="0">Выберите помещение</option>
                    <?php foreach ($this->data['rooms'] as $item): ?>
                        <?php if ($item['id'] < 100): ?>
                            <option class="font-bold" value="<?= $item['id'] ?>" disabled><?= $item['name'] ?></option>
                        <?php else: ?>
                            <option value="<?= $item['id'] ?>" <?= $item['id'] === $this->data['room'] ? 'selected' : '' ?>>
                                -- <?= $item['name'] ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
            </div>
        </div>
    </div>
    <!--./filters-->

    <table id="journalCondition" class="table table-striped journal text-center">
        <thead>
        <tr class="table-light align-middle">
            <th scope="col"></th>
            <th scope="col">Дата</th>
            <th scope="col">Температура, ºC</th>
            <th scope="col">Влажность, %</th>
            <th scope="col">Давление, кПа</th>
            <th scope="col">Напряжение, В</th>
            <th scope="col">Частота, Гц</th>
            <th scope="col">Помещение</th>
        </tr>
        <tr class="header-search">
            <th scope="col">
                <select class="form-control bg-white search">
                    <option value="">Все</option>
                    <option value="1">Соответствуют</option>
                    <option value="0">Не соответствуют</option>
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
                <input type="text" class="form-control search" disabled>
            </th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>


    <form id="conditionsModalForm" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative condition-modal-form"
          action="/ulab/lab/insertUpdate/" method="post">
        <input id="conditionsId" type="hidden" value="<?= $this->data['id'] ?>" name="id">

        <div class="title mb-3 h-2">
            Данные условий
        </div>

        <div class="line-dashed-small"></div>

        <div class="mb-3">
            <label class="form-label" for="room">Помещение</label>
            <select name="form[room_id]" id="room" class="form-control room" required>
                <option value="">Выберите помещение</option>
                <?php foreach ($this->data['rooms'] as $item): ?>
                    <?php if ($item['id'] < 100): ?>
                        <option class="font-bold"
                                value="<?= $item['id'] ?>" disabled><?= $item['name'] ?></option>
                    <?php else: ?>
                        <option value="<?= $item['id'] ?>" <?= (int)$this->data['form']['room_id'] === $item['id'] ? 'selected' : '' ?>>
                            -- <?= $item['name'] ?></option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="temp">Температура</label>
            <input type="number" class="form-control w-100" id="temp" name="form[temp]" step="any"
                   value="<?= $this->data['form']['temp'] ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="humidity">Влажность</label>
            <input type="number" class="form-control w-100" id="humidity" name="form[humidity]" step="any"
                   value="<?= $this->data['form']['humidity'] ?>"
                   required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="pressure">Давление</label>
            <input type="number" class="form-control w-100" id="pressure" name="form[pressure]" step="any"
                   value="<?= $this->data['form']['pressure'] ?? $this->data['pressure'] ?>"
                   required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="voltage">Напряжение, В</label>
            <input type="number" class="form-control w-100" id="voltage" name="form[voltage]" step="any"
                   value="<?= $this->data['form']['voltage'] ? $this->data['voltage'] : 220 ?>"
                   required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="frequency">Частота, Гц</label>
            <input type="number" class="form-control w-100" id="frequency" name="form[frequency]" step="any"
                   value="<?= $this->data['form']['frequency'] ? $this->data['frequency'] : 50 ?>"
                   required>
        </div>
        <?php /*if ($this->data['is_may_edit']): */?>
            <!--<div class="mb-3">
                <label class="form-label" for="date">Дата</label>
                <input type="date" class="form-control w-100" id="date" name="form[date]" step="any"
                       value="<?/*= $this->data['form']['updated_at'] ?? date('Y-m-d') */?>"
                       required>
            </div>-->
            <div style="display: none">
                <?php print_r($this->data['ttt'])?>
            </div>
            <div class="mb-3">
                <label class="form-label" for="date">Дата</label>
                <input type="datetime-local" class="form-control w-100 <?= $this->data['is_may_edit'] ? '' : 'bg-light-secondary' ?>" id="date" name="form[date]" step="any"
                       value="<?= $this->data['form']['updated_at'] ?? date('Y-m-d H:i') ?>"
                       <?= $this->data['is_may_edit'] ? '' : 'readonly' ?>>
            </div>
        <?php /*endif; */?>

        <div class="line-dashed-small"></div>

        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>


    <form id="conditionsDopModalForm" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative condition-dop-modal-form"
          action="/ulab/lab/updateDop/" method="post">
        <input id="conditionsId" type="hidden" value="<?= $this->data['id'] ?>" name="id">

        <div class="title mb-3 h-2">
            Данные условий
        </div>

        <div class="line-dashed-small"></div>

        <div class="mb-3">
            <label class="form-label" for="voltage">Напряжение, В</label>
            <input type="number" class="form-control w-100" id="voltage" name="voltage" step="any"
                   value="<?= $this->data['form']['voltage'] ?? $this->data['voltage'] ?>"
                   required>
        </div>
        <div class="mb-3">
            <label class="form-label" for="frequency">Частота, Гц</label>
            <input type="number" class="form-control w-100" id="frequency" name="frequency" step="any"
                   value="<?= $this->data['form']['frequency'] ?? $this->data['frequency'] ?>"
                   required>
        </div>

        <div class="line-dashed-small"></div>

        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
    <!--./condition-modal-form-->

    <form id="pressureModalForm" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative pressure-modal-form"
          action="/ulab/lab/pressureInsertUpdate/" method="post">
        <input id="pressureId" type="hidden" name="pressure_id" value="">

        <div class="title mb-3 h-2">
            Данные атмосферного давления
        </div>

        <div class="line-dashed-small"></div>

        <div class="edit-pressure">
            <div class="mb-3 list-group-wrapper d-none">
                <em>Для редактирования давления выберите дату и значение</em>
                <div class="list-group mb-2"></div>
            </div>

            <button type="button" class="btn btn-outline-primary text-nowrap w-100 mw-100 mb-3 d-none" id="addPressure">
                Добавить новое значение
            </button>
        </div>

        <div class="wrapper-shadow">
            <strong class="d-block mb-2 title-pressure">Новое атмосферное давление</strong>

            <div class="mb-3">
                <label class="form-label" for="pressure">Давление</label>
                <input type="number" class="form-control w-100" id="pressure" name="form[pressure]" step="any"
                       value="<?= $this->data['form']['pressure'] ?>"
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="date">Дата</label>
                <input type="datetime-local" class="form-control w-100" id="date" name="form[date]" step="any"
                       value="<?= $this->data['form']['updated_at'] ?? date('Y-m-d H:i') ?>"
                       required>
            </div>
        </div>

        <div class="line-dashed-small"></div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
    </form>
    <!--./pressure-modal-form-->

    <div id="alert_modal" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
        <div class="title mb-3 h-2 alert-title"></div>

        <div class="line-dashed-small"></div>

        <div class="mb-3 alert-content"></div>
    </div>

	<div id="barForm" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">

		<table class="table table-bordered result-bar">
			<tr>
				<td>Pк, кПа</td>
				<td>Температура среды, С</td>
				<td>Psi, кПа</td>
				<td>Pt, кПа</td>
				<td colspan="5">Расчет поправки шкалы</td>
				<td>Поправка шкалы, кПа</td>
				<td>Результат</td>
			</tr>
			<tr>
				<td>97,592</td>
				<td class="temp">Температура среды, С</td>
				<td class="press">Psi, кПа</td>
				<td class="first_pressure"></td>
				<td class="f1"></td>
				<td class="af"></td>
				<td class="f2"></td>
				<td class="fb"></td>
				<td class="f3"></td>
				<td class="bar"></td>
				<td class="pressure fw-bold"></td>
			</tr>
		</table>

		<div class="line-dashed-small"></div>
		<h6> Формулы расчетов:</h6>
		Температурная поправка:
		<div class="formula"></div>

		Расчет поправки шкалы:
		<div class="formula_bar"></div>

		Исправленный отчет по барометру
		<div class="formula_result"></div>
		<div class="line-dashed-small"></div>

		<h6>Паспортные значения поправки шкалы Барометр-андроида метеорологического БАММ-1</h6>
		<table class="table table-bordered">
			<tr>
				<td>Отметка шкалы, кПа</td>
				<td>Поправка шкалы, кПа</td>
				<td>Отметка шкалы, кПа</td>
				<td>Поправка шкалы, кПа</td>
			</tr>
			<tr>
				<td>80</td>
				<td>0,27</td>
				<td>94</td>
				<td>0,05</td>
			</tr>
			<tr>
				<td>81</td>
				<td>0,28</td>
				<td>95</td>
				<td>0,02</td>
			</tr>
			<tr>
				<td>82</td>
				<td>0,29</td>
				<td>96</td>
				<td>0,00</td>
			</tr>
			<tr>
				<td>83</td>
				<td>0,30</td>
				<td>97</td>
				<td>-0,02</td>
			</tr>
			<tr>
				<td>84</td>
				<td>0,28</td>
				<td>98</td>
				<td>-0,01</td>
			</tr>
			<tr>
				<td>85</td>
				<td>0,25</td>
				<td>99</td>
				<td>0,00</td>
			</tr>
			<tr>
				<td>86</td>
				<td>0,23</td>
				<td>100</td>
				<td>0,00</td>
			</tr>
			<tr>
				<td>87</td>
				<td>0,21</td>
				<td>101</td>
				<td>-0,01</td>
			</tr>
			<tr>
				<td>88</td>
				<td>0,20</td>
				<td>102</td>
				<td>0,06</td>
			</tr>
			<tr>
				<td>89</td>
				<td>0,19</td>
				<td>103</td>
				<td>0,13</td>
			</tr>
			<tr>
				<td>90</td>
				<td>0,17</td>
				<td>104</td>
				<td>0,17</td>
			</tr>
			<tr>
				<td>91</td>
				<td>0,14</td>
				<td>105</td>
				<td>0,20</td>
			</tr>
			<tr>
				<td>92</td>
				<td>0,11</td>
				<td>106</td>
				<td>0,23</td>
			</tr>
			<tr>
				<td>90</td>
				<td>0,14</td>
				<td></td>
				<td></td>
			</tr>
		</table>

	</div>

	<div id="scaleBar" class="bg-light mfp-hide col-md-4 m-auto p-3 position-relative">
		<table class="table table-bordered scale-table">
			<thead>
				<th>Отметка шкалыб кПа</th>
				<th>Поправка шкалы, кПа</th>
				<th>Отметка шкалыб кПа</th>
				<th>Поправка шкалы, кПа</th>
			</thead>
			<tbody class="scale-body">

			</tbody>
		</table>
	</div>
    <!--./alert_modal-->
</div>
