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

$res = $DB->Query("SELECT * FROM `REACT_SUPPLY` WHERE `id` = {$_GET['ID']}")->Fetch();
$res_rem = $DB->Query("SELECT * FROM `supply_rem` WHERE `id_supply` = {$res['id']}");

$result_supply = [];
$i = 0;
while ($row = $res_rem->Fetch()) {
    $result_supply[$i] = [
        'Date' => $row['date'],
        'Income' => !empty($row['income']) ? $row['income'] : '',
        'Exp' => !empty($row['expense']) ? $row['expense'] : ''
    ];
    $i++;
}

$file = new Bitrix\Main\IO\File(__DIR__ . '/../supple.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$document->normalizeContent();
$document->setValues([
    'numSupl' => $res['number'],
    'dateSupl' => date('d.m.Y', strtotime($res['date_create'])),
    'ndSupl' => $res['nd'],
    'qualSupl' => $res['quality'],
    'dateToSupl' => date('d.m.Y', strtotime($res['date_to'])),
    'Supl' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $result_supply,
        [
        'ITEM_NAME' => 'Item',
        'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'SuplItemDate' => 'Supl.Item.Date',
    'SuplItemIncome' => 'Supl.Item.Income',
    'SuplItemExp' => 'Supl.Item.Exp',
    'sumSupl' => $res['balance']
]);

$result = $document->process();
$content = $document->getContent();

// вывод непосредственно в браузер
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Отчет партии.docx"');
header('Cache-Control: max-age=0');
echo $content;