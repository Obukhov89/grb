<div class="filters mb-4">
    <div class="row">
        <div class="col">
            <input type="date" id="inputDateStart" class="form-control filter filter-date-start" value="<?=$this->data['date_start'] ?? '2010-01-01'?>" placeholder="Введите дату начала:">
        </div>

        <div class="col">
            <input type="date" id="inputDateEnd" class="form-control filter filter-date-end" value="<?=date('Y-m-d')?>" placeholder="Введите дату окончания:">
        </div>
        <div class="col">
            <select id="selectLab" class="form-control filter filter-lab">
                <option value='' selected>Bсе лаборатории</option>
                <?php foreach ($this->data['lab'] as $lab): ?>
                    <option value="<?= $lab['ID'] ?? '' ?>"><?= $lab['NAME'] ?? '' ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col">
            <select id="selectSend" class="form-control filter filter-send">
                <option value='' selected>Все протоколы</option>
                <option value='1'>Отправленные</option>
                <option value='2'>Неотправленные</option>
            </select>
        </div>

        <div class="col-auto">
            <button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
        </div>
    </div>
</div>

<table id="journal_protocol" class="table table-striped journal">
    <thead>
    <tr class="table-light">
        <th scope="col"></th>
        <th scope="col" class="text-nowrap">ОА</th>
        <th scope="col" class="text-nowrap">Серт.</th>
        <th scope="col" class="text-nowrap">№ Протокола</th>
        <th scope="col" class="text-nowrap">Дата регистрации</th>
        <th scope="col" class="text-nowrap">Дата окончания испытаний</th>
        <th scope="col" class="text-nowrap">Дата отправки</th>
        <th scope="col" class="text-nowrap">Заказчик</th>
        <th scope="col" class="text-nowrap">Объект испытаний</th>
        <th scope="col" class="text-nowrap">Ответственный</th>
        <th scope="col" class="text-nowrap">№ Заявки</th>
        <th scope="col" class="text-nowrap">№ ТЗ</th>
        <th scope="col" class="text-nowrap">Рез-ты исп.</th>
        <th scope="col" class="text-nowrap">DOC/PDF</th>
        <th scope="col" class="text-nowrap">Последнее изменение</th>
    </tr>
    <tr class="header-search">
        <th scope="col"></th>
        <th scope="col">
            <select class="form-control search">
                <option value="0"></option>
                <option value="1">ОА</option>
            </select>
        </th>
        <th scope="col">
			<select class="form-control search">
				<option value="0"></option>
				<option value="1">Cерт.</option>
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
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
        </th>
<!--        <th scope="col">-->
<!--            <input type="text" class="form-control search">-->
<!--        </th>-->
<!--        <th scope="col">-->
<!--            <input type="text" class="form-control search">-->
<!--        </th>-->
<!--        <th scope="col">-->
<!--            <input type="text" class="form-control search">-->
<!--        </th>-->
<!--        <th scope="col">-->
<!--            <input type="text" class="form-control search">-->
<!--        </th>-->
        <th scope="col">
            <input type="text" class="form-control search" disabled>
        </th>
        <th scope="col">
            <input type="text" class="form-control search" disabled>
        </th>
        <th scope="col">
            <input type="text" class="form-control search">
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

<div class="line-dashed"></div>

<!--<a href="/protocols_test_binding_multiple_protocols.php">Вернуться на старый дизайн</a>-->
