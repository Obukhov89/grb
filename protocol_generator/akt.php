<?php
/**
 * Created by Makaralex92.
 * User: Алексей
 * Date: 25.04.2019
 * Time: 16:01
 */

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

$file = new Bitrix\Main\IO\File(__DIR__ . '/../KP.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

function num2word($n, $titles)
{
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
} 

//Запрашиваем данные
$ID = $_GET['ID']; //ID Заявки
$TZ_ID = $_GET['TZ_ID']; //ID Технического задания. В нём хранится ТЗ, результаты исследований и информация о пробах
$mater = $_GET['mater']; // ID материала, указанного в ТЗ
$probe_ID = $_GET['probe']; // ID пробы, указанного в пробах, заполненных на основе ТЗ

$deal = CCrmDeal::GetByID($ID); //Берём значение сделки
$company = CCrmCompany::GetByID($deal['COMPANY_ID']); //Берём значение компании
$companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch(); //Берём значение компании

$res_akt = $DB->Query("SELECT * FROM `ACT` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
if($res_akt)
	$res_akt = $DB->Query("UPDATE `ACT` SET `DATE`= NOW() WHERE `TZ_ID`=" . $TZ_ID);
else
	$res_akt = $DB->Query("INSERT INTO `ACT` (`DATE`, `TZ_ID`) VALUES (NOW(), " . $TZ_ID.")" );


if (CModule::IncludeModule('crm')) {
        $deal1 = new CCrmDeal;
        $arDealUpdate = array('STAGE_ID' => '2');
        $resUpd = $deal1->Update((int)$_GET['ID'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
    }


$comPhones = CCrmFieldMulti::GetList(array(), array('TYPE_ID' => 'PHONE', 'ELEMENT_ID' => $deal['COMPANY_ID']));
$comEmails = CCrmFieldMulti::GetList(array(), array('TYPE_ID' => 'EMAIL', 'ELEMENT_ID' => $deal['COMPANY_ID']));
while ($Phone = $comPhones->Fetch()) {
    $arMultiFields['PHONE'][] = $Phone['VALUE'];
};
while ($Email = $comEmails->Fetch()) {
    $arMultiFields['EMAIL'][] = $Email['VALUE'];
};

$arRequisite = new \Bitrix\Crm\EntityRequisite();
$dbRes = $arRequisite->getList(["filter" => ["ENTITY_ID" => $deal['COMPANY_ID'], "ENTITY_TYPE_ID" => CCrmOwnerType::Company, 'PRESET_ID' => 1]]);
$req = $dbRes->fetch();

$rs = $arRequisite->getList([
    "filter" => [
        "ENTITY_ID" => 1,
        "ENTITY_TYPE_ID" => CCrmOwnerType::Company,
    ]
]);
$rows = $rs->fetchAll();

foreach ($rows as $companyReq) {
    $params = [
        'filter' => [
            'ENTITY_ID' => $companyReq['ID'],
            'ENTITY_TYPE_ID' => CCrmOwnerType::Requisite]
    ];
    $bankReq = (new \Bitrix\Crm\EntityBankDetail)->getList($params)->fetch();
}

//echo "<pre>";
//var_dump($req);
//exit;

$address = Bitrix\Crm\EntityRequisite::getAddresses($deal['COMPANY_ID']);

global $DB;
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
$ba_tz = $res->Fetch();

//Формируем данные
$tz_arr = [];
$results = [];
foreach (unserialize($ba_tz['TZ']) as $item) {
    $probe = [];
    $PRICE = [];
    foreach ($item['gost'] as $items) {
        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $items);
        $gost = $ba_gost->Fetch();
        $probe[] = $gost['SPECIFICATION'];
        $PRICE[] = $gost['PRICE'];
    }
    $tz_arr[] = [
        'Name' => $item['mater'],
        'Spec' => implode(", ", $probe),
        'V' => $item['obiem'] . " " . num2word($item['obiem'], ["проба", "пробы", "проб"]),
    ];
    $results[] = [
        'Prod' => $item['mater'],
        'Spec' => implode(", ", $probe),
        'Price' => implode(", ", $PRICE),
    ];
}


//Заполняем данные
$document->normalizeContent();
$document->setValues([


    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $tz_arr,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'TableItemName' => 'Table.Item.Name',
    'TableItemSpec' => 'Table.Item.Spec',
    'TableItemV' => 'Table.Item.V',


    'Pres' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'PresItemProd' => 'Pres.Item.Prod',
    'PresItemSpec' => 'Pres.Item.Spec',
    'PresItemPrice' => 'Pres.Item.Price',
]);


$result = $document->process();
$content = $document->getContent();

$curdate = date("d-m-Y_H-i-s");
$newpath = "archive_akt/".$ID."/".$curdate.".docx";

function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );
   
    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );
   
    file_put_contents( $fullPath, $contents, $flags );
}


file_force_contents($newpath, $content);

// вывод непосредственно в браузер 
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="KП.docx"');
header('Cache-Control: max-age=0');
echo $content;
die;