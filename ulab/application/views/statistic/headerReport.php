<form action="" class="row col-5 mb-4" method="post">
	<div class="col-6">
		<input type="month" class="form-control" name="month" value="<?=$this->data['statistic_date']?>">
	</div>
	<div class="col-4">
		<button type="submit" class="btn btn-success">Сформировать</button>
	</div>
</form>

<div class="panel panel-default">
	<header class="panel-heading">
		Отчет по руководителям ИЦ
		<span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
    	</span>
	</header>
	<div class="panel-body">
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<th style="width: 30%"></th>
					<th>Всего</th>
					<th>ЛФХИ</th>
					<th>ДСЛ</th>
					<th>ЛФМИ</th>
					<th>ЛСМ</th>
<!--					<th>ОСК</th>-->
				</tr>
			</thead>
			<tbody>
			<tr>
				<th colspan="6">Протоколы</th>
			</tr>
				<tr>
					<td>
						Общее количество протоколов (смежные), шт
					</td>
					<td><?=$this->data['protocols']['count']?></td>
					<td><?=$this->data['protocols']['labs'][54] . '(' . $this->data['protocols']['labs_dop'][54]?>)</td>
					<td><?=$this->data['protocols']['labs'][55] . '(' .  $this->data['protocols']['labs_dop'][55]?>)</td>
					<td><?=$this->data['protocols']['labs'][56] . '(' .  $this->data['protocols']['labs_dop'][56]?>)</td>
					<td><?=$this->data['protocols']['labs'][57] . '(' .  $this->data['protocols']['labs_dop'][57]?>)</td>
					<!--					<td>--><?//=$this->data['protocols']['labs'][58] . '(' .  $this->data['protocols']['labs_dop'][58]?><!--)</td>-->
				</tr>
				<tr>
					<td>
						Выдано протоколов, шт
					</td>
					<td><?=$this->data['protocols']['labs_won'][54]['count']+$this->data['protocols']['labs_won'][55]['count']+$this->data['protocols']['labs_won'][56]['count']+$this->data['protocols']['labs_won'][57]['count']?></td>
					<td><?=$this->data['protocols']['labs_won'][54]['count']?></td>
					<td><?=$this->data['protocols']['labs_won'][55]['count']?></td>
					<td><?=$this->data['protocols']['labs_won'][56]['count']?></td>
					<td><?=$this->data['protocols']['labs_won'][57]['count']?></td>
<!--					<td>--><?//=$this->data['protocols']['labs_won'][58] . '(' .  $this->data['protocols']['labs_Won_dop'][58]?><!--)</td>-->
				</tr>
				<tr>
					<td>
						Незавершенные протоколы, шт
					</td>
					<td><?=$this->data['protocols']['in_work']?></td>
					<td><?=$this->data['protocols']['labs_work'][54] ?? '0'?></td>
					<td><?=$this->data['protocols']['labs_work'][55] ?? '0'?></td>
					<td><?=$this->data['protocols']['labs_work'][56] ?? '0'?></td>
					<td><?=$this->data['protocols']['labs_work'][57] ?? '0'?></td>
