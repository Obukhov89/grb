<?php
//require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
use \Bitrix\Main\Loader;
Loader::IncludeModule("crm");
//$dealUp = CCrmDeal::GetByID($_GET['EDIT']);
$result_select_dogovor = $DB->Query("SELECT `ID`,`NUMBER`,`CLIENT_ID`,`TZ_ID`,`ACTUAL_VER` from `DOGOVOR` where isnull(`CLIENT_ID`)");

//echo'<pre>';var_dump($result_select_dogovor);
while($row_select_dogovor = $result_select_dogovor->Fetch()) {
	//echo'<pre>';var_dump($row_select_dogovor);
	
	$result_select_ba_tz = null;
	
	if (!empty($row_select_dogovor['TZ_ID'])) {
			$result_select_ba_tz = $DB->Query("SELECT * from `ba_tz` WHERE `ID`=" . (int)$row_select_dogovor['TZ_ID'])->Fetch();
	}
	
	$deal = null;
	
	if (!empty($result_select_ba_tz['ID_Z'])) {
		$deal = CCrmDeal::GetByID($result_select_ba_tz['ID_Z']);
		//$deal = CCrmDeal::GetByID(2463);
		//echo'<pre>';var_dump($deal['COMPANY_ID']);
	}
	
	if (!empty($deal['COMPANY_ID']) && !empty($row_select_dogovor['ID'])) {
		//echo'<pre>';var_dump($deal['COMPANY_ID'],$row_select_dogovor['ID']);
		//echo'======';
		$result_update_dogovor = $DB->Query("UPDATE `DOGOVOR` SET `CLIENT_ID`='" . (int)$deal['COMPANY_ID'] . "' WHERE `ID` = " . (int)$row_select_dogovor['ID']);
	}
}

?>