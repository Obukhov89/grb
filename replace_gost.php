<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
//include $_SERVER['DOCUMENT_ROOT'] . '/Classes/CDB.php';
//
//$db_conn = new CDB();
$res = $DB->Query("SELECT `TZ` FROM `ba_tz` WHERE ID=" . $_GET['ID_TZ'])->Fetch();
//$gost = $DB->Query("SELECT * FROM `ba_gost`");
$useGost = unserialize($res['TZ']);
//echo '<pre>';
//print_r($useGost);
//echo '</pre>';
for ($i = 1; $i <= count($useGost); $i++ ) {
	for ($j = 0; $j < count($useGost[$i]['gost']); $j++) {
		$gostOld = $DB->Query("SELECT ID, IN_OA, NUM_OA_NEW FROM `ba_gost` WHERE ID=" . $useGost[$i]['gost'][$j])->Fetch();

		$gost = $DB->Query("SELECT ID, GOST, NUM_OA_NEW FROM `ba_gost` WHERE NUM_OA_NEW=" . $useGost[$i]['gost'][$j])->Fetch();

		if (($gostOld['IN_OA'] == '1') && ($gostOld['NUM_OA_NEW'] == '0')) {
			$useGost[$i]['gost'][$j] = $gost['ID'];
			}
	}
}
$updateGost = serialize($useGost);
$DB->Query("UPDATE `ba_tz` SET `TZ` ='" . serialize($useGost) . "' WHERE ID=" . $_GET['ID_TZ']);