<!--					<td>--><?//=$this->data['protocols']['labs_work'][58] ?? '0'?><!--(--><?//=$this->data['protocols']['labs_Work_dop'][58]?><!--)</td>-->
				</tr>
				<tr>
					<td>
						Выдано протоколов на сумму, руб
					</td>
					<td><?= array_sum($this->data['protocols']['labs_won'][54]['price']) +
						array_sum($this->data['protocols']['labs_won'][55]['price']) +
						array_sum($this->data['protocols']['labs_won'][56]['price']) +
						array_sum($this->data['protocols']['labs_won'][57]['price']) ?></td>
					<td><?= array_sum($this->data['protocols']['labs_won'][54]['price']) ?? '0' ?></td>
					<td><?= array_sum($this->data['protocols']['labs_won'][55]['price']) ?? '0' ?></td>
					<td><?= array_sum($this->data['protocols']['labs_won'][56]['price']) ?? '0' ?></td>
					<td><?= array_sum($this->data['protocols']['labs_won'][57]['price']) ?? '0' ?></td>
					<!--					<td>--><? //=$this->data['protocols']['labs_work'][58] ?? '0'?><!--(-->
					<? //=$this->data['protocols']['labs_Work_dop'][58]?><!--)</td>-->
				</tr>
				<tr>
					<th colspan="6">Методики</th>
				</tr>
				<tr>
					<td>
						Количество завершенных испытаний, шт
					</td>
					<td><?=$this->data['protocols']['count_tests']?></td>
					<td><?=$this->data['protocols']['all_methodic_in_labs'][54]?></td>
					<td><?=$this->data['protocols']['all_methodic_in_labs'][55]?></td>
					<td><?=$this->data['protocols']['all_methodic_in_labs'][56]?></td>
					<td><?=$this->data['protocols']['all_methodic_in_labs'][57]?></td>
<!--					<td>--><?//=$this->data['protocols']['all_methodic_in_labs'][58]?><!--</td>-->
				</tr>
				<tr>
					<td>
						Использовано методик, шт
					</td>
					<td><?=$this->data['protocols']['count_method']?></td>
					<td><?=$this->data['protocols']['methodic_in_labs'][54]?></td>
					<td><?=$this->data['protocols']['methodic_in_labs'][55]?></td>
					<td><?=$this->data['protocols']['methodic_in_labs'][56]?></td>
					<td><?=$this->data['protocols']['methodic_in_labs'][57]?></td>
<!--					<td>--><?//=$this->data['protocols']['methodic_in_labs'][58]?><!--</td>-->
				</tr>
<!--				<tr>-->
<!--					<td>-->
<!--						более 50 %-->
<!--					</td>-->
<!--					<td>--><?//=$this->data['protocols']['count_value']?><!--</td>-->
<!--					<td>--><?//=$this->data['protocols']['methodic_in_labs_more'][54]?><!--</td>-->
<!--					<td>--><?//=$this->data['protocols']['methodic_in_labs_more'][55]?><!--</td>-->
<!--					<td>--><?//=$this->data['protocols']['methodic_in_labs_more'][56]?><!--</td>-->
<!--					<td>--><?//=$this->data['protocols']['methodic_in_labs_more'][57]?><!--</td>-->
<!--					<td>--><?//=$this->data['protocols']['methodic_in_labs_more'][58]?><!--</td>-->
<!--				</tr>-->
<!--				<tr>-->
<!--					<td>-->
<!--						Всего актов выполненных работ-->
<!--					</td>-->
<!--					<td></td>-->
<!--					<td></td>-->
<!--					<td></td>-->
<!--					<td></td>-->
<!--					<td></td>-->
<!--					<td></td>-->
<!--				</tr>-->
			</tbody>
		</table>
	</div>
</div>
<div class="panel panel-default">
	<header class="panel-heading">
		Отчет по сотрудникам ИЦ
		<span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
    	</span>
	</header>
	<div class="panel-body" style="overflow: auto">
		<table class="table table-striped table-bordered">
			<thead>
				<tr>
					<td rowspan="2" class="align-middle">Показатель</td>
					<td colspan="<?=count($this->data['Staff']['lfhi'])?>" class="text-center">ЛФХИ</td>
					<td colspan="<?=count($this->data['Staff']['dsl'])?>" class="text-center">ДСЛ</td>
					<td colspan="<?=count($this->data['Staff']['lfmi'])?>" class="text-center">ЛФМИ</td>
					<td colspan="<?=count($this->data['Staff']['lsm'])?>" class="text-center">ЛСМ</td>
