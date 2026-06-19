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

		<div class="col-auto">
			<button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
		</div>
	</div>
</div>

<table id="journal_probe" class="table table-striped journal">
	<thead>
	<tr class="table-light">
		<th scope="col" class="text-nowrap">Акт ПП</th>
		<th scope="col" class="text-nowrap">Шифры</th>
		<th scope="col" class="text-nowrap">Дата</th>
		<th scope="col" class="text-nowrap">Объект</th>
		<th scope="col" class="text-nowrap">Ответственный</th>
		<th scope="col" class="text-nowrap">Лаборатория</th>
		<th scope="col" class="text-nowrap">Внести результаты</th>
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
		<th></th>
	</tr>
	</thead>
	<tbody>
	</tbody>
</table>

<div class="line-dashed"></div>

