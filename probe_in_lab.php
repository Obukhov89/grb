<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->RestartBuffer();

	$ba_tz = $DB->Query("SELECT `PROBE` FROM `ba_tz` WHERE `ID_Z` = {$_POST['ID']}")->Fetch();
	$probe = unserialize($ba_tz['PROBE']);
	if ($_POST['value'] == 1) {

	}
echo '<pre>'; print_r($probe); echo '</pre>';

if ($_POST['value'] == 1) {
	$DB->Query("UPDATE `ba_tz` SET `PROBE_IN_LAB` = '2' WHERE `ID_Z` = {$_POST['ID']}");
} else {
	$DB->Query("UPDATE `ba_tz` SET `PROBE_IN_LAB` = '1' WHERE `ID_Z` = {$_POST['ID']}");
}