<!--					<td colspan="--><?//=count($this->data['Staff']['osk'])?><!--" class="text-center">ОСК</td>-->
				</tr>
				<tr>
					<?php foreach ($this->data['Staff'] as $lab):?>
						<?php foreach ($lab as $user):?>
							<td class="text-center" nowrap><a href="/ulab/statistic/headerReportByUser/<?=$user['id']?>"><?=$user['short_name']?></a></</td>
						<?php endforeach;?>
					<?php endforeach;?>
				</tr>
			</thead>
			<tbody>
			<tr>
				<th colspan="23">Испытания</th>
			</tr>
				<tr>
					<td nowrap>Количество завершенных испытаний, шт:</td>
					<?php foreach ($this->data['Staff'] as $lab):?>
						<?php foreach ($lab as $user):?>
							<td class="text-center align-middle" nowrap><?=$user['test_by_user']?></td>
						<?php endforeach;?>
					<?php endforeach;?>
				</tr>
				<tr>
					<td nowrap>Количество незавершенных испытаний, шт:</td>
					<?php foreach ($this->data['Staff'] as $lab):?>
						<?php foreach ($lab as $user):?>
							<td class="text-center align-middle" nowrap><?=$user['request_in_work']?></td>
						<?php endforeach;?>
					<?php endforeach;?>
				</tr>
				<tr>
					<td nowrap>Процент завершенных испытаний<br> относительно лаборатории, %:</td>
					<?php foreach ($this->data['Staff'] as $lab):?>
						<?php foreach ($lab as $user):?>
							<td class="text-center align-middle" nowrap><?=$user['procent_by_test']?></td>
						<?php endforeach;?>
					<?php endforeach;?>
				</tr>
				<tr>
					<td nowrap>Стоимость выполненных методик, руб:</td>
					<?php foreach ($this->data['Staff'] as $lab):?>
						<?php foreach ($lab as $user):?>
							<td class="text-center align-middle" nowrap><?=$user['price_test']?></td>
						<?php endforeach;?>
					<?php endforeach;?>
				</tr>
				<tr>
					<td nowrap>Процент от общей стоимости<br> выполненных методик лаборатории, %</td>
					<?php foreach ($this->data['Staff'] as $lab):?>
						<?php foreach ($lab as $user):?>
							<td class="text-center align-middle" nowrap><?=$user['procent_by_price']?></td>
						<?php endforeach;?>
					<?php endforeach;?>
				</tr>
<!--				<tr>-->
<!--					<td>-->
<!--						Участие в общем количестве заявок:-->
<!--					</td>-->
<!--                    --><?php //foreach ($this->data['Staff'] as $lab):?>
<!--                        --><?php //foreach ($lab as $user):?>
<!--                            <td class="text-center align-middle" nowrap>--><?//=$user['count_requests']?><!--%</td>-->
<!--                        --><?php //endforeach;?>
<!--                    --><?php //endforeach;?>
<!--				</tr>-->
<!--				<tr>-->
<!--					<td>-->
<!--						Из них завершенных:-->
<!--					</td>-->
<!--					--><?php //foreach ($this->data['Staff'] as $lab):?>
<!--						--><?php //foreach ($lab as $user):?>
<!--							<td class="text-center align-middle" nowrap>--><?//=$user['stage_request']?><!--</td>-->
<!--						--><?php //endforeach;?>
<!--					--><?php //endforeach;?>
<!--				</tr>-->
<!--				<tr>-->
<!--					<td>-->
<!--						Общее количество проб в месяц-->
<!--					</td>-->
<!--                    --><?php //foreach ($this->data['Staff'] as $lab):?>
<!--                        --><?php //foreach ($lab as $user):?>
<!--                            <td class="text-center align-middle" nowrap>--><?//=$user['all_probe']?><!--</td>-->
<!--                        --><?php //endforeach;?>
<!--                    --><?php //endforeach;?>
<!--				</tr>-->
<!--				<tr>-->
<!--					<td>-->
<!--						в работе-->
<!--					</td>-->
<!--				</tr>-->
<!--				<tr>-->
<!--					<td>-->
<!--						выдан результат-->
<!--					</td>-->
<!--				</tr>-->
<!--				<tr>-->
<!--					<td>-->
<!--						не доставлено в ИЦ-->
<!--					</td>-->
<!--                    --><?php //foreach ($this->data['Staff'] as $lab):?>
<!--                        --><?php //foreach ($lab as $user):?>
<!--                            <td class="text-center align-middle" nowrap>--><?//=$user['non_probe']?><!--</td>-->
<!--                        --><?php //endforeach;?>
<!--                    --><?php //endforeach;?>
<!--				</tr>-->
<!--				<tr>-->
<!--					<td nowrap>-->
<!--						Количество испытаний <br>в которых принимал участие-->
<!--					</td>-->
<!--                    --><?php //foreach ($this->data['Staff'] as $lab):?>
<!--                        --><?php //foreach ($lab as $user):?>
<!--                            <td class="text-center align-middle" nowrap>--><?//=$user['count_gosts']?><!--</td>-->
<!--                        --><?php //endforeach;?>
<!--                    --><?php //endforeach;?>
<!--				</tr>-->
			</tbody>
		</table>
		<button class="btn btn-success" id="view-chart">Посмотреть график</button>
		<div class="visually-hidden" id="chartForm">
			<canvas id="myChart"></canvas>
		</div>
	</div>
