<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

CModule::IncludeModule('crm');

if (!empty($_POST['id_tz']) && !empty($_POST['id_deal'])) {

	$result_select_taken = null;
	$result_select_taken = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_POST['id_deal'])->Fetch();
	
	$id_tz_taken = !empty($result_select_taken['ID']) ? (int)$result_select_taken['ID'] : ''; 
	
	$result_update_taken = $DB->Query("UPDATE `ba_tz` SET `TAKEN_ID_DEAL`=" . $id_tz_taken . " WHERE `ID`=" . (int)$_POST['id_tz'])->Fetch();

	$deal_taken = CCrmDeal::GetByID($_POST['id_deal']);

	$number_deal = !empty($deal_taken['TITLE']) ? $deal_taken['TITLE'] : ''; 
	$company_deal = !empty($deal_taken['COMPANY_TITLE']) ? $deal_taken['COMPANY_TITLE'] : ''; 
	$begin_date_deal = !empty($deal_taken['BEGINDATE']) ? $deal_taken['BEGINDATE'] : ''; 
	
	if (!empty($deal_taken)) {
		$title_deal_taken = 'Заявка ' . $number_deal . ', ' . $company_deal . ', от ' . $begin_date_deal;
	
		
		echo $title_deal_taken;
	}
	 
}	

if (!empty($_POST['id_tz_del'])) {
	$result_update_taken = $DB->Query("UPDATE `ba_tz` SET `TAKEN_ID_DEAL`='' WHERE `ID`=" . (int)$_POST['id_tz_del'])->Fetch();
}
	
?>