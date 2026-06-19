<?php
/**
 * Created by PhpStorm.
 * User: јлексей
 * Date: 26.04.2019
 * Time: 9:38
 */


use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

$file = new Bitrix\Main\IO\File(__DIR__ . '/../oborud_akt_vvoda.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

//«апрашиваем данные
$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . (int)$_GET['OB_ID']);
$sql = $res->Fetch();
$select = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID`=" . $sql['place_of_installation_or_storage'])->Fetch();

$date = $sql['god_vvoda_expluatation'];
$d1 = strtotime($date); // переводит из строки в дату
$date2 = date("d.m.Y", $d1); // переводит в новый формат


$assignedId = $sql['ID_ASSIGN1'];
$user = CUser::GetByID($assignedId)->Fetch();
$n = mb_substr($user['NAME'], 0, 1, 'UTF-8');
$o = mb_substr($user['SECOND_NAME'], 0, 1, 'UTF-8');

$assigned = "{$user['LAST_NAME']} {$n}.{$o}.";

switch ($sql['place_of_installation_or_storage']) {
    case '1':
        $lab="ЛФХИ";
        break;
    case '2':
        $lab="ЛСМ";
        break;
	case '3':
        $lab="ЛФМИ";
        break;
	case '4':
        $lab="ДСЛ";
        break;
	case '5':
        $lab="ОСК";
        break;
}


$nameArr = [];
if ( !empty($sql['OBJECT']) ) {
    $nameArr[] = $sql['OBJECT'];
}
if ( !empty($sql['REG_NUM']) ) {
    $nameArr[] = $sql['REG_NUM'];
}
if ( !empty($sql['FACTORY_NUMBER']) ) {
    $nameArr[] = $sql['FACTORY_NUMBER'];
}


$nameStr = implode(', ', $nameArr);

$document->normalizeContent();
$document->setValues([
    'NAME' => $nameStr,
	'MANUFACTURER' => $sql['manufacturer'],
	'EXPLUATATION' => $date2,
	'PLACE' => $lab,
	'ASSIGNED'  => $assigned,
    'DATE' => $date2,
]);

$result = $document->process();
$content = $document->getContent();

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="akt_vvoda.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;