</div>
<div class="panel panel-default">
	<header class="panel-heading">
		Финансовый Отчет ИЦ
		<span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
    	</span>
	</header>
	<div class="panel-body">
		<table class="table table-striped table-bordered">
			<thead>
			<tr>
				<th style="width: 30%">Показатель</th>
				<th>Всего</th>
				<th>ЛФХИ</th>
				<th>ДСЛ</th>
				<th>ЛФМИ</th>
				<th>ЛСМ</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<th colspan="6">Заявка</th>
			</tr>
			<tr>
				<td>
					Общая стоимость новых заявок, руб
				</td>
				<td><?=$this->data['Finance']['total']?></td>
				<td><?=$this->data['Finance']['totalLFHI']?></td>
				<td><?=$this->data['Finance']['totalDSL']?></td>
				<td><?=$this->data['Finance']['totalLFMI']?></td>
				<td><?=$this->data['Finance']['totalLSM']?></td>
			</tr>
			<tr>
				<td>
					-- С начала года, руб
				</td>
				<td><?=$this->data['Finance']['totalByYear']?></td>
				<td><?=$this->data['Finance']['totalLFHIByYear']?></td>
				<td><?=$this->data['Finance']['totalDSLByYear']?></td>
				<td><?=$this->data['Finance']['totalLFMIByYear']?></td>
				<td><?=$this->data['Finance']['totalLSMByYear']?></td>
			</tr>
			<tr>
				<td>
					Всего оплачено, руб
				</td>
				<td><?=$this->data['Finance']['paidTotal']?></td>
				<td><?=$this->data['Finance']['paidTotalLFHI']?></td>
				<td><?=$this->data['Finance']['paidTotalDSL']?></td>
				<td><?=$this->data['Finance']['paidTotalLFMI']?></td>
				<td><?=$this->data['Finance']['paidTotalLSM']?></td>
			</tr>
			<tr>
				<td>
					Всего неоплачено заявок, шт
				</td>
				<td><?=$this->data['Finance']['notPaidTotal']?></td>
				<td><?=$this->data['Finance']['notPaidTotalLFHI']?></td>
				<td><?=$this->data['Finance']['notPaidTotalDSL']?></td>
				<td><?=$this->data['Finance']['notPaidTotalLFMI']?></td>
				<td><?=$this->data['Finance']['notPaidTotalLSM']?></td>
			</tr>
			<tr>
				<td>
					-- На сумму, руб
				</td>
				<td><?=$this->data['Finance']['notPaidSumTotal']?></td>
				<td><?=$this->data['Finance']['notPaidSumTotalLFHI']?></td>
				<td><?=$this->data['Finance']['notPaidSumTotalDSL']?></td>
				<td><?=$this->data['Finance']['notPaidSumTotalLFMI']?></td>
				<td><?=$this->data['Finance']['notPaidSumTotalLSM']?></td>
			</tr>
			<tr>
				<td>
					-- С начала года, руб
				</td>
				<td><?=$this->data['Finance']['notPaidSumTotalByYear']?></td>
				<td><?=$this->data['Finance']['notPaidSumTotalLFHIByYear']?></td>
				<td><?=$this->data['Finance']['notPaidSumTotalDSLByYear']?></td>
				<td><?=$this->data['Finance']['notPaidSumTotalLFMIByYear']?></td>
				<td><?=$this->data['Finance']['notPaidSumTotalLSMByYear']?></td>
			</tr>
			<tr>
				<td>
					Оплачено частично, шт
				</td>
				<td><?=$this->data['Finance']['partiallyPaidTotal']?></td>
				<td><?=$this->data['Finance']['partiallyPaidTotalLFHI']?></td>
				<td><?=$this->data['Finance']['partiallyPaidTotalDSL']?></td>
				<td><?=$this->data['Finance']['partiallyPaidTotalLFMI']?></td>
				<td><?=$this->data['Finance']['partiallyPaidTotalLSM']?></td>
			</tr>
			<tr>
				<td>
					-- На сумму, руб
				</td>
				<td><?=$this->data['Finance']['partiallyPaidTotalSum']?></td>
				<td><?=$this->data['Finance']['partiallyPaidTotalSumLFHI']?></td>
				<td><?=$this->data['Finance']['partiallyPaidTotalSumDSL']?></td>
				<td><?=$this->data['Finance']['partiallyPaidTotalSumLFMI']?></td>
				<td><?=$this->data['Finance']['partiallyPaidTotalSumLSM']?></td>
			</tr>
			<tr>
				<td>
					-- С начала года, руб
				</td>
				<td><?=$this->data['Finance']['partiallyPaidTotalSumByYear']?></td>
				<td><?=$this->data['Finance']['partiallyPaidTotalSumLFHIByYear']?></td>
				<td><?=$this->data['Finance']['partiallyPaidTotalSumDSLByYear']?></td>
				<td><?=$this->data['Finance']['partiallyPaidTotalSumLFMIByYear']?></td>
				<td><?=$this->data['Finance']['partiallyPaidTotalSumLSMByYear']?></td>
			</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="panel panel-default">
	<header class="panel-heading">
		Отчет по МФЦ ИЦ
		<span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
    	</span>
	</header>
	<div class="panel-body">
		<table class="table table-striped table-bordered w-50">
			<thead>
			<tr>
				<th>Показатель</th>
				<th>Всего</th>
			</tr>
			</thead>
			<tbody>
			<tr>
				<th colspan="2">Заявка</th>
			</tr>
			<tr>
				<td>
					Общее количество заявок, шт
				</td>
				<td><?=$this->data['MFC']['request']?></td>
			</tr>
			<tr>
				<td>
					Успешные, шт
				</td>
				<td><?=$this->data['MFC']['request_won']?></td>
			</tr>
			<tr>
				<td>
					Неуспешные, шт
				</td>
				<td><?=$this->data['MFC']['request_lose']?></td>
			</tr>
			<tr>
				<td>
					Уникальные (одна лаб.), шт
				</td>
				<td><?=$this->data['MFC']['laba_uniq']?></td>
			</tr>
			<tr>
				<td>
					Совместные (несколько лаб.), шт
				</td>
				<td><?=$this->data['MFC']['laba_non_uniq']?></td>
			</tr>
			<tr>
				<th colspan="2">Клиент</th>
			</tr>
			<tr>
				<td>
					Новый клиентов, шт:
				</td>
				<td><?=$this->data['MFC']['new_company']?></td>
			</tr>
