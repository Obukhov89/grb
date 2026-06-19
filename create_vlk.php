<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
require($_SERVER["DOCUMENT_ROOT"] . "/Classes/CDB.php");

$APPLICATION->RestartBuffer();

function pre($obj) {
	echo('<pre>');
	print_r($obj);
	echo('</pre>');
}

$newDeal = new CCrmDeal;

$assigned = !empty($_POST['USER_ID']) ? $_POST['USER_ID'] : '';
$labaId = !empty($_POST['LAB_ID']) ? $_POST['LAB_ID'] : '';
$gostId = !empty($_POST['OA_ID']) ? $_POST['OA_ID'] : '';
$materTz = !empty($_POST['mater']) ? $_POST['mater'] : '';
$date = !empty($_POST['DATE']) ? $_POST['DATE'] : '';
$typeId = !empty($_POST['type_vlk']) ? $_POST['type_vlk'] : '';

$mater = $DB->Query("SELECT * FROM `MATERIALS` WHERE `ID` = {$materTz}")->Fetch();
$gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID` = {$gostId}")->Fetch();

//$materTzToArr = $mater['NAME'] .', ID' . $mater['ID'];
//$gostTz = $gostId;

$tz[1] = [
	'mater' => $mater['NAME'] .', ID ' . $mater['ID'],
	'gost' => [$gostId],
	'obiem' => 1,
	'gost_new' =>[2522],
	'price' => [$gost['PRICE']]
];

$tz_ser = serialize($tz);

$curYear = date("Y");
$yReq = "/" . substr($curYear, 2);
$deals = CCrmDeal::GetList(['DATE_CREATE' => 'DESC'], ['>DATE_CREATE' => "01.01.{$curYear} 00:00:00", 'TYPE_ID' => '1']);
$num_request=0;
$num_request = $deals->SelectedRowsCount();
$num_request++;

$request_title = 'ВЛК №' . $num_request . $yReq;

$arFields = [
	"TYPE_ID" => '1',
	"ASSIGNED_BY_ID" => $assigned,
	"TITLE" => $request_title,
	"COMPANY_ID" => '2458'
];

$idDeal = $newDeal->Add($arFields);

echo $idDeal;

$updDeal = CCrmDeal::GetByID($idDeal);

$dateCreate = !empty($updDeal['DATE_CREATE']) ? date("d.m.Y", strtotime($updDeal['DATE_CREATE'])) : '';

$newDealBaTz = $DB->Query("INSERT INTO `ba_tz_vlk` SET 
`TZ` = '{$tz_ser}',
`ID_Z` = {$idDeal}, 
`REQUEST_TITLE` = '{$request_title}', 
`STAGE_ID` = 'PREPARATION', 
`LABA_ID` = {$labaId},
`DATE_CREATE` = '{$dateCreate}', 
`COMPANY_TITLE` = 'ВЛК', 
`type_results_vlk` = '{$typeId}', 
`TYPE_ID` = 1, 
`MATERIAL` = '{$mater['NAME']}'");

$ba_tz = $DB->Query("SELECT * FROM `ba_tz_vlk` WHERE `ID_Z` ={$idDeal}")->Fetch();

/* Создание шифрованной пробы*/



$arrayAllNameLaba = [
	1 => 'ЛФХИ',
	3 => 'ЛФМИ',
	4 => 'ДСЛ',
	2 => 'ЛСМ',
	5 => 'ОСК'
];

$nameLabaVlk = !empty($arrayAllNameLaba[$labaId]) ? $arrayAllNameLaba[$labaId] : '';

$yearDateCreateRequest = (!empty($dateCreate) ? date("y", strtotime($dateCreate)) : '');

$sampleCode = !empty($ba_tz['ID']) ? $ba_tz['ID'] . '.' . 'ВЛК.' . $nameLabaVlk . '/' . $yearDateCreateRequest : '-';

$updBa_tz = $DB->Query("UPDATE `ba_tz_vlk` SET `secret_probe` = '{$sampleCode}' WHERE `ID_Z` ={$idDeal}");

$updlVLK = $DB->Query("UPDATE `VLK` SET `DEAL_ID` = {$idDeal}, `TZ_ID` = {$ba_tz['ID']} WHERE `ID` = {$_POST['ID']}");
