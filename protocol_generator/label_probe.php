<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 26.04.2019
 * Time: 9:38
 */


use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;
require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/phpqrcode/qrlib.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
require_once($_SERVER['DOCUMENT_ROOT'] . '/Classes/DataStorage.php');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
use  NcJoes\OfficeConverter\OfficeConverter;



Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";


//Запрашиваем данные
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
$sql = $res->Fetch();
$tz = unserialize($sql['TZ']);//[$_GET['mater']];
$probe = unserialize($sql['PROBE']);//[$_GET['mater']];
dump($probe);
//$companys = CCrmCompany::GetList();
//$deal = CCrmDeal::GetByID($sql['ID_Z']); //Берём значение сделки
//$company = CCrmCompany::GetByID($deal['COMPANY_ID']);
//$zakaz_man = $company['TITLE'];
$gosts = [];
//-------подбор лаборатории------//
if (!empty($sql['LABA_ID'])) {
	$labaNum = explode(',', $sql['LABA_ID']);
	$labaNumCheck = (int)$labaNum[0];


} else {
	$labAssigned = $DB->Query("SELECT * FROM `assigned_to_request` WHERE `deal_id` = {$sql['ID_Z']} AND `is_main` = 1")->Fetch();
	$assignToLab = CUser::GetByID($labAssigned['user_id'])->Fetch();
	$labaNumCheck = $assignToLab['UF_DEPARTMENT'][0];
}
switch ($labaNumCheck) {
	case 56:
		$labaNumName = "ЛФМИ";
		break;
	case 53:
		$labaNumName = "Администрация ИЦ";
		break;
	case 54:
		$labaNumName = "ЛФХИ";
		break;
	case 58:
		$labaNumName = "ОСК";
		break;
	case 60:
		$labaNumName = "Экстранет";
		break;
	case 55:
		$labaNumName = "ДСЛ";
		break;
	case 57:
		$labaNumName = "ЛСМ";
		break;
}
//-------------конец подбра лаборатории------------//

//-------------Работа с QR кодом.Начало-------------------//
$qrPath = $_SERVER['DOCUMENT_ROOT'] . '/qrNEW.png';
QRcode::png('https://ulab.niistrom.pro/ulab/request/card/' . $sql['ID_Z'], $qrPath);
$pathDoc = $_SERVER['DOCUMENT_ROOT'] . '/label_probe.docx';
$newTemplate = new \PhpOffice\PhpWord\Template($pathDoc);
$newTemplate->setImageValue('image1.png', $qrPath);
$newTemplate->saveAs($_SERVER['DOCUMENT_ROOT'] . '/example1.docx'); // Сохранение документа
//-------------Работа с QR кодом.Конец-------------------//
$file = new Bitrix\Main\IO\File(__DIR__ . '/../example1.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

//--'Вставка №Заявки в шаблоны' начало--//
//Номер заявки
//$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';
//
//$arr_num_request = !empty($num_request) ? explode(' ', $num_request) : '';
//
//$str_num_request = !empty($arr_num_request[1]) ? $arr_num_request[1] : '';
//
//$req = "заявка";

//--'Вставка №Заявки в шаблоны' окончание--//

foreach ($tz[1]['gost'] as $item) {
    $res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
    $gost = $res->Fetch();
	//echo $item;
    $gosts[] = $gost['SPECIFICATION'];
}
$gosts = implode(', ', array_unique($gosts));
//Имя пробприемщика
$assigned = CUser::GetByID($USER->GetID())->arResult[0];
$assigned_user = $assigned['NAME'].' '.$assigned['LAST_NAME'];

//Формируем данные
//$k=(int)$_GET['mater'];
//
//
//$probe_arr = [];
//for ($j=0; $j< count($probe[$k]['sh_number']); $j++)
//{
//    $probe_arr[$j] = [
//         'Number' => $probe[$k]['sh_number'][$j],
//         'Name' => $probe[$k]['number_probe'][$j],
//      ];
//
//}

$probeToMaterial = $DB->Query("SELECT mtr.`NAME_MATERIAL`, ptm.`cipher` FROM `MATERIALS_TO_REQUESTS` mtr, `probe_to_materials` ptm WHERE ptm.`material_request_id` = mtr.`ID` AND mtr.`ID_DEAL` = {$_GET['ID']} AND mtr.`ID_MATERIAL` = {$_GET['mater']}");
while ($arrProbe = $probeToMaterial->Fetch()) {
	$probe_arr[] = [
		'Number' => $arrProbe['cipher'],
		'Name' => $arrProbe['NAME_MATERIAL']
	];
}
$dateCreateAct = date('d.m.Y');

echo '________'.$gosts;
//Заполняем данные
$document->normalizeContent();
$document->setValues([
    'Date' => $dateCreateAct,
     'Lab' => $labaNumName,
	//--'Вставка №Заявки в шаблоны' начало--//
	'num_request' => !empty($str_num_request) ? 'по заявке ' . $str_num_request : '',
    'Probe' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $probe_arr,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'ProbeItemName' => 'Probe.Item.Name',
    'ProbeItemNumber' => 'Probe.Item.Number',
	'Name' => $assigned_user
]);

$result = $document->process();
$content = $document->getContent();

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="label prob.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;