<!--			<tr>-->
<!--				<td>-->
<!--					постоянный клиент-->
<!--				</td>-->
<!--				<td></td>-->
<!--			</tr>-->
<!--			<tr>-->
<!--				<td>-->
<!--					недобросовестный клиент-->
<!--				</td>-->
<!--				<td></td>-->
<!--			</tr>-->
			<tr>
				<th colspan="2">Акты приемки</th>
			</tr>
			<tr>
				<td>
					Общее количество актов приемки проб, шт
				</td>
				<td><?=$this->data['MFC']['new_act']?></td>
			</tr>
			<tr>
				<td>
					Завершенные, шт
				</td>
				<td><?=$this->data['MFC']['act_won']?></td>
			</tr>
			<tr>
				<td>
					Незавершенные, шт
				</td>
				<td><?=$this->data['MFC']['act_work']?></td>
			</tr>
			<tr>
				<th colspan="2">Договоры</th>
			</tr>
			<tr>
				<td>
					Сфоромированно, шт
				</td>
				<td><?=$this->data['MFC']['dogovors_all']?></td>
			</tr>
			<tr>
				<td>
					Подписаны, шт
				</td>
				<td><?=$this->data['MFC']['dogovors_complete']?></td>
			</tr>
			<tr>
				<td>
					Не подписаны, шт
				</td>
				<td><?=$this->data['MFC']['dogovors_null']?></td>
			</tr>
			<tr>
				<th colspan="2">Техническое задание</th>
			</tr>
			<tr>
				<td>
					Отправлены клиенту, шт
				</td>
				<td><?=$this->data['MFC']['tz']?></td>
			</tr>
