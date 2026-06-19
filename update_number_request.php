<?php

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
	
$APPLICATION->SetTitle("Формирование заявки на испытания");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
//4295-4318

$requestNum = 4295;
while($requestNum <= 4318) {
	//$dealUp = CCrmDeal::GetByID($_GET['EDIT']);	
	$dealUp = CCrmDeal::GetByID($requestNum);	

	//ID сделки
	$id_deal_edit = !empty($dealUp['ID']) ? (int)$dealUp['ID'] : '';
			
	$arrTitle = explode(' ', $dealUp['TITLE']);
	$arrTitleSlash = explode('/', $arrTitle[1]);
	$num = str_replace('№', '', $arrTitleSlash[0]);

	$num = (int)$num + 4;
echo'<pre>';var_dump($num);echo'</pre>';
	if (CModule::IncludeModule('crm')) {
		$deal = new CCrmDeal;
		$arDealUpdate = array(
			"TITLE" => 'ВЛК №' . $num . getCurrentYearForDocs(), //Заголовок(номер) заявки
		);
		$resUpd = $deal->Update($dealUp['ID'],
			$arDealUpdate,
			true,
			true,
			array('DISABLE_USER_FIELD_CHECK' => true)
		);
	}
	
	//Находим сделку по ID
	$deal_edited = CCrmDeal::GetByID(!empty($id_deal_edit) ? $id_deal_edit : '');
	//Заявка(Номер заявки)
	$title_deal_edit = !empty($deal_edited['TITLE']) ? $DB->ForSql($deal_edited['TITLE']) : '';
echo'<pre>';var_dump($title_deal_edit);echo'</pre>';
	if (!empty($id_deal_edit)) {

		$res_assign_update = $DB->Query("UPDATE `ba_tz` SET `REQUEST_TITLE`='" . $title_deal_edit . "' WHERE `ID_Z`=" . $id_deal_edit);
	}
	
	$requestNum++;

}

?>