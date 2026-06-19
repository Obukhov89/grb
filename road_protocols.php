<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
include $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";

Global $DB;

$arr_soot = [
	0 => 'Не соответствует',
	1 => 'Соответствует',
	2 => '-',
	3 => 'Не нормируется'
];

$res = $DB->Query("SELECT p.*, bt.`ID` as btID, bt.`ID_Z`,bt.`MATCH_MANUAL`, bt.`PROBE` as btProbe, bt.`TZ` FROM `ba_tz` bt, `PROTOCOLS` p WHERE bt.`LABA_ID` LIKE '%55%' 
AND p.`ID_TZ` = bt.`ID` AND p.`NUMBER_AND_YEAR` LIKE '%/22%' ORDER BY p.`NUMBER` desc");
while ($row = $res->Fetch()){
	$av = unserialize($row['ACTUAL_VALUES']);
	$match = unserialize($row['MATCH_MANUAL']);
	$tz = unserialize($row['TZ']);
	$btProbe = unserialize($row['btProbe']);
		$osta = unserialize($row['ostatki']);
		$matchProt = [];
		$gostDocNew = [];
	foreach ($av as $key1 => $el) {
		foreach ($tz[$key1 + 1]['gost_new'] as $item) {
			if ($item == 2522) {
				continue;
			}
			$gostDocNew[] = $item;
		}
		foreach ($el as $key2 => $el2) {
			foreach ($el2 as $key3 => $el3) {
				$matchProt[] = $match[$key1][$key2][$key3];
			}
		}
	}
	$gostDocArr = implode(',', array_unique($gostDocNew));
	$gostDocArrName = [];
	$gostDocName = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID` IN ('{$gostDocArr}')");
	while ($gostDocName1 = $gostDocName->Fetch()) {
		if ($gostDocName1['GOST'] == '--' || $gostDocName1['GOST'] == '' || $gostDocName1['GOST'] == '-') {
			continue;
		}
		$gostDocArrName[] = $gostDocName1['GOST'];
	}
	$gostDoc = implode(', ', $gostDocArrName);
	$probe = unserialize($row['PROBE']);
		foreach ($probe as $k => $v) {
		foreach ($v['number_probe'] as $key => $prot) {
			if ((stristr($prot, 'Асфальто') !== false) ||
				(stristr($prot, 'ЩМА') !== false) ||
				(stristr($prot, 'керн') !== false) ||
				(stristr($prot, 'выруб') !== false)) {
				$mat = $prot;
				if (in_array('0', $matchProt)) {
					$mProt = '-';
				} else {
					$mProt = '+';
				}
				$data[] = [
					'id' => $row['ID'],
					'protocol' => $row['NUMBER_AND_YEAR'],
					'material' => $mat,
					'match' => $mProt,
					'id_z' => $row['ID_Z'],
					'object' => $row['OBJECT'],
					'gost_doc' => $gostDoc
				];
			} else {
				continue;
			}
		}
	}
}
$i = 1;
?>
<link rel="stylesheet" type="text/css" href="/ulab/assets/plugins/DataTables/datatables.css">
<link rel="stylesheet" href="/ulab/assets/plugins/DataTables/ColReorder-1.5.5/css/colReorder.dataTables.min.css">
<link rel="stylesheet" href="/ulab//assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="/ulab/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="/ulab/assets/plugins/DataTables/Buttons-2.0.1/css/buttons.dataTables.min.css">
<div>
	<table id="probe" class="myTable">
		<thead>
		<tr>
			<th>№</th>
			<th>Протокол</th>
			<th>Материал</th>
			<th>Объект строительства</th>
			<th>Требования</th>
			<th>Соответствие</th>
		</tr>
		<tr>
			<th></th>
			<th><input type="text" name="protocol_filter" id="protocol_filter"></th>
			<th><input type="text" name="material_filter" id="material_filter"></th>
			<th><input type="text" name="object_filter" id="object_filter"></th>
			<th><input type="text" name="gost_filter" id="gost_filter"></th>
			<th></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($data as $k => $elem):?>
		<tr>
			<td><?=$i?></td>
			<td><a href="https://ulab.niistrom.pro/results_isp.php?ID=<?=$elem['id_z']?>&ID_P=<?=$elem['id']?>"><?=$elem['protocol']?></a></td>
			<td><?=$elem['material']?></td>
			<td><?=$elem['object']?></td>
			<td><?=$elem['gost_doc']?></td>
			<td><?=$elem['match']?></td>
		</tr>
		<?php $i++;
		endforeach;?>
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
				"pageLength": '500',
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
				"aria": {
					"sortAscending": ": активировать для сортировки столбца по возрастанию",
					"sortDescending": ": активировать для сортировки столбца по убыванию"
				}
			},
			"order": [[ 0, "asc" ]],
			"colReorder": true,
			"bSortCellsTop": true,
			dom: 'frtB<"bottom"lip>',
			buttons: [
				'copy', 'excel', 'colvis', 'print'
			],


		});
		$('#protocol_filter').on( 'keyup', function () {
			table
				.columns( 1 )
				.search( this.value )
				.draw();
		} );
		$('#material_filter').on( 'keyup', function () {
			table
				.columns( 2 )
				.search( this.value )
				.draw();
		} );
		$('#object_filter').on( 'keyup', function () {
			table
				.columns( 3 )
				.search( this.value )
				.draw();
		} );
		$('#gost_filter').on( 'keyup', function () {
			table
				.columns( 4 )
				.search( this.value )
				.draw();
		} );
	})
</script>
