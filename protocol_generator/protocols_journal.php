<?php
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

function pre($obj) {
    echo('<pre>');
    print_r($obj);
    echo('</pre>');
}
$year = !empty($_GET['year']) ? $_GET['year'] : date('Y');
$protYear = substr($year, -2);


$res = $DB->Query("SELECT PROTOCOLS.*, ba_tz.`COMPANY_TITLE`, ba_tz.`MATERIAL` FROM `PROTOCOLS` INNER JOIN `ba_tz` ON `PROTOCOLS`.ID_TZ = `ba_tz`.ID WHERE `DATE` BETWEEN '{$year}-01-01' AND '{$year}-12-31' ORDER BY `NUMBER`");

$arr_protocols = [];
$i = 0;
while ($row = $res->Fetch()) {
    $arr_protocols[$i] = [
      'Number' => $row['NUMBER'] . '/' . $protYear,
      'Date'  => date('d.m.Y', strtotime($row['DATE'])),
      'Company'  => $row['COMPANY_TITLE'],
      'Object'  => $row['MATERIAL'],
      'Assigned'  => $row['VERIFY_NAME_LASTNAME'],
    ];
    $i++;
}

$file = new Bitrix\Main\IO\File(__DIR__ . '/../protocols_journal.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$document->normalizeContent();
$document->setValues([
    'Prot' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $arr_protocols,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'ProtItemNumber' => 'Prot.Item.Number',
    'ProtItemDate' => 'Prot.Item.Date',
    'ProtItemCompany' => 'Prot.Item.Company',
    'ProtItemObject' => 'Prot.Item.Object',
    'ProtItemAssigned' => 'Prot.Item.Assigned'
]);

$result = $document->process();
$content = $document->getContent();

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Журнал протоколов.docx"');
header('Cache-Control: max-age=0');
echo $content;
