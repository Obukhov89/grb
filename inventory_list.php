<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
include $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";

global $APPLICATION;
global $DB;
$APPLICATION->SetTitle("Инвентарная ведомость");

$lab = [
	'1' => 'ЛФХИ',
	'2' => 'ЛСМ',
	'3' => 'ЛФМИ',
	'4' => 'ДСЛ',
	'5' => 'ОСК',
	'6' => 'Испытательный центр "УралНИИстром", г. Челябинск, ул. Сталеваров, д.5 к2',
	'7' => 'Отдел СМК и метрологии'
];

$data = [];
$i = 1;
$res = $DB->Query("SELECT * FROM `ba_oborud` 
					WHERE `roomnumber` != '9' 
						AND `roomnumber` != '10' 
						AND `roomnumber` != '11'
						AND `REG_NUM` NOT LIKE '%В%'
						AND `LONG_STORAGE` = '0' 
						AND `SPISANIE` = ''
						AND (`DATE_SP` = '' OR `DATE_SP` IS NULL)
					ORDER BY `place_of_installation_or_storage`");
while ($row = $res->Fetch()) {
	$poverka = strtotime($row['POVERKA']) - time();
	if ($poverka <= 0) {
		continue;
	}
	$name = !empty($row['TYPE_OBORUD']) ? trim($row['OBJECT']) . ', ' . $row['TYPE_OBORUD'] : trim($row['OBJECT']);
	$laba = $lab[$row['place_of_installation_or_storage']];
	$data[$laba][] = [
		'num' => $i,
		'name' => $name,
		'factory_number' => $row['FACTORY_NUMBER'],
		'reg_num' => $row['REG_NUM'],
	];
	$i++;
}
?>
<link rel="stylesheet" type="text/css" href="/ulab/assets/plugins/DataTables/datatables.css">
<link rel="stylesheet" href="/ulab/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css">
<link rel="stylesheet" href="/ulab//assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="/ulab/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/ulab/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css">
<a href="/protocol_generator/inventory_list_generator.php">Скачать инвентарную ведомость</a>
<div>
	<table id="oborud" class="myTable">
		<thead>
		<tr>
			<th>№п/п</th>
			<th>Наименование</th>
			<th>Зав. №</th>
			<th>Инв.№</th>
		</tr>
		<tr>
			<th></th>
			<th><input type="text" name="name_filter" id="name_filter"></th>
			<th><input type="text" name="factory_filter" id="factory_filter"></th>
			<th><input type="text" name="reg_num_filter" id="reg_num_filter"></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($data as $k => $item): ?>
			<tr>
				<td></td>
				<td style="text-align: center; font-weight: bold"><?= $k ?></td>
				<td></td>
				<td></td>
			</tr>
			<?php foreach ($item as $elem): ?>
				<tr>
					<td><?= $elem['num'] ?></td>
					<td><?= $elem['name'] ?></td>
					<td><?= !empty($elem['factory_number']) ? $elem['factory_number'] : 'б/н' ?></td>
					<td><?= !empty($elem['reg_num']) ? $elem['reg_num'] : 'б/н' ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
<script src="/ulab/assets/js/jquery-3.5.1.min.js" type="text/javascript"></script>
<script src="/ulab/assets/js/bootstrap.min.js" type="text/javascript"></script>
<script src="/ulab/assets/plugins/DataTables/DataTables-1.11.3/js/jquery.dataTables.min.js"></script>
<script src="/ulab/assets/plugins/DataTables/ColReorder-1.5.5/js/dataTables.colReorder.min.js"></script>
<script src="/ulab/assets/plugins/DataTables/Buttons-2.0.1/js/dataTables.buttons.min.js"></script>
<script src="/ulab/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.colVis.min.js"></script>
<script src="/ulab/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.print.min.js"></script>
<script src="/ulab/assets/plugins/DataTables/Buttons-2.0.1/js/buttons.html5.min.js"></script>
<script src="/ulab/assets/plugins/DataTables/JSZip-2.5.0/jszip.min.js"></script>
<script src="/ulab/assets/plugins/DataTables/dataRender/ellipsis.js"></script>
<script src="/ulab/assets/plugins/DataTables/dataRender/intl.js"></script>
<script src="/ulab/assets/plugins/DataTables/FixedHeader-3.2.0/js/dataTables.fixedHeader.min.js"></script>
<script>
	$(document).ready(function () {
		let table = $('.myTable').DataTable({
			"lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]],
			"pageLength": '1000',
			"language":{
				"processing": "Подождите...",
				"search": "Поиск:",
				"lengthMenu": "Показать _MENU_ записей",
				"info": "Записи с _START_ до _END_ из _TOTAL_ записей",
				"infoEmpty": "Записи с 0 до 0 из 0 записей",
				"infoFiltered": "(отфильтровано из _MAX_ записей)",
				"infoPostFix": "",
				"loadingRecords": "Загрузка записей...",
				"zeroRecords": "Записи отсутствуют.",
				"emptyTable": "В таблице отсутствуют данные",
				"paginate": {
					"first": "Первая",
					"previous": "Предыдущая",
					"next": "Следующая",
					"last": "Последняя"
				},
			},
			order: [],
			colReorder: true,
			bSortCellsTop: true,
			dom: 'frtlip',
		});
		$('#name_filter').on( 'keyup', function () {
			table
				.columns( 1 )
				.search( this.value )
				.draw();
		} );
		$('#factory_filter').on( 'keyup', function () {
			table
				.columns( 2 )
				.search( this.value )
				.draw();
		} );
		$('#reg_num_filter').on( 'keyup', function () {
			table
				.columns( 3 )
				.search( this.value )
				.draw();
		} );
	})
</script>
