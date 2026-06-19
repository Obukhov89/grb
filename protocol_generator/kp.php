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


require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';
/*require_once $_SERVER['DOCUMENT_ROOT'] . '/Classes/Rights.php';
if (!Rights::check($USER, basename(__FILE__, '.php'))) {
    $url = $_SERVER['HTTP_REFERER'] ?? 'http://172.16.255.2/requests_test.php';
    header('location:' . $url);
    exit();
}*/
Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

$file = new Bitrix\Main\IO\File(__DIR__ . '/../KP.docx');

$file2 = new Bitrix\Main\IO\File(__DIR__ . '/../KP2.docx');

$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$document2 = new Bitrix\DocumentGenerator\Body\Docx($file2->getContents());

function num2word($n, $titles)
{
    $cases = array(2, 0, 1, 1, 1, 2);
    return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
}




$ID = !empty($_GET['ID']) ? $_GET['ID'] : '';


$TZ_ID = !empty($_GET['TZ_ID']) ? $_GET['TZ_ID'] : '';

$mater = $_GET['mater'];
$probe_ID = $_GET['probe'];

$deal = CCrmDeal::GetByID($ID);

if ($deal['TYPE_ID'] == 'COMPLEX') $file = new Bitrix\Main\IO\File(__DIR__ . '/../KP_OSK.docx');

$company = CCrmCompany::GetByID($deal['COMPANY_ID']);
$companys = CCrmCompany::GetList([], ["ID" => $deal['COMPANY_ID']])->Fetch();



$date = date('Y-m-d H:i:s');

$dateKP = date('d-m-Y_H-i-s', strtotime($date));

$curdate = "КП_" . $dateKP;
$curdateEng = "KP_" . $dateKP;



$assigned = CUser::GetByID($USER->GetID())->arResult[0];

$assigned_name = !empty($assigned['NAME']) ? $assigned['NAME'] : '';

$assigned_lastname = !empty($assigned['LAST_NAME']) ? $assigned['LAST_NAME'] : '';

$assigned_user = $assigned_name . ' ' . $assigned_lastname;
global $DB;
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$TZ_ID);
$ba_tz = $res->Fetch();

$ID_P = $ba_tz['NUM_P'] ? $ba_tz['NUM_P'] : 'номер не присвоен';

$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';


$arr_num_request = !empty($num_request) ? explode(' ', $num_request) : '';

$str_num_request = !empty($arr_num_request[1]) ? $arr_num_request[1] : '';


$str_type = '';


$val_comm_tz = (!empty($ba_tz['COMMENT_TZ']) ? $ba_tz['COMMENT_TZ'] : '');
$val_comm_kp = (!empty($ba_tz['COMMENT_KP']) ? $ba_tz['COMMENT_KP'] : '');
$res_kp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
if ($res_kp) {
    $res_kp = $DB->Query("UPDATE `KP` SET `DATE`= NOW(), `ACTUAL_VER` ='" . $curdate . "' WHERE `TZ_ID`=" . $TZ_ID);
    $str_type = 'Обновление комерческого предложения';
} else {
    $res_kp = $DB->Query("INSERT INTO `KP` (`DATE`, `TZ_ID`, `ACTUAL_VER`) VALUES (NOW(), " . $TZ_ID . ", '" . $curdate . "')");
    $str_type = 'Формирование комерческого предложения';
}

$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$TZ_ID . "','" . $USER->GetID() . "', '" . $str_type . "', '" . $num_request . "')");



if ($ba_tz['ID']) {
    $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$TZ_ID . " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
    $history_ass=$history['ASSIGNED'];
    $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
    $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
    if($history_date<$history_date_null) $history_date='';


    $assignet_history = $history_ass . " " . $history_date;

    $update_assignet_ba_tz = $DB->Query("UPDATE `ba_tz` SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID_Z` = " . (int)$ID);
}
if (CModule::IncludeModule('crm')) {
        $deal1 = new CCrmDeal;
        $arDealUpdate = array('STAGE_ID' => 'PREPAYMENT_INVOICE', 'UF_CRM_1562503450' =>  $_SERVER["SERVER_NAME"] . "/protocol_generator/kp.php?ID=" . $ID . '&TZ_ID=' . $TZ_ID);
        $resUpd = $deal1->Update((int)$_GET['ID'],
            $arDealUpdate,
            true,
            true,
            array('DISABLE_USER_FIELD_CHECK' => true)
        );
    }



//var_dump("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('PREPAYMENT_INVOICE') . "' WHERE `ID_Z`=" . !empty($_GET['ID']) ? (int)$_GET['ID'] : '');die;
$update_ba_tz_stage_id = $DB->Query("UPDATE `ba_tz` SET `STAGE_ID`='" . $DB->ForSql('PREPAYMENT_INVOICE') . "' WHERE `ID_Z`=" . (int)$ID);


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

