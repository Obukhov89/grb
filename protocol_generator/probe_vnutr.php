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

$file = new Bitrix\Main\IO\File(__DIR__ . '/../Act_vnutr.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

//Запрашиваем данные
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
$sql = $res->Fetch();
$tz = unserialize($sql['TZ']);//[$_GET['mater']];
$probe = unserialize($sql['PROBE']);//[$_GET['mater']];
//var_dump($probe);
$deal = CCrmDeal::GetByID((int)$_GET['ID']);
$y_date = date("Y", strtotime($deal['DATE_CREATE']));
$y_date = "/".substr($y_date, 2);

$companys = CCrmCompany::GetList();

$gosts = [];
foreach ($tz[1]['gost'] as $item) {
    $res = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
    $gost = $res->Fetch();
	//echo $item;
    $gosts[] = $gost['SPECIFICATION'];
}
$gosts = implode(', ', array_unique($gosts));
//var_dump($tz[1]['gost']);
//Формируем данные
$probe_arr = [];
for ($j=1; $j<= count($probe); $j++) {
  // for ($i = 1; $i <= 2; $i++) {  //count($probe['sh_number'][$j])
   // // $probe_arr[$j] = [
    // //    'Number' => $probe[]['number'].".".$i,
    // //    'Name' => $probe['number_probe'][$i - 1],
   // // ];
 
   $probe_arr[$j] = [
         'Number' => $probe[$j]['sh_number'][0],
         'Name' => $probe[$j]['number_probe'][0],
      ];
   // }
}
//echo '------------------------------';
//echo $probe[1]['zakaz_man'];
//echo 'PROBE='.$probe['zakaz_man'].'___';
while ($company = $companys->Fetch()) {
	//echo $company['ID'].'   ';
    if (!empty($probe[1]['zakaz_man']) && $probe[1]['zakaz_man'] == $company['ID'])
        $zakaz_man = $company['TITLE'];
}

echo '________'.$gosts;
//Заполняем данные
$document->normalizeContent();
$document->setValues([
    'Number' => $probe[1]['number'],
    'DatePost' => date("d.m.Y", strtotime($sql['DATE_ACT'])),
    'ZakazMan' => $zakaz_man,
    'mesto_data' => $probe[1]['mesto_data'],
    'probe_proizv' => $probe[1]['probe_proizv'],
    'SPECIFICATION' => $gosts,
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
header('Content-Disposition: attachment;filename="Act peredachi.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;