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

$file = new Bitrix\Main\IO\File(__DIR__ . '/../oborud_akt_sp.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

//«апрашиваем данные
$res = $DB->Query("SELECT * FROM `ba_oborud` WHERE `ID`=" . (int)$_GET['OB_ID']);
$sql = $res->Fetch();
//$select = $DB->Query("SELECT * FROM `ba_laba` WHERE `ID`=" . $sql['place_of_installation_or_storage'])->Fetch();
//$tz = unserialize($sql['TZ']);//[$_GET['mater']];
//$probe = unserialize($sql['PROBE']);//[$_GET['mater']];
//var_dump($sql['god_vvoda_expluatation']);
if(!empty($sql['DATE_SP'])){
    $date = $sql['DATE_SP'];
    $d1 = strtotime($date); // переводит из строки в дату
    $date2 = date("d.m.Y", $d1); // переводит в новый формат
}else{
    $date2 = '';
}
// $Users = CUser::GetList(($by="work_department"), ($order="desc"), '');
// while ($rsUsers = $Users->Fetch()){
// var_dump($rsUsers);
// }
// $assigned="";

// switch ($sql['ASSIGNED']) {
    // case '1':
        // $assigned="Овчинникова Т.А.";
        // break;
    // case '2':
        // $assigned="Велютич Т.В.";
        // break;
	// case '3':
        // $assigned="Шарипова А.В.";
        // break;
	// case '4':
        // $assigned="Щелканова А.В.";
        // break;
// }
$assignedId = $sql['ID_ASSIGN1'];
$user = CUser::GetByID($assignedId)->Fetch();
$n = mb_substr($user['NAME'], 0, 1, 'UTF-8');
$o = mb_substr($user['SECOND_NAME'], 0, 1, 'UTF-8');

$assigned = "{$user['LAST_NAME']} {$n}.{$o}.";

switch ($sql['IDENT']) {
    case 'IO':
        $ident = 'ИО';
        break;
    case 'VO':
        $ident = 'ВО';
        break;
    case 'TS':
        $ident = 'ТС';
        break;
    case 'SI':
        $ident = 'СИ'; //Системы измерения
        break;
    case 'SO':
        $ident = 'СО';
        break;
    case 'REACT':
        $ident = 'реактив';
        break;
    case 'OOPP':
        $ident = 'ООПП';
        break;
    default:
        $ident = "СИ/ИО/ВО";
}


$document->normalizeContent();
$document->setValues([
    'OB_ID' => $sql['ID'],
    'NAME' => $sql['OBJECT'],
    'IDENT_OB' => $ident,
	'DATE_SP' => $date2,
	'SPISANIE' => $sql['SPISANIE'],
	'INV_NUM'  => $sql['REG_NUM'],
	// 'FACTORY_NUM' => $sql['FACTORY_NUMBER'] ? ", зав.".$sql['FACTORY_NUMBER'] : '',
	'FACTORY_NUM' => $sql['FACTORY_NUMBER'],
	'Assigned' => $assigned

]);

$result = $document->process();
$content = $document->getContent();

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="akt_spisaniya.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;
