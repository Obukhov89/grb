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
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";
global $DB;

$signPath = $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/archive_client/' . $_GET['ID'] . '/sign.png';
$pathDoc = $_SERVER['DOCUMENT_ROOT'] . '/Act.docx';
$newTemplate = new \PhpOffice\PhpWord\Template($pathDoc);
$newTemplate->setImageValue('image1.png', $signPath);
$newTemplate->saveAs($_SERVER['DOCUMENT_ROOT'] . '/Act1.docx'); // Сохранение документа
//-------------Работа с QR кодом.Конец-------------------//

$file = new Bitrix\Main\IO\File(__DIR__ . '/../Act1.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$actBaseSql = $DB->Query("SELECT * FROM `ACT_BASE` WHERE `ID_Z`=" . $_GET['ID'])->Fetch();

$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . (int)$_GET['ID']);
$sql = $res->Fetch();
$tz = unserialize($sql['TZ']);//[$_GET['mater']];
$probe = unserialize($sql['PROBE']);//[$_GET['mater']];
//dump($probe);
$companys = CCrmCompany::GetList();
$deal = CCrmDeal::GetByID($sql['ID_Z']); //Берём значение сделки
$company = CCrmCompany::GetByID($deal['COMPANY_ID']);
$zakaz_man = $company['TITLE'];
$deliveryman = $actBaseSql['deliveryman'];
$gosts = [];

//--'Вставка №Заявки в шаблоны' начало--//
//Номер заявки
$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';

$arr_num_request = !empty($num_request) ? explode(' ', $num_request) : '';

$str_num_request = !empty($arr_num_request[1]) ? $arr_num_request[1] : '';

$req = "заявка";

//--'Вставка №Заявки в шаблоны' окончание--//
foreach ($tz as $k) {
	foreach ($k['gost'] as $item) {
		$res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
		$gost = $res->Fetch();
		//echo $item;
		$gosts[] = $gost['SPECIFICATION'];
		}
}
$gosts = implode(', ', array_unique($gosts));
if (!empty($sql['QUARRY_ID'])) {
	$quarList = $DB->Query("SELECT * FROM `Quarry` WHERE `ID` = {$sql['QUARRY_ID']}")->Fetch();
	$quarryName = $quarList['NAME'];
}
//Формируем данные
$probe_arr = [];
$probeinf = $DB->Query("SELECT mtr.`NAME_MATERIAL`, mtr.`ID_MATERIAL`, mtr.`OBIEM`, ptm.`cipher`, 
										(SELECT GROUP_CONCAT('<p>', `SPECIFICATION` SEPARATOR '</p>') FROM `ba_gost` bg, `gost_to_probe` gtp 
										WHERE bg.`ID` = gtp.`gost_method` AND gtp.`probe_id` = ptm.`ID`) gtp 
										FROM `MATERIALS_TO_REQUESTS` mtr, `probe_to_materials` ptm 
										WHERE mtr.`ID` = ptm.`material_request_id` AND `ID_DEAL`={$_GET['ID']}");
while ( $arrProbe = $probeinf->Fetch() ) {
	$probe_arr[] = [
		'Number' => $arrProbe['cipher'],
		'Name' => $arrProbe['NAME_MATERIAL'],
	];
}

//if (empty($probe)) {
//	$y_date = null;
//	if (!empty($actBaseSql['DATE_ACT'])) {
//		$y_date = date("Y", strtotime($actBaseSql['DATE_ACT']));
//		if ($y_date != '2020') $y_date = "/".substr($y_date, 2);
//		else $y_date = "/".$y_date;
//	}
//	$probeinf = $DB->Query("SELECT * FROM `MATERIALS_TO_REQUESTS` WHERE `ID_DEAL`=" . (int)$_GET['ID']);
//	$actNum = $DB->Query("SELECT * FROM ACT_BASE WHERE ID_Z={$_GET['ID']}")->Fetch();
//	$sh_nunmber = json_decode($actNum['PROBE']);
//	$probe_arr = [];
//	$NameProbe = [];
//	$NumProbe = [];
//	$h = 0;
//	$x=0;
//	while ($probeinform = $probeinf->Fetch()) {
//		$z = 0;
//		for ($z; $z< $probeinform['OBIEM']; $z++) {
//			$material = explode(', ID', $probeinform['NAME_MATERIAL']);
//			$NameProbe[$x] = $material[0];
//			$NumProbe[$x] = $sh_nunmber[$h][$z];
//			$x++;
//		}
//	$h++;
//		for ($j = 0; $j < $x; $j++)
//		{
//			$probe_arr[$j] =
//				[
//					'Number' => $NumProbe[$j],
//					'Name' => $NameProbe[$j],
//				];
//		}
//	}
//} else {
//	$NameProbe = [];
//	$NumProbe = [];
//	$z = 0;
//	$h = 0;
//	$probe_arr = [];
//	foreach ($probe as $elem) {
////	$NameProbe = $elem['number_probe'];
////	$NumProbe = $elem['sh_number'];
//		foreach ($elem['number_probe'] as $element) {
//			$NameProbe[$z] = explode(', ID', $element)[0];
//			$z = ++$z;
//		}
//		foreach ($elem['sh_number'] as $element) {
//			$NumProbe[$h] = $element;
//			$h = ++$h;
//		}
//	}
//	for ($j = 0; $j < $z; $j++)
//	{
//		$probe_arr[$j] =
//			[
//				'Number' => $NumProbe[$j],
//				'Name' => $NameProbe[$j],
//			];
//	}
//}
$dogovor = getDogovor($_GET['ID']);
$date = $sql['DATE_ACT'] ? date("d.m.Y", strtotime($sql['DATE_ACT'])) : date("d.m.Y");
//echo '------------------------------';
//echo $probe[1]['zakaz_man'];
//echo 'PROBE='.$probe['zakaz_man'].'___';
// while ($company = $companys->Fetch()) {
	// //echo $company['ID'].'   ';
    // if (!empty($probe[1]['zakaz_man']) && $probe[1]['zakaz_man'] == $company['ID'])
        // 
// }
echo '________'.$gosts;
//Заполняем данные
$document->normalizeContent();
$document->setValues([
    'Number' => $actBaseSql['ACT_NUM'],
	'DogovorNum' => $dogovor,
    'DatePost' => $date,
//    'deliveryman' => $date,
    'ZakazMan' => $zakaz_man,
    'deliveryman' => $deliveryman,
    'mesto' => $actBaseSql['PLACE_PROBE'],
    'dateProbe' => !empty($actBaseSql['DATE_PROBE']) ? ' от ' . date('d.m.Y', strtotime($actBaseSql['DATE_PROBE'])) : '',
    'probe_proizv' => $actBaseSql['PROBE_PROIZV'],
    'SPECIFICATION' => !empty($gosts) ? 'Определяемые показатели: ' . $gosts : '',
	'DESCRIPTION' => !empty($sql['DESCRIPTION']) ? $sql['DESCRIPTION'] : '',
//	'QUARRY' => !empty($sql['QUARRY_ID']) ? 'Карьер: ' . $quarList['NAME'] : '',
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
]);

$result = $document->process();
$content = $document->getContent();

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header("Content-Disposition: attachment;filename=\"Акт приемки проб {$actBaseSql['ACT_NUM']} от {$date}.docx\"");
header('Cache-Control: max-age=0');
echo $content;
die;
