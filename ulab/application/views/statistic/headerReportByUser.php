<div class="panel panel-default">
	<header class="panel-heading">
		Отчет по сотруднику <u><?=$this->data['User']['user_name']?></u>
		<span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
    	</span>
	</header>
	<div class="panel-body" style="overflow: auto">
		<table class="table table-striped table-bordered">
			<thead>
			<tr>
				<th></th>
				<th>Количество завершенных испытаний:</th>
				<th>Количество испытаний в работе:</th>
				<th>Процент выполненных испытаний<br> относительно лаборатории:</th>
				<th>Стоимость выполненных методик:</th>
				<th>Процент от общей стоимости<br> выполненных методик лаборатории</th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ($this->data['Staff'] as $lab):?>
				<tr>
					<td style="width: 10%"><b><?=$lab['ruDate']?></b></td>
					<td><?=$lab['test_by_user']?></td>
					<td><?=$lab['request_in_work']?></td>
					<td><?=$lab['procent_by_test']?>%</td>
					<td><?=$lab['price_test']?></td>
					<td><?=$lab['procent_by_price']?>%</td>
				</tr>
			<?php endforeach;?>
			</tbody>
		</table>
	</div>
</div>
