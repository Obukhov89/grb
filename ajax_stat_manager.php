<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

$APPLICATION->RestartBuffer();

global $DB;

$data = [];
$to = date('Y-m-d', strtotime($_POST['to']));
$from = date('Y-m-d', strtotime($_POST['from']));
$fromCRM = date('d.m.Y', strtotime($_POST['from']));
$toCRM = date('d.m.Y', strtotime($_POST['to']));

$arOrder = array('ID' => 'ASC');
$arFilter = array();
$arSelect = array();
$deal = CCrmDeal::GetList(['DATE_CREATE' => 'DESC'], ['>DATE_CREATE' => ConvertDateTime($fromCRM, "d.m.Y") . " 00:00:00", '<=DATE_CREATE' => ConvertDateTime($toCRM, "d.m.Y") . " 23:59:59", 'TYPE_ID' => "SALE"], $arSelect);
while ($row = $deal->fetch()) {
//    $row['TITLE'] = StringHelper::removeSpace($row['TITLE']);
	$result[] = $row;
//    $assign = $DB->Query("SELECT `user_id` FROM `assigned_to_request` WHERE `deal_id` = {$result['ID']}");

	if ($row['STAGE_ID'] == 4) {
		$endDeal[] = $row['ID'];
	}
	$companys[] = $row['COMPANY_ID'];
	$company = CCrmCompany::GetByID($row['COMPANY_ID']);
	$date[] = strtotime($company['DATE_CREATE']);
	if (strtotime($company['DATE_CREATE']) >= strtotime($from)) {
		$newComp[] = $company['DATE_CREATE'];
	}
}
foreach ($result as $id) {
	$ids[] = $id['ID'];
}
$id = implode(',', $ids);

$assign = $DB->Query("SELECT atr.`deal_id`, atr.`user_id` FROM `assigned_to_request` atr WHERE atr.`deal_id` IN ({$id}) AND (atr.`user_id` = 62 OR atr.`user_id` = 56 OR atr.`user_id` = 83) ORDER BY atr.`deal_id` ASC");
while ($assigns = $assign->Fetch()) {
	$idAss[] = $assigns['deal_id'];
	if ($assigns['user_id'] == '62') {
		$shlykova[] = $assigns['deal_id'];
	} elseif ($assigns['user_id'] == '83') {
		$golikova[] = $assigns['deal_id'];
	} elseif ($assigns['user_id'] == '56') {
		$bulatova[] = $assigns['deal_id'];
	}
}

$probes = $DB->Query("SELECT `OBIEM` FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL` IN ({$id})");
while ($probe = $probes->Fetch()) {
	$countProbe[] = $probe['OBIEM'];
}
$allProbe = array_sum($countProbe);
$dogovors = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `DATE` BETWEEN '{$from}' AND '{$to}'")->SelectedRowsCount();

$comp = array_unique($companys);


$data['request'] = count($result) . ' / ' . count($endDeal);
$data['req_shlykova'] = count($shlykova);
$data['req_golikova'] = count($golikova);
$data['req_bulatova'] = count($bulatova);
$data['client'] = count($comp) . ' / ' . count($newComp);
$data['company'] = count($newComp);
$data['dogovor'] = $dogovors;
$data['probe'] = $allProbe;

echo json_encode($data, JSON_UNESCAPED_UNICODE);

?>
