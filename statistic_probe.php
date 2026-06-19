<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
include $_SERVER['DOCUMENT_ROOT']."/testDB/CDB.php";
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

$APPLICATION->SetTitle("Статистика испытаний за 2021 год");

$laba_name = 'Все лаборатории';

$location_laba = "window.location.assign('statistic_probe.php?laba='+this.options[this.selectedIndex].value)";
switch ($_GET['laba']) {
	case '1':
		$id_dep = 54;
		$where_condition = " AND (ba.`LABA_ID` LIKE '" . $id_dep ."' OR ba.`LABA_ID` LIKE '" . $id_dep .",%' OR ba.`LABA_ID` LIKE '%," . $id_dep . "' OR ba.`LABA_ID` LIKE '%," . $id_dep . ",%') ";
		$laba_name = 'Лаборатория физико-химических испытаний';
		break;
	case '2':
		$id_dep = 57;
		$where_condition = " AND (ba.`LABA_ID` LIKE '" . $id_dep ."' OR ba.`LABA_ID` LIKE '" . $id_dep .",%' OR ba.`LABA_ID` LIKE '%," . $id_dep . "' OR ba.`LABA_ID` LIKE '%," . $id_dep . ",%') ";
		$laba_name = 'Лаборатория строительных материалов';
		break;
	case '3':
		$id_dep = 56;
		$where_condition = " AND (ba.`LABA_ID` LIKE '" . $id_dep ."' OR ba.`LABA_ID` LIKE '" . $id_dep .",%' OR ba.`LABA_ID` LIKE '%," . $id_dep . "' OR ba.`LABA_ID` LIKE '%," . $id_dep . ",%') ";
		$laba_name = 'Лаборатория физико-механических испытаний';
		break;
	case '4':
		$id_dep = 55;
		$where_condition = " AND (ba.`LABA_ID` LIKE '" . $id_dep ."' OR ba.`LABA_ID` LIKE '" . $id_dep .",%' OR ba.`LABA_ID` LIKE '%," . $id_dep . "' OR ba.`LABA_ID` LIKE '%," . $id_dep . ",%') ";
		$laba_name = 'Дорожно-строительная лаборатория';
		break;
	case '5':
		$id_dep = 58;
		$where_condition = " AND (ba.`LABA_ID` LIKE '" . $id_dep ."' OR ba.`LABA_ID` LIKE '" . $id_dep .",%' OR ba.`LABA_ID` LIKE '%," . $id_dep . "' OR ba.`LABA_ID` LIKE '%," . $id_dep . ",%') ";
		$laba_name = 'Отдел строительного контроля';
		break;
	case '6':
		$id_dep = 53;
		$where_condition = " AND (ba.`LABA_ID` LIKE '" . $id_dep ."' OR ba.`LABA_ID` LIKE '" . $id_dep .",%' OR ba.`LABA_ID` LIKE '%," . $id_dep . "' OR ba.`LABA_ID` LIKE '%," . $id_dep . ",%') ";
		$laba_name = 'Испытательный центр "УралНИИстром", г. Челябинск, ул. Сталеваров, д.5 к2';
		break;
}
?>
<div style="margin: 15px 0px">
	<select class="oborud" name="laba" id="laba" onchange="<?= $location_laba?>" >
		<option value="all" selected>Все лаборатории</option>

		<?php
		$select = $DB->Query("SELECT * FROM `ba_laba`");
		while ($option = $select->Fetch()) {

			echo "<option ";
			if ($_GET['laba'] == $option['ID']) {
				echo "selected";
			}
			echo " value='" . $option['ID'] . "' style='font-weight:bold'>" . $option['NAME'] . "</option>";
		}
		?>
	</select>
</div>
<?php
$x = 0;

$res = $DB->Query("SELECT * FROM `ba_tz` ba WHERE `NUM_P` IS NOT NULL AND (`DATE_RESULTS` BETWEEN '2021-01-01' AND '2021-12-31') " . $where_condition);
while ($sql = $res->Fetch()) {
	$x++;
	$tz = unserialize($sql['TZ']);
	$companys[] = '<li>'.$sql['COMPANY_TITLE'].'</li>';

	foreach ($tz as $item) {

		$countObiemLaba += $item['obiem'];
		$mater_exp[] = $item['mater'];
		$price[] = array_sum($item['price']) * $item['obiem'];

//			$sumPrice += $price;
//			$mater_exp .= '<li>'.$item['mater'].'</li>';
	}

	$maters_expa = array_unique($mater_exp);
	$companyTitle = array_unique($companys);
	$countCompany = count($companyTitle);
	natcasesort($companyTitle);
	natcasesort($maters_expa);

}
//echo '<pre>'; print_r($price); echo '</pre>';
foreach ($maters_expa as $elem) $maters_exp .= '<li>'.$elem.'</li>';
foreach ($companyTitle as $elem) $companysTitle .= '<li style="list-style-type: none;">'.$elem.'</li>';
foreach ($price as $elem) $sumPrice += $elem;

$sumPrices = number_format($sumPrice, 2, ',', ' ');
//
?>
<table id="results_isp">
	<thead>
	<th>Лаборатория</th>
	<th>Всего проб испытано</th>
	<th>Список всех организаций</th>
	<th>Испытанные материалы</th>
	<th>Общая стоимость</th>
	</thead>
	<tr>
		<td style="vertical-align: top;"><b><?= $laba_name ?></b></td>
		<td style="vertical-align: top;"><b><?= $countObiemLaba ?></b></td>
		<td style="vertical-align: top;"><?= $countCompany . '<ul>' . $companysTitle ?></ul></td>
		<td style="vertical-align: top;"><ul><?= $maters_exp ?></ul></td>
		<td style="vertical-align: top;"><b><?= $sumPrices ?></b></td>
	</tr>
</table>
