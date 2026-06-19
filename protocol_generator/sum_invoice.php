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

// класс получения номера счёта
include ( $_SERVER['DOCUMENT_ROOT'] . "/Classes/numerator_acc.php" );
$numerAcc = new CAccountNumber();

//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
require_once $_SERVER['DOCUMENT_ROOT'] . '/protocol_generator/converter/index.php';
//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//

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
//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
$ID = !empty($_GET['ID']) ? $_GET['ID'] : '';
//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//

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
//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
if (!empty($_GET['SUM_INVOICE_PDF'])) {
	$file2 = new Bitrix\Main\IO\File(__DIR__ . '/../Account2.docx');
	$document2 = new Bitrix\DocumentGenerator\Body\Docx($file2->getContents());
}
//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//

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
//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
//ID договора
$id_comtract = !empty($_GET['ID_CONTRACT']) ? (int)$_GET['ID_CONTRACT'] : '';

//Дата формирования счета
$date = date('Y-m-d H:i:s');

if (!empty($_GET['SUM_INVOICE_PDF'])) {
//Email заказчика
$email = !empty($_GET['EMAIL']) ? $_GET['EMAIL'] : '';
}
//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//
//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
$res_acc = $DB->Query("SELECT * FROM `INVOICE` WHERE `CONTRACT_ID`=" . $_GET['ID_CONTRACT'])->Fetch();
$curdate = "Счет_".$res_acc['ID'].'_'.date("d-m-Y_H-i-s");

//if($res_acc)
//	$DB->Query("UPDATE `INVOICE` SET `DATE`= NOW(),`ACTUAL_VER` ='".$curdate."' WHERE `CONTRACT_ID`=" . $_GET['ID_CONTRACT']);
//else
	//$DB->Query("INSERT INTO `INVOICE` (`DATE`, `CONTRACT_ID`, `ACTUAL_VER`) VALUES (NOW(), " . $_GET['ID_CONTRACT'].", '".$curdate."')" );

//Создаем новый счет
$res = $DB->Query("INSERT INTO `INVOICE` (`DATE`, `CONTRACT_ID`) VALUES ('" . $date . "', " . $id_comtract . ")");
$inserted_id = $res->DB->db_Conn->insert_id;

//Находим последний добавленный счет
$result_select_invoice = $DB->Query("SELECT * FROM `INVOICE` ORDER BY ID DESC LIMIT 1")->Fetch();

//последний номер счета возвращает класс
$invNumber = $numerAcc->getValue();

//ID, номер счета
// $id_invoice = !empty($result_select_invoice['ID']) ? (int)$result_select_invoice['ID'] : '';
$id_invoice = !empty($result_select_invoice['ID']) ? (int)$invNumber : '';

//Создаем актуальную версию
$dateAcc = date('d-m-Y_H-i-s', strtotime($date));
//$curdate = "Счет_".$res_acc['ID'].'_'.date("d-m-Y_H-i-s");
$curdate = "Счет_" . $id_invoice . '_'.$dateAcc;
if (!empty($_GET['SUM_INVOICE_PDF'])) {
	$curdateEng = "Account_" . $id_invoice . '_'.$dateAcc;
}

if (!empty($id_invoice)) {
	//Добавляем в счет актуальную версию
	//$DB->Query("UPDATE `INVOICE` SET `ACTUAL_VER` ='" . $curdate . "' WHERE `ID`=" . $id_invoice); // по старому id
	$DB->Query("UPDATE `INVOICE` SET `ACTUAL_VER` ='" . $curdate . "', NUMBER = ".$id_invoice." WHERE `ID`=" . $inserted_id);
}

//Получаем из переданий строки все id сделок в массив
$array_deals = !empty($ID) ? explode('_', $ID) : '';

//Перебираем все полученные сделки, получаем id ТЗ и записывем в таблицу TZ_TO_INVOICE
foreach($array_deals as $id_deals) {
	$result_select_ba_tz = null;

	if (!empty($id_deals)) {
		$result_select_ba_tz = $DB->Query("SELECT * FROM `ba_tz` WHERE `ID_Z` = " . (int)$id_deals)->Fetch();
	}

	if (!empty($result_select_ba_tz['ID']) && !empty($id_invoice)) {
		$result_insert_deals_to_invoice2 = $DB->Query("INSERT INTO `TZ_TO_INVOICE` (`id_tz`, `id_invoice`) VALUES ('" . (int)$result_select_ba_tz['ID'] . "', '" . $id_invoice . "')");
	}

}

//$resInv = $DB->Query("SELECT * FROM `INVOICE` WHERE `CONTRACT_ID`=" . $_GET['ID_CONTRACT'])->Fetch();
if (!empty($id_invoice)) {
	$resInv = $DB->Query("SELECT * FROM `INVOICE` WHERE `ID`=" . $id_invoice)->Fetch();
}

$all_requests = '';

