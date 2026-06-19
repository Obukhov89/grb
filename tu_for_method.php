<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/include/function.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");



global $DB;

$ID = $_POST['id'];
$arrTU = $_POST['arrGost'];

$tu = json_encode($arrTU, JSON_UNESCAPED_UNICODE);
$DB->Query("UPDATE `ba_gost` SET `ID_TU` = '{$tu}' WHERE `ID` = {$ID}");

header('location: obl_acc.php?ID='. $ID);
