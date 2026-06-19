<?php
/**
 * Created by Makaralex92.
 * User: Алексей
 * Date: 07.05.2019
 * Time: 16:11
 */

use \Bitrix\Main\Loader;
use \Bitrix\DocumentGenerator;

require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php';
include $_SERVER["DOCUMENT_ROOT"]."/include/function.php";

Loader::includeModule('documentgenerator');
Loader::IncludeModule("crm");

if (!function_exists('mb_ucfirst') && extension_loaded('mbstring'))
{
	/**
	 * mb_ucfirst - преобразует первый символ в верхний регистр
	 * @param string $str - строка
	 * @param string $encoding - кодировка, по-умолчанию UTF-8
	 * @return string
	 */
	function mb_ucfirst($str, $encoding='UTF-8')
	{
		$str = mb_ereg_replace('^[\ ]+', '', $str);
		$str = mb_strtoupper(mb_substr($str, 0, 1, $encoding), $encoding).
			   mb_substr($str, 1, mb_strlen($str), $encoding);
		return $str;
	}
}

$dealsId = array();
$requestArr = array();
$tzArr = array();
$materArr= array();
$results = array();
$priceArr = array();

$dealsId = explode('_', $_GET['ID']);

$deal = CCrmDeal::GetByID($dealsId[0]); //Берём значение сделки
$company = CCrmCompany::GetByID($deal['COMPANY_ID']);

foreach($dealsId as $key => $id)
{
	$requestArr[$key] = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z`=" . $id)->Fetch();
	$tzArr[] = unserialize($requestArr[$key]['TZ']);
	$priceArr[] = (int)$requestArr[$key]['PRICE'];
}

foreach($tzArr as $key => $mater)
{
	
	foreach($mater as $num => $materName) 
	{
		$materArr[] = $materName['mater'];
	}
}
$strMater = "Лабораторные испытания материалов: ".implode(", ",$materArr);
$sumPrice = array_sum($priceArr);


$contractData = $DB->Query("SELECT * FROM `DOGOVOR` WHERE `ID`=" . (int)$_GET['ID_CONTRACT'])->Fetch();

$file = new Bitrix\Main\IO\File(__DIR__ . '/../Account.docx');
$document = new Bitrix\DocumentGenerator\Body\Docx($file->getContents());

$results[0] = [
	'Specification' => $strMater,
	'Price' => number_format($sumPrice, 2, ',', ''),
	'Sum' => number_format($sumPrice, 2, ',', ''),
	'Obiem' => 1,
	'NUM' => 1
];	

$req = new \Bitrix\Crm\EntityRequisite();
$rs = $req->getList([
    "filter" => [
        "ENTITY_ID" => $deal['COMPANY_ID'],
    ]
]);
$requis = $rs->fetch();

$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `CONTRACT_ID`=" . $_GET['ID_CONTRACT'])->Fetch();
$curdate = "Счет_".$res_acc['ID'].'_'.date("d-m-Y_H-i-s");

if($res_acc)
	$DB->Query("UPDATE `INVOICE` SET `DATE`= NOW(),`ACTUAL_VER` ='".$curdate."' WHERE `CONTRACT_ID`=" . $_GET['ID_CONTRACT']);
else
	$DB->Query("INSERT INTO `INVOICE` (`DATE`, `CONTRACT_ID`, `ACTUAL_VER`) VALUES (NOW(), " . $_GET['ID_CONTRACT'].", '".$curdate."')" );

$resInv = $DB->Query("SELECT * FROM `INVOICE` WHERE `CONTRACT_ID`=" . $_GET['ID_CONTRACT'])->Fetch();

$document->normalizeContent();

$document->setValues([
    'nDogovor' => "Договор №".$contractData['NUMBER'],
    'dDorovor' => " от ".ruDate($contractData['DATE']),
    'nZakazchik' => $deal['COMPANY_TITLE'],
    'innZakazchik' => $requis['RQ_INN'],
    'ogrnZakazchik' => $requis['RQ_OGRN'],
    'aZakazchik' => $requis['RQ_ACCOUNTANT'],
 	'SumAll' => number_format($sumPrice, 2, ',', ''),
	'SumAllWord' => mb_ucfirst(num2str($sumPrice)),
	'AllName' => 1,
	'NUM_ACC' => $resInv['ID'],
	'DATE_ACC' => date("d.m.Y"),
	'Results' => new DocumentGenerator\DataProvider\ArrayDataProvider(
        $results,
        [
            'ITEM_NAME' => 'Item',
            'ITEM_PROVIDER' => DocumentGenerator\DataProvider\HashDataProvider::class,
        ]
    ),
    'ResultsItemObject' => 'Results.Item.Object',
    'ResultsItemSpecification' => 'Results.Item.Specification',
	'ResultsItemSpecificationTU' => 'Results.Item.SpecificationTU',
    'ResultsItemPrice' => 'Results.Item.Price',
	'ResultsItemSum' => 'Results.Item.Sum',
	'ResultsItemNUM' => 'Results.Item.NUM',
    'ResultsItemObiem' => 'Results.Item.Obiem',
]
);

$result = $document->process();
$content = $document->getContent();
$newpath = "archive_acc/".$ID."/".$curdate.".docx";

function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );
   
    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );
   
    file_put_contents( $fullPath, $contents, $flags );
}

file_force_contents($newpath, $content);

//echo "ВЕДУТСЯ ТЕХНИЧЕСКИЕ РАБОТЫ! ПОПРОБУЙТЕ СФОРМИРОВАТЬ ПРОТОКОЛ ПОЗЖЕ!";

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Счет № ' .  $resInv['ID']. ' от ' . date("d.m.Y") . '.docx"');
header('Cache-Control: max-age=0');
echo $content; 


die;