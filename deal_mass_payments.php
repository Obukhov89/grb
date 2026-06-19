<?php

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";



$dealsId = array();
$requestArr = array();
$tzArr = array();
$materArr= array();
$results = array();
$priceArr = array();
$datePayment = date("d.m.Y");

$dealsId = explode('_', $_GET['ID']);

foreach($dealsId as $key => $id)
{
	$requestArr[$key] = $DB->Query("SELECT `PRICE` FROM `ba_tz` WHERE `ID_Z`=" . $id)->Fetch();
	$DB->Query("UPDATE `ba_tz` SET `OPLATA` = ".$requestArr[$key]['PRICE'].", `DATE_OPLATA` = '".$datePayment."' WHERE `ID_Z`=" . $id);
	
}

echo "Заявки успешно отмечены оплаченными!";

//require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>