//перебираем масив с ID сделок
foreach($dealsId as $valueId) {
	$deal_select = CCrmDeal::GetByID($valueId);

	//Записываем все найденные номера заявок в строку
	$all_requests .= !empty($deal_select['TITLE']) ? $deal_select['TITLE'] : '';

	//добавляет запятую после каждой заявки кроме последней
	if (end($dealsId) != $valueId)
	$all_requests .= ', ';
}

$request_str = '';

//Если выбрали несколько заявок то склоняем во множественом числе
if (!empty($dealsId) && count($dealsId) > 1) {
	$request_str = ' Заявки ';
} else {
	$request_str = ' Заявка ';
}

//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//
$document->normalizeContent();

$document->setValues([
    'nDogovor' => "Договор №".$contractData['NUMBER'],
    'dDorovor' => " от ".ruDate($contractData['DATE']),
	//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
	'nRequest' => (!empty($all_requests) ? $request_str . $all_requests : ''), //поле Основание (в счете)
	//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//
    'nZakazchik' => $deal['COMPANY_TITLE'],
    'innZakazchik' => $requis['RQ_INN'],
    'ogrnZakazchik' => $requis['RQ_OGRN'],
    'aZakazchik' => $requis['RQ_ACCOUNTANT'],
 	'SumAll' => number_format($sumPrice, 2, ',', ''),
	'SumAllWord' => mb_ucfirst(num2str($sumPrice)),
	'AllName' => 1,
	// 'NUM_ACC' => $resInv['ID'],
	'NUM_ACC' => $invNumber,
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
//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
if (!empty($_GET['SUM_INVOICE_PDF'])) {
	$interimPath = "interim_archive_acc/".$ID."/".$curdateEng.".docx";
	$outputFile = $curdate.".pdf";
	$outputPath = "archive_acc/".$ID."/";
}
//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//

function file_force_contents( $fullPath, $contents, $flags = 0 ){
    $parts = explode( '/', $fullPath );
    array_pop( $parts );
    $dir = implode( '/', $parts );

    if( !is_dir( $dir ) )
        mkdir( $dir, 0777, true );

    file_put_contents( $fullPath, $contents, $flags );
}

//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
//if (empty($_GET['SUM_INVOICE_PDF'])) {
//	file_force_contents($newpath, $content);
//}
use  NcJoes\OfficeConverter\OfficeConverter;

//Если отправляем общий счет в pdf, то конвертируем в pdf документ, иначе если просто формируем счет то оставляем в pdf
if (!empty($_GET['SUM_INVOICE_PDF'])) {


$newDirectory = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/archive_acc/'.$ID;

	if( !is_dir( $newDirectory ) ) {
		mkdir($newDirectory, 0777, true);
	}
	file_force_contents($interimPath, $content);

	$fullPathDocx = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$interimPath;
	$fullPathPDF = $_SERVER['DOCUMENT_ROOT'] .'/protocol_generator/'.$outputPath;

	$converter  =  new  OfficeConverter($fullPathDocx, $fullPathPDF);
	$converter -> convertTo($outputFile); // генерирует pdf файл в том же каталоге, что и test-file.docx

	$document2->normalizeContent();

	$document2->setValues([
		'nDogovor' => "Договор №".$contractData['NUMBER'],
		'dDorovor' => " от ".ruDate($contractData['DATE']),
		'nZakazchik' => $deal['COMPANY_TITLE'],
		'innZakazchik' => $requis['RQ_INN'],
		'ogrnZakazchik' => $requis['RQ_OGRN'],
		'aZakazchik' => $requis['RQ_ACCOUNTANT'],
		'SumAll' => number_format($sumPrice, 2, ',', ''),
		'SumAllWord' => mb_ucfirst(num2str($sumPrice)),
		'AllName' => 1,
		// 'NUM_ACC' => $resInv['ID'],
		'NUM_ACC' => $invNumber,
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

	$result2 = $document2->process();
	$content2 = $document2->getContent();
} else {
	file_force_contents($newpath, $content);
}
//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//

//echo "ВЕДУТСЯ ТЕХНИЧЕСКИЕ РАБОТЫ! ПОПРОБУЙТЕ СФОРМИРОВАТЬ ПРОТОКОЛ ПОЗЖЕ!";

header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment;filename="Счет № ' .  $invNumber. ' от ' . date("d.m.Y") . '.docx"');
header('Cache-Control: max-age=0');
//-------------'Формирование общего счета в pdf и отправка' начало-----------------//
//Если сформировали общий счет то выводим $content, Если сформировать ощий счет и отправить клиенту в pdf, то перенапрвляем на страницу отправки писем
if (!empty($_GET['SUM_INVOICE_PDF'])) {
	//echo $content2;

	//Имя и Фамилия отправляющего письмо
	$name_isp = !empty($_GET['NAME']) ? $_GET['NAME'] : '';
	//$email = 'x-sania@mail.ru';
	header ('Location: /mail.php?ID_REQUESTS='.$ID.'&TYPE=9&EMAIL=' . $email . '&NAME=' . $name_isp . '&ID_CONTRACT='. $id_comtract);
} else {
	echo $content;
}
//-------------'Формирование общего счета в pdf и отправка' окончание-----------------//

die;
