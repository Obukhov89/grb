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
require_once($_SERVER['DOCUMENT_ROOT'] . '/ulab/application/core/Model.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/ulab/application/models/Methods.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/ulab/application/models/Lab.php');
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
use  NcJoes\OfficeConverter\OfficeConverter;

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

//Запрашиваем данные
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);

$checkTzResponse = $DB->Query("SELECT leader FROM CHECK_TZ WHERE tz_id = '{$_GET['TZ_ID']}'");
$checkTzResult = [];


$sql = $res->Fetch();
$tz = unserialize($sql['TZ']);//[$_GET['mater']];
$probe = unserialize($sql['PROBE']);//[$_GET['mater']];
$gosts = [];

$labaNumName = [];

$labShortName = [
    '54' => 'ЛФХИ',
    '57' => 'ЛСМ',
    '56' => 'ЛФМИ',
    '55' => 'ДСЛ',
    '58' => 'ОСК',
    '73' => 'ОПП',
    '72' => 'ОК',
    '66' => 'ОСК-Д',
    '67' => 'ОСК-О',
    '68' => 'НИЦ',
];
while ($row = $checkTzResponse->Fetch()) {
    $departmentId = $DB->Query("SELECT id_dep FROM ba_laba WHERE HEAD_ID = '{$row['leader']}'")->Fetch()['id_dep'];

    $currentShortName = $labShortName[$departmentId];

    if($currentShortName) {
        $labaNumName []= $currentShortName;
    }
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
//Имя пробоприемщика
$actId = $_GET['ACT_ID'] ? $_GET['ACT_ID'] : 0;
//Формируем данные

$actNumber = 0;

if ($actId) {
    $actNumber = $DB->Query("SELECT ACT_NUM FROM ACT_BASE WHERE ID = '{$actId}'")->Fetch()['ACT_NUM'];
}

//$probe_arr = [];
//$h = 0;
//for ($i=1; $i <= count($probe); $i++) {
//	for ($j=0; $j< count($probe[$i]['sh_number']); $j++)
//	{
//		$probe_arr[$h] = [
//			 'Number' => $probe[$i]['sh_number'][$j],
//			 'Name' => $probe[$i]['number_probe'][$j],
//		  ];
//		$h++;
//	}
//}
if ($_GET['ID'] < 9763) {
	$probeToMaterial = $DB->Query("SELECT mtr.`NAME_MATERIAL`, ptm.`cipher` FROM `MATERIALS_TO_REQUESTS` mtr, `probe_to_materials` ptm WHERE ptm.`material_request_id` = mtr.`ID` AND mtr.`ID_DEAL` = {$_GET['ID']}");
} else {
    if($actNumber) {
        $probeToMaterial = $DB->Query("SELECT m.`NAME`, mtr.`cipher`, mtr.`name_for_protocol`, g.name as gName , CONCAT('[', GROUP_CONCAT(gtp.method_id SEPARATOR ', '), ']') as method_id
                                    FROM `ulab_material_to_request` mtr
                                    join `MATERIALS` m on m.ID = mtr.material_id
                                    left join `materials_groups` g on g.id = mtr.group
                                    left join `ulab_gost_to_probe` gtp on gtp.material_to_request_id = mtr.id
                                    WHERE mtr.`deal_id` = {$_GET['ID']} AND mtr.`cipher` LIKE '{$actNumber}.%'
                                    group by mtr.id ORDER BY mtr.probe_number");
    } else {

        $probeToMaterial = $DB->Query("SELECT m.`NAME`, mtr.`cipher`, mtr.`name_for_protocol`, g.name as gName , CONCAT('[', GROUP_CONCAT(gtp.method_id SEPARATOR ', '), ']') as method_id
                                    FROM `ulab_material_to_request` mtr
                                    join `MATERIALS` m on m.ID = mtr.material_id
                                    left join `materials_groups` g on g.id = mtr.group
                                    left join `ulab_gost_to_probe` gtp on gtp.material_to_request_id = mtr.id
                                    WHERE mtr.`deal_id` = {$_GET['ID']} group by mtr.id ORDER BY mtr.probe_number");
    }
}

$methodModel = new Methods();
$labModel = new Lab();
while ($arrProbe = $probeToMaterial->Fetch()) {
	$arrLabNameByProbe = [];
	foreach (json_decode($arrProbe['method_id']) as $method_id) {
		$labName = $methodModel->getLab($method_id);
		foreach ($labName as $lab) {
			$arrLabNameByProbe[] = $labModel->getLabaById($lab);
		}
	}

	$probe_arr[] = [
		'Number' => !empty($arrProbe['name_for_protocol']) ? "{$arrProbe['cipher']} ({$arrProbe['name_for_protocol']})" : $arrProbe['cipher'],
		'Name' => $arrProbe['NAME'] . ' ' . $arrProbe['gName'],
		'Lab' => implode(', ', array_unique($arrLabNameByProbe))
	];
}
$probe_pairs  = [];
$probe_single = [];

for ($i = 0; $i < count($probe_arr); $i += 2) {
    if (isset($probe_arr[$i + 1])) {
        $left  = $probe_arr[$i];
        $right = $probe_arr[$i + 1];

        $probe_pairs[] = [
            'L_Number' => $left['Number'],
            'L_Name'   => $left['Name'],
            'L_Lab'    => $left['Lab'],

            'R_Number' => $right['Number'],
            'R_Name'   => $right['Name'],
            'R_Lab'    => $right['Lab'],
        ];
    } else {
        $probe_single[] = [
            'S_Number' => $probe_arr[$i]['Number'],
            'S_Name'   => $probe_arr[$i]['Name'],
            'S_Lab'    => $probe_arr[$i]['Lab'],
        ];
    }
}
//if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
//    echo "<pre>";
//    print_r($probe_arr);
//    echo "</pre>";
//    die();
//}

//if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
//    pre($probe_arr);
//    exit();
//}

$dateCreateAct = date('d.m.Y');
echo '________'.$gosts;

$labaNumName = implode(', ', $labaNumName);

//Заполняем данные
//if ($_SESSION['SESS_AUTH']['USER_ID'] == 61) {
//    var_dump($probe_pairs);
//    exit();
//}
$document->normalizeContent();
$document->setValues([
    'Date' => $dateCreateAct,     // заполняет {{Date}}
    'Name' => $assigned_user,     // заполняет {{Name}}
    'ProbePairs' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $probe_pairs,
        ['ITEM_NAME' => 'Item', 'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class]
    ),
    'PairLNumber' => 'ProbePairs.Item.L_Number',
    'PairLName'   => 'ProbePairs.Item.L_Name',
    'PairLLab'    => 'ProbePairs.Item.L_Lab',
    'PairRNumber' => 'ProbePairs.Item.R_Number',
    'PairRName'   => 'ProbePairs.Item.R_Name',
    'PairRLab'    => 'ProbePairs.Item.R_Lab',

    'ProbeSingle' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $probe_single,
        ['ITEM_NAME' => 'Item', 'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class]
    ),
    'SingleNumber' => 'ProbeSingle.Item.S_Number',
    'SingleName'   => 'ProbeSingle.Item.S_Name',
    'SingleLab'    => 'ProbeSingle.Item.S_Lab',
]);

$result = $document->process();
$content = $document->getContent();

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="label prob all.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;
