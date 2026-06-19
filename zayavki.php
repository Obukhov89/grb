<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Журнал заявок протоколов выданных ВНЕ ЛИС");
include $_SERVER["DOCUMENT_ROOT"] . "/include/function.php";
$location_laba = "window.location.assign('zayavki.php?laba='+this.options[this.selectedIndex].value)";
switch ($_GET['laba']) {
	case '1':
		$id_dep = 54;
		$where_condition = " AND (ba.`LABA_ID` LIKE '" . $id_dep ."' OR ba.`LABA_ID` LIKE '" . $id_dep .",%' OR ba.`LABA_ID` LIKE '%," . $id_dep . "' OR ba.`LABA_ID` LIKE '%," . $id_dep . ",%') ";
		break;
	case '2':
		$id_dep = 57;
		$where_condition = " AND (ba.`LABA_ID` LIKE '" . $id_dep ."' OR ba.`LABA_ID` LIKE '" . $id_dep .",%' OR ba.`LABA_ID` LIKE '%," . $id_dep . "' OR ba.`LABA_ID` LIKE '%," . $id_dep . ",%') ";
		break;
	case '3':
		$id_dep = 56;
		$where_condition = " AND (ba.`LABA_ID` LIKE '" . $id_dep ."' OR ba.`LABA_ID` LIKE '" . $id_dep .",%' OR ba.`LABA_ID` LIKE '%," . $id_dep . "' OR ba.`LABA_ID` LIKE '%," . $id_dep . ",%') ";
		break;
	case '4':
		$id_dep = 55;
		$where_condition = " AND (ba.`LABA_ID` LIKE '" . $id_dep ."' OR ba.`LABA_ID` LIKE '" . $id_dep .",%' OR ba.`LABA_ID` LIKE '%," . $id_dep . "' OR ba.`LABA_ID` LIKE '%," . $id_dep . ",%') ";
		break;
	case '5':
		$id_dep = 58;
		$where_condition = " AND (ba.`LABA_ID` LIKE '" . $id_dep ."' OR ba.`LABA_ID` LIKE '" . $id_dep .",%' OR ba.`LABA_ID` LIKE '%," . $id_dep . "' OR ba.`LABA_ID` LIKE '%," . $id_dep . ",%') ";
		break;
	case '6':
		$id_dep = 53;
		$where_condition = " AND (ba.`LABA_ID` LIKE '" . $id_dep ."' OR ba.`LABA_ID` LIKE '" . $id_dep .",%' OR ba.`LABA_ID` LIKE '%," . $id_dep . "' OR ba.`LABA_ID` LIKE '%," . $id_dep . ",%') ";
		break;

}
?>
<h2>Журнал заявок протоколов выданных ВНЕ ЛИС</h2>

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
<table id="results_isp">
	<thead>
	<th>№</th>
	<th>Заявка</th>
	<th>№протокола</th>
	<th>Главный ответственный</th>
	</thead>

<?php
$i = 1;
if (!empty($_GET['laba'])) {
	$res = $DB->Query("SELECT ba.`ID_Z`, pr.`VERIFY`, ba.`REQUEST_TITLE`, pr.`NUMBER_AND_YEAR`, pr.`ID` prID, ba.`NUM_P_TABLE` FROM `ba_tz` ba, `PROTOCOLS` pr WHERE pr.`PROTOCOL_OUTSIDE_LIS` = '1' AND pr.`PDF` IS NULL AND pr.`ID_TZ` = ba.`ID` {$where_condition}");
} else {
	$res = $DB->Query("SELECT ba.`ID_Z`, pr.`VERIFY`, ba.`REQUEST_TITLE`, pr.`NUMBER_AND_YEAR`, pr.`ID` prID, ba.`NUM_P_TABLE` FROM `ba_tz` ba, `PROTOCOLS` pr WHERE pr.`PROTOCOL_OUTSIDE_LIS` = '1' AND pr.`PDF` IS NULL AND pr.`ID_TZ` = ba.`ID`");
}
while ($sql = $res->Fetch()) {
	$deal = CCrmDeal::GetByID((int)$sql['ID_Z']);
	$verifyID = !empty($sql['VERIFY']) ? unserialize($sql['VERIFY']) : '';
	if (!empty($verifyID)) {
	$user = null;
	$z = null;
	$j = 0;
		for ($j = 0; $j < count($verifyID); $j++) {
			$user[$j] = CUser::GetByID($verifyID[$j])->Fetch();
			$z[$j] = getInitials($user[$j]['NAME']) . " " . $user[$j]['LAST_NAME'];
			$usera = implode(', ', $z);
		}
	} else {
		$usera = '';
	}
	echo "<tr><td>{$i}</td><td><a href='/request_card.php?ID={$sql['ID_Z']}' target='_blank'>{$sql['REQUEST_TITLE']}</a></td><td><a href='/results_isp.php?ID={$sql['ID_Z']}&ID_P={$sql['prID']}' target='_blank'>{$sql['NUMBER_AND_YEAR']}</a></td><td>{$usera}</td></tr>";
	$i++;
}
?>
</table>