<!--			<tr>-->
<!--				<td>-->
<!--					подписано-->
<!--				</td>-->
<!--				<td></td>-->
<!--			</tr>-->
<!--			<tr>-->
<!--				<td>-->
<!--					не подписано-->
<!--				</td>-->
<!--				<td></td>-->
<!--			</tr>-->
			<tr>
				<th colspan="2">Счета</th>
			</tr>
			<tr>
				<td>
					Сформировано, шт
				</td>
				<td><?=$this->data['MFC']['invoice']?></td>
			</tr>
			<tr>
				<td>
					Сформировано на сумму, руб
				</td>
				<td><?=$this->data['MFC']['invoice_price']?></td>
			</tr>
			<tr>
				<td>
					Оплачено на сумму, руб
				</td>
				<td><?=$this->data['MFC']['invoice_pay_win']?></td>
			</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="panel panel-default">
	<header class="panel-heading">
		Годовой статистический отчет
		<span class="tools float-end">
            <a href="#" class="fa fa-chevron-up"></a>
    	</span>
	</header>
	<div class="panel-body">
		<h4>За данный год было:</h4>
		<p>Принято <strong><?=$this->data['Year']['fullYearRequest']?></strong> заявок</p>
		<br>
		<p>Заключено договоров: <b><?=$this->data['Year']['fullYearContract']?></b>, из них абонентских <b><?=$this->data['Year']['fullYearContractLongterm']?></b></p>
		<br>
		<p>Всего проведено испытаний: <b><?=$this->data['Year']['fullYearTests']?></b></p>
		<br>
		<p>Всего выдано протоколов: <b><?=$this->data['Year']['fullYearProtocols']?></b></p>
		<br>
		<p>Всего принято проб: <b><?=$this->data['Year']['fullYearProbe']?></b></p>
	</div>
</div>
<!--<div class="row gx-2">-->
<!--	<div class="col-md-6">-->
<!--		<div class="panel">-->
<!--			<header class="panel-heading">Отчет по руководителям ИЦ</header>-->
<!--		</div>-->
<!--	</div>-->
<!--	<div class="col-md-6">-->
<!--		<div class="panel">-->
<!--			<header class="panel-heading">Отчет по сотрудникам ИЦ</header>-->
<!--		</div>-->
<!--	</div>-->
<!--	<div class="col-md-6">-->
<!--		<div class="panel">-->
<!--			<header class="panel-heading">Отчет по финансам ИЦ</header>-->
<!--		</div>-->
<!--	</div>-->
<!--	<div class="col-md-6">-->
<!--		<div class="panel">-->
<!--			<header class="panel-heading">Отчет по МФЦ ИЦ</header>-->
<!--		</div>-->
<!--	</div>-->
<!--</div>-->
