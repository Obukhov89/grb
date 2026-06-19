<?php

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

include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";


$ID = !empty($_GET['ID']) ? $_GET['ID'] : '';



$TZ_ID = !empty($_GET['TZ_ID']) ? $_GET['TZ_ID'] : '';

$deal = CCrmDeal::GetByID($ID);
$company = CCrmCompany::GetByID($deal['COMPANY_ID']);
$ispolnitel = '';
$user = CUser::GetByID($deal['ASSIGNED_BY_ID'])->arResult[0];
$y_date = date("Y", strtotime($deal['DATE_CREATE']));
			if ($y_date != '2020') $y_date = "/".substr($y_date, 2);
			else $y_date = "/".$y_date;

$workpos = $user['WORK_POSITION'];
$ispolnitel = mb_substr($user['NAME'], 0, 1, 'UTF-8') . '. ' . (($user['SECOND_NAME']) ? mb_substr($user['SECOND_NAME'], 0, 1, 'UTF-8') . '. ' : '') . $user['LAST_NAME'];

global $DB;
$res = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID`=" . $TZ_ID);
$ba_tz = $res->Fetch();

$tz = unserialize($ba_tz['TZ']);
$labs = unserialize($ba_tz['LAB']);
$probe = unserialize($ba_tz['PROBE']);

// дедлайн по испытаниям
if(isset($ba_tz['DAY_TO_TEST']) && $ba_tz['DAY_TO_TEST'] != ''){
	$cur = date('d.m.Y'); //текущая дата
	switch ($ba_tz['type_of_day']) {
		case 'day':
			$typeDay = num_word($ba_tz['DAY_TO_TEST'], 'день', 'дня', 'дней'); break;
		case 'work_day':
			$typeDay = num_word($ba_tz['DAY_TO_TEST'], 'рабочий день', 'рабочих дня', 'рабочих дней'); break;
		case 'month':
			$typeDay = num_word($ba_tz['DAY_TO_TEST'], 'месяц', 'месяца', 'месяцев'); break;
		default: $typeDay = '';
	}

	$current_day = $ba_tz['DAY_TO_TEST'] . ' ' . $typeDay; //кол-во пришедших дней


	// $day_to_test = date('d.m.Y', strtotime($cur. " + ".$current_day." days")); // складывание
	$day_to_test_text = "Сроки проведения испытаний составляет " . $current_day . " с момента поступления проб (образцов) в ИЦ, подписания договора и оплаты испытаний.";
	$day_to_test_note = 'Сроки являются ориентировочными и могут быть скорректированы по согласованию сторон.';
}else{
	$day_to_test_text = '';
	$day_to_test_note = '';
}

// комментарии в тз
$val_comm_tz = (!empty($ba_tz['COMMENT_TZ']) ? $ba_tz['COMMENT_TZ'] : '');
$val_comm_kp = (!empty($ba_tz['COMMENT_KP']) ? $ba_tz['COMMENT_KP'] : '');

$accuracy=[];
$sum = number_format((int)$ba_tz['PRICE'], 2, ',', '');

$file = new Bitrix\Main\IO\File(__DIR__ . '/../TZ.docx');
$file2 = new Bitrix\Main\IO\File(__DIR__ . '/../TZ2.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());
$document2 = new Bitrix\DocumentGenerator\Body\Docx($file2->getContents());

foreach ($tz as $items) {
    foreach ($items['gost'] as $k => $item) {

        $ba_gost = $DB->Query("SELECT * FROM `ba_gost` WHERE `ID`=" . $item);
        $gost = $ba_gost->Fetch();
        $ress = '';
		$res1 = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $items['gost_new'][$k])->Fetch();
        $SPECIFICATION[] = $gost['SPECIFICATION'];
		$SPECIFICATION_TU[] = $res1['SPECIFICATION'];
        $GOST_PUNKT[] = $gost['GOST_PUNKT'];
		$GOST_PUNKT_TU[] = $res1['GOST_PUNKT'];
		$GOST_DOC[] = $res1['GOST'];//$items['gost_new'][$k];
 		$GOST_DOC_DESC_TU[] = $res1['NAME_GOST'];
		$GOST_DOC_DESC_MET[] = $gost['NAME_GOST'];
        if ($items['gost_new'][$k] != '-') $GOST_DOC_PLUS_DESC[] = $items['gost_new'][$k] . " - " . $items['gost_new_desc'][$k];
        $GOST2[] = $gost['GOST'];
 		$ch++;
    }
}

$i = 0;

foreach ($tz as $key => $arrDatum) {
	 $j=0;

		foreach ($arrDatum['gost'] as $k => $item){

			$res = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $item)->Fetch();
			$res_TU = $DB->Query("SELECT * FROM `ba_gost` WHERE ID=" . $arrDatum['gost_new'][$j])->Fetch();

			$prod_arr = explode(', ID',$arrDatum['mater']);
			$prod = $prod_arr[0];

			$price = number_format($arrDatum['price'][$k], 2, ',', '');
		//	dump($res_TU);
			$results[] = [
					'Object' => $prod,
					'Specification' => $res['SPECIFICATION'],
					'TU' => ($res_TU['GOST']) ? $res_TU['GOST']. ' ' . $res_TU['GOST_PUNKT']. ' | '.$res_TU['SPECIFICATION'] : '-',
					'GOST' => ($res['GOST']) ? $res['GOST'].' '.$res['GOST_PUNKT'] : '-',
					'Obiem' => $arrDatum['obiem'],
					'Price' => $price

				];
			$j++;
			}

	}



$req = new \Bitrix\Crm\EntityRequisite();
$rs = $req->getList([
    "filter" => [
        "ENTITY_ID" => $deal['COMPANY_ID'],
    ]
]);
$requis = $rs->fetch();

$assigned = CUser::GetByID($USER->GetID())->arResult[0];
$assigned_user = $assigned['NAME'].' '.$assigned['LAST_NAME'];

$dogovorNumber = explode(', ID', $ba_tz['DOGOVOR_NUM']);
$dogovorNumber = explode('Договор №', $dogovorNumber[0]);
$dogovor_num = getDogovor($ba_tz['ID_Z']);



$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';

$arr_num_request = !empty($num_request) ? explode(' ', $num_request) : '';

$str_num_request = !empty($arr_num_request[1]) ? $arr_num_request[1] : '';

$header = checkHeader(53);

$document->normalizeContent();

$document->setValues([

	'num_request' => !empty($str_num_request) ? 'по заявке ' . $str_num_request : '',

    'nDogovor' => $dogovor_num,
	'SUM' => $sum,
    'nZakazchik' => $deal['COMPANY_TITLE'],
	'header' => $header['short_name'],
    'innZakazchik' => $requis['RQ_INN'],
    'ogrnZakazchik' => $requis['RQ_OGRN'],
    'aZakazchik' => $requis['RQ_ACCOUNTANT'],
	'work_position' => $header['work_position'],
    'dProbe' => ($ba_tz['DATE_ACT'] ? date("d.m.Y", strtotime($ba_tz['DATE_ACT'])) : end($probe)['dat']),
    'mestoSboraProbe' => (string)end($probe)['mesto_data'],
    'oStroit' => $ba_tz['OBJECT'],
    'oIspit' => $number_probe,
    'nProbe' => $number,
	'nProbe_reg' => $ba_tz['ACT_NUM'].$y_date_act,//$y_date_act=='/2020' ? substr($numbers[0],0,-7).$y_date_act : substr($numbers[0],0,-5).$y_date_act ,
   	'ISP' => (count($verify) > 1 ? 'Исполнители' : 'Исполнитель'),
	'Point' => $point,
	'Phone' => 'тел: ' . $requis['RQ_PHONE'],
    'Email' => 'E-mail: ' . $requis['RQ_FIRST_NAME'],
    'DAY_TO_TEST_TEXT' => $day_to_test_text,
    'DAY_TO_TEST_NOTE' => $day_to_test_note,

    'COMMENT_TZ' => $val_comm_tz,

    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'TableItemObject' => 'Table.Item.Object',
    'TableItemSpecification' => 'Table.Item.Specification',
	'TableItemGOST' => 'Table.Item.GOST',
    'TableItemTU' => 'Table.Item.TU',
    'TableItemObiem' => 'Table.Item.Obiem',
	'TableItemPrice' => 'Table.Item.Price',

]
);

$result = $document->process();
$content = $document->getContent();

$date = date('Y-m-d H:i:s');

$dateTZ = date('d-m-Y_H-i-s', strtotime($date));

$curdate = "ТЗ_".$TZ_ID."_".$dateTZ;
$curdateEng = "TZ_".$TZ_ID."_".$dateTZ;
$newpath = "archive_tz/".$TZ_ID."/".$curdate.".docx";

$str_type = '';

$res_tz = $DB->Query("SELECT * FROM `TZ_DOC` WHERE `TZ_ID`=" . $TZ_ID)->Fetch();
if($res_tz['ID']) {
    $DB->Query("UPDATE `TZ_DOC` SET `DATE`= NOW(), `ACTUAL_VER` ='" . $curdate . "' WHERE `TZ_ID`=" . $TZ_ID);
    $str_type = 'Обновление приложения к договору (ТЗ)';
} else {
    $DB->Query("INSERT INTO `TZ_DOC` (`DATE`, `TZ_ID`, `ACTUAL_VER`) VALUES (NOW(), " . $TZ_ID . ", '" . $curdate . "')");
    $str_type = 'Формирование приложения к договору (ТЗ)';
}


$ID_P = $ba_tz['NUM_P'] ? $ba_tz['NUM_P'] : 'номер не присвоен';

$num_request = !empty($deal["TITLE"]) ? $deal["TITLE"] : '';

$res_history = $DB->Query("INSERT INTO `HISTORY`(`DATE`, `ASSIGNED`, `PROT_NUM`, `TZ_ID`, `USER_ID`, `TYPE`, `REQUEST`) VALUES ('" . $date . "','" . $assigned_user . "','" . $ID_P  . "','" . (int)$TZ_ID . "','" . $USER->GetID() . "', '" . $str_type . "', '" . $num_request . "')");



if ($ba_tz['ID'])
{
    $history = $DB->Query("SELECT * FROM `HISTORY` WHERE `TZ_ID`=" . (int)$TZ_ID . " ORDER BY `DATE` DESC LIMIT 1")->Fetch();
    $history_ass=$history['ASSIGNED'];
    $history_date=date("d.m.Y H:i:s", strtotime($history['DATE']));
    $history_date_null=date("d.m.Y H:i:s", strtotime('2019-01-01 05:00:00'));
    if($history_date<$history_date_null) $history_date='';


    $assignet_history = $history_ass . " " . $history_date;

    $update_assignet_ba_tz = $DB->Query("UPDATE `ba_tz` SET `USER_HISTORY` = '" . $assignet_history . "' WHERE `ID_Z` = " . (int)$ID);
}

$interimPath = "interim_archive_tz/".$TZ_ID."/".$curdateEng.".docx";
$outputFile = $curdate.".pdf";
$outputPath = "archive_tz/".$TZ_ID."/";

function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );

    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );

    file_put_contents( $fullPath, $contents, $flags );
}




$newDirectory = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive_tz/'.$TZ_ID;
if( !is_dir( $newDirectory ) ) {
	mkdir($newDirectory, 0777, true);
}

file_force_contents($interimPath, $content);

$fullPathDocx = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$interimPath;
$fullPathPDF = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$outputPath;

use  NcJoes\OfficeConverter\OfficeConverter;

$converter  =  new  OfficeConverter($fullPathDocx, $fullPathPDF);
$converter -> convertTo($outputFile);

$document2->normalizeContent();

$document2->setValues([

	'num_request' => !empty($str_num_request) ? '(Заявка ' . $str_num_request . ')' : '',

    'nDogovor' => $dogovor_num,
	'SUM' => $sum,
    'nZakazchik' => $deal['COMPANY_TITLE'],
    'innZakazchik' => $requis['RQ_INN'],
    'ogrnZakazchik' => $requis['RQ_OGRN'],
    'aZakazchik' => $requis['RQ_ACCOUNTANT'],
    'dProbe' => ($ba_tz['DATE_ACT'] ? date("d.m.Y", strtotime($ba_tz['DATE_ACT'])) : end($probe)['dat']),
    'mestoSboraProbe' => (string)end($probe)['mesto_data'],
    'oStroit' => $ba_tz['OBJECT'],
    'oIspit' => $number_probe,
    'nProbe' => $number,
	'nProbe_reg' => $ba_tz['ACT_NUM'].$y_date_act,//$y_date_act=='/2020' ? substr($numbers[0],0,-7).$y_date_act : substr($numbers[0],0,-5).$y_date_act ,
   	'ISP' => (count($verify) > 1 ? 'Исполнители' : 'Исполнитель'),
	'Point' => $point,
	'Phone' => 'тел: ' . $requis['RQ_PHONE'],
	'Email' => 'E-mail: ' . $requis['RQ_FIRST_NAME'],
    'DAY_TO_TEST_TEXT' => $day_to_test_text,
    'DAY_TO_TEST_NOTE' => $day_to_test_note,

    'COMMENT_TZ' => $val_comm_tz,

    'Table' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'TableItemObject' => 'Table.Item.Object',
    'TableItemSpecification' => 'Table.Item.Specification',
	'TableItemGOST' => 'Table.Item.GOST',
    'TableItemTU' => 'Table.Item.TU',
    'TableItemObiem' => 'Table.Item.Obiem',
	'TableItemPrice' => 'Table.Item.Price',

]

);


$result2 = $document2->process();
$content2 = $document2->getContent();




	header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
	header('Content-Disposition: attachment;filename="ТЗ № ' . $TZ_ID . ' от ' . date("d.m.Y") . '.docx"');
	header('Cache-Control: max-age=0');
	echo $content2;


die;
