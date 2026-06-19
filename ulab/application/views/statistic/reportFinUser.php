<!--<pre>--><?//=print_r($this->data['a'])?><!--</pre>-->
<div class="filters mb-4">
	<div class="row">
		<div class="col-3">
			<input type="date" id="inputDateStart" class="form-control filter filter-date-start date" value="<?= date('Y-m-01') ?>" placeholder="Введите дату начала:">
		</div>

		<div class="col-3">
			<input type="date" id="inputDateEnd" class="form-control filter filter-date-end date" value="<?= date('Y-m-d') ?>" placeholder="Введите дату окончания:">
		</div>

		<div class="col-auto">
			<button type="button" class="btn btn-outline-secondary filter-btn-reset">Сбросить</button>
		</div>
	</div>
</div>

<table id="journal_users" class="table table-bordered table-hover">
	<thead>
	<tr>
		<th></th>
		<th></th>
		<th>Участие в новых заявках</th>
		<th>Участие в неуспешных заявках</th>
		<th>Участие в закрытых успешных заявках</th>
		<th>Участие в заявках, по которым составлены акты ПП</th>
		<th>Участие в оплаченных полностью заявках</th>
		<th>Участие в оплаченных частично</th>
		<th>Участие в сформированных протоколах</th>
		<th>Участие в выданных протоколах</th>
		<th>Использовано методик</th>
	</tr>
	</thead>
	<tbody class="table-group-divider">
	</tbody>
</table>

<div class="line-dashed"></div>