// global $DB;
// $res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . (int)$_GET['TZ_ID']);
// $ba_tz = $res->Fetch();
//$sum = $ba_tz['PRICE'];

$sumTotal = number_format((int)$ba_tz['PRICE'], 2, ',', '');



$tz_arr = [];
$results = [];
foreach (unserialize($ba_tz['TZ']) as $item) {
    $probe = [];
    $PRICE = [];
	$i=0;

    foreach ($item['gost'] as $items) {

        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $items);
        $gost = $ba_gost->Fetch();
        $probe = $gost['SPECIFICATION'];
		$gostName = $gost['GOST']. ' ' . $gost['GOST_PUNKT'];
        $PRICE[] = $gost['PRICE'];
		$sum = number_format((int)$item['price'][$i]*(int)$item['obiem'], 2, ',', '');

        $quantity = !empty($item['obiem']) ? $item['obiem'] : '';
        $gostItem = !empty($gost['GOST']) ? $gost['GOST'] : '';

		$prod = '';
		if(!$i)
		{
			$prod_arr = explode(', ID',$item['mater']);
			$prod = $prod_arr[0];
		}


		if($i == count($item['gost'])-1) {$line = '______________________';}

		$results[] = [
        'Prod' => $prod,
        'Spec' => $probe,
		'Gost' => $gostName,
        'Price' => ($item['price'][$i] ? number_format($item['price'][$i], 2, ',', '') : '-') ,
		'Sum' => $sum,

        'Quantity' => $quantity,
        'Gost' => $gostItem,
        'Line' => $line,


    ];
	$i++;
    }

    $tz_arr[] = [
        'Name' => $item['mater'],
       // 'Spec' => implode(", ", $probe),
       // 'V' => $item['obiem'] . " " . num2word($item['obiem'], ["проба", "пробы", "проб"]),
    ];

}

var_dump($results);
$res_kp = $DB->Query("SELECT * FROM `KP` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();

$document->normalizeContent();
$document->setValues([



	'SUM' => $sumTotal,

	'NUM_KP' => $res_kp['ID'],
	'NUM_DATE' => date('d.m.Y', strtotime($res_kp['DATE'])),

	'NUM_REQUEST' => !empty($str_num_request) ? 'ПО ЗАЯВКЕ ' . $str_num_request : '',

	'CLIENT_NAME' => $company['TITLE'],

    'COMMENT_KP' => $val_comm_kp,
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
	'PresItemGost' => 'Pres.Item.Gost',
	'PresItemSum' => 'Pres.Item.Sum',

    'PresItemQuantity' => 'Pres.Item.Quantity',
    'PresItemGost' => 'Pres.Item.Gost',
    'PresItemLine' => 'Pres.Item.Line',

]);


$result = $document->process();
$content = $document->getContent();


$newpath = "archive_kp/".$ID."/".$curdate.".docx";

$interimPath = "interim_archive_kp/".$ID."/".$curdateEng.".docx";
$outputFile = $curdate.".pdf";
$outputPath = "archive_kp/".$ID."/";

function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );

    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );

    file_put_contents( $fullPath, $contents, $flags );
}




$newDirectory = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive_kp/'.$ID;
if( !is_dir( $newDirectory ) ) {
	mkdir($newDirectory, 0777, true);
}

file_force_contents($interimPath, $content);

$fullPathDocx = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$interimPath;
$fullPathPDF = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$outputPath;


use  NcJoes\OfficeConverter\OfficeConverter;

$converter  =  new  OfficeConverter($fullPathDocx, $fullPathPDF);//, $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/');
$converter -> convertTo($outputFile); // генерирует pdf файл в том же каталоге, что и test-file.docx



$document2->normalizeContent();
$document2->setValues([
    'SUM' => $sumTotal,
    'NUM_KP' => $res_kp['ID'],
    'NUM_DATE' => date('d.m.Y', strtotime($res_kp['DATE'])),

	'NUM_REQUEST' => !empty($str_num_request) ? 'ПО ЗАЯВКЕ ' . $str_num_request : '',

    'CLIENT_NAME' => $company['TITLE'],

    'COMMENT_KP' => $val_comm_kp,
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
    'PresItemSum' => 'Pres.Item.Sum',
    'PresItemQuantity' => 'Pres.Item.Quantity',
    'PresItemGost' => 'Pres.Item.Gost',
    'PresItemLine' => 'Pres.Item.Line',
]);


$result2 = $document2->process();
$content2 = $document2->getContent();



header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="KП №'.$res_kp['ID'].' от '.date('d.m.Y', strtotime($res_kp['DATE'])).'.docx"');
header('Cache-Control: max-age=0');


echo $content2;

die;
