<?php
/**
 * Created by PhpStorm.
 * User: Алексей
 * Date: 26.04.2019
 * Time: 9:38
 */


use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

$file = new Bitrix\Main\IO\File(__DIR__ . '/../Act.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());


$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
$sql = $res->Fetch();
$tz = unserialize($sql['TZ']);//[$_GET['mater']];
$probe = unserialize($sql['PROBE']);//[$_GET['mater']];
dump($probe);
$companys = CCrmCompany::GetList();
$deal = CCrmDeal::GetByID($sql['ID_Z']); 
$company = CCrmCompany::GetByID($deal['COMPANY_ID']);
$zakaz_man = $company['TITLE'];
$gosts = [];



$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';

$arr_num_request = !empty($num_request) ? explode(' ', $num_request) : '';

$str_num_request = !empty($arr_num_request[1]) ? $arr_num_request[1] : '';

$req = "заявка";



foreach ($tz[1]['gost'] as $item) {
    $res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
    $gost = $res->Fetch();
	//echo $item;
    $gosts[] = $gost['SPECIFICATION'];
}
$gosts = implode(', ', array_unique($gosts));
//var_dump($tz[1]['gost']);

$k=(int)$_GET['mater'];
$probe_arr = [];
for ($j=0; $j< count($probe[$k]['sh_number']); $j++)
{
 
   $probe_arr[$j] = [
         'Number' => $probe[$k]['sh_number'][$j],
         'Name' => $probe[$k]['number_probe'][$j],
      ];
   // }
}
//echo '------------------------------';
//echo $probe[1]['zakaz_man'];
//echo 'PROBE='.$probe['zakaz_man'].'___';
// while ($company = $companys->Fetch()) {
	// //echo $company['ID'].'   ';
    // if (!empty($probe[1]['zakaz_man']) && $probe[1]['zakaz_man'] == $company['ID'])
        // 
// }

echo '________'.$gosts;

$document->normalizeContent();
$document->setValues([
    'Number' => $probe[1]['number'],
	'DogovorNum' => getDogovor($sql['ID_Z']),
    'DatePost' => $sql['DATE_ACT'] ? date("d.m.Y", strtotime($sql['DATE_ACT'])) : date("d.m.Y"),
    'ZakazMan' => $zakaz_man,
    'mesto_data' => $probe[1]['mesto_data'],
    'probe_proizv' => $probe[1]['probe_proizv'],
    'SPECIFICATION' => $gosts,

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


header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Act priemki.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